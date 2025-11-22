/* main.js - updated to call PHP backend endpoints
   Save/replace: /mnt/data/main.js
   Backend endpoints expected:
     - register.php        (POST JSON)
     - login.php           (POST JSON)
     - manual_submit.php   (POST JSON)
     - upload.php          (POST FormData; field name images[] )
     - get_reports.php     (GET returns { success: true, reports: [...] })
*/

/* -------------------------
   Toast Notification Function
   ------------------------- */
function showToast(title, description) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.innerHTML = `
        <div class="toast-title">${title}</div>
        <div class="toast-description">${description}</div>
    `;
    toast.classList.add('show');

    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

/* -------------------------
   Local Storage Helpers (fallback / offline)
   ------------------------- */
const REPORTS_KEY = 'communityfix_reports';

function getStoredReports() {
    try {
        const raw = localStorage.getItem(REPORTS_KEY);
        return raw ? JSON.parse(raw) : [];
    } catch (err) {
        console.error('Failed to read reports from localStorage', err);
        return [];
    }
}

function saveReportToStorage(report) {
    try {
        const reports = getStoredReports();
        reports.unshift(report); // newest first
        localStorage.setItem(REPORTS_KEY, JSON.stringify(reports));
    } catch (err) {
        console.error('Failed to save report to localStorage', err);
    }
}

/* -------------------------
   Auth Page - Tab Switching
   ------------------------- */
if (document.querySelector('.tabs')) {
    const tabTriggers = document.querySelectorAll('.tab-trigger');
    const tabContents = document.querySelectorAll('.tab-content');

    tabTriggers.forEach(trigger => {
        trigger.addEventListener('click', () => {
            const tabName = trigger.getAttribute('data-tab');

            tabTriggers.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            trigger.classList.add('active');
            document.getElementById(tabName).classList.add('active');
        });
    });
}

/* -------------------------
   Auth Page - Login Form (AJAX)
   ------------------------- */
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = loginForm.querySelector('button[type="submit"]');
        if (btn) { btn.classList.add('loading'); btn.disabled = true; }

        const identifier = document.getElementById('login-email').value.trim(); // email or username
        const password = document.getElementById('login-password').value.trim();

        if (!identifier || !password) {
            showToast('Validation error', 'Please enter email/username and password');
            if (btn) { btn.classList.remove('loading'); btn.disabled = false; }
            return;
        }

        try {
            const res = await fetch('login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ identifier, password })
            });
            const json = await res.json();
            if (json.success) {
                showToast('Welcome', json.message || 'Login successful');
                // optional client-side store for UI
                if (json.user) sessionStorage.setItem('user', JSON.stringify(json.user));
                setTimeout(() => { window.location.href = 'dashboard.php'; }, 600);
            } else {
                showToast('Login failed', json.message || 'Invalid credentials');
            }
        } catch (err) {
            console.error('Login error', err);
            showToast('Network error', err.message || 'Could not reach server');
        } finally {
            if (btn) { btn.classList.remove('loading'); btn.disabled = false; }
        }
    });
}

/* -------------------------
   Auth Page - Register Form (AJAX)
   ------------------------- */
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = registerForm.querySelector('button[type="submit"]');
        if (btn) { btn.classList.add('loading'); btn.disabled = true; }

        const username = document.getElementById('register-username').value.trim();
        const email = document.getElementById('register-email').value.trim();
        const password = document.getElementById('register-password').value;

        if (!username || !email || !password) {
            showToast('Validation error', 'Please fill all fields');
            if (btn) { btn.classList.remove('loading'); btn.disabled = false; }
            return;
        }

        try {
            const res = await fetch('register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, email, password })
            });

            const json = await res.json();

            if (json.success) {
                showToast('Registered', json.message || 'Account created');
                setTimeout(() => { window.location.href = 'index.html'; }, 700);
            } else {
                showToast('Register failed', json.message || 'Could not create account');
            }
        } catch (err) {
            console.error('Register error', err);
            showToast('Network error', err.message || 'Could not reach server');
        } finally {
            if (btn) { btn.classList.remove('loading'); btn.disabled = false; }
        }
    });
}

/* -------------------------
   Dropdown Toggle
   ------------------------- */
const dropdownTriggers = document.querySelectorAll('.dropdown-trigger');
dropdownTriggers.forEach(trigger => {
    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        const dropdown = trigger.closest('.dropdown');
        dropdown.classList.toggle('active');
    });
});
document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown').forEach(dropdown => {
        dropdown.classList.remove('active');
    });
});

/* -------------------------
   Dashboard - File Upload Functionality
   ------------------------- */
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');
const filesContainer = document.getElementById('filesContainer');
const filesGrid = document.getElementById('filesGrid');
const fileCount = document.getElementById('fileCount');
const clearAllBtn = document.getElementById('clearAllBtn');
const submitBtn = document.getElementById('submitBtn');

let selectedFiles = [];

if (uploadArea && fileInput) {
    // Click to upload
    uploadArea.addEventListener('click', () => {
        fileInput.click();
    });

    // Drag and drop events
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragging');
    });

    uploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragging');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragging');

        const files = Array.from(e.dataTransfer.files);
        handleFiles(files);
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        handleFiles(files);
        fileInput.value = '';
    });

    // Clear all button
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', () => {
            selectedFiles = [];
            updateFilesDisplay();
        });
    }

    // Submit button -> uploads to backend (upload.php)
    if (submitBtn) {
        submitBtn.addEventListener('click', async () => {
            if (selectedFiles.length === 0) {
                showToast('No files selected', 'Please select at least one image to upload');
                return;
            }

            // prepare FormData
            const fd = new FormData();
            selectedFiles.forEach(f => fd.append('images[]', f));

            // UI
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            showToast('Uploading...', `Uploading ${selectedFiles.length} file(s)...`);

            try {
                const res = await fetch('upload.php', {
                    method: 'POST',
                    body: fd
                });

                const json = await res.json();

                if (json.success) {
                    // If backend returns uploaded items, optionally save to localStorage (nice for users)
                    if (Array.isArray(json.uploaded)) {
                        json.uploaded.forEach(u => {
                            // Create a report object compatible with local UI
                            const r = {
                                id: u.id || ('r_' + Date.now()),
                                uuid: u.uuid || null,
                                type: 'image',
                                problem: 'Image report',
                                department: 'Unknown',
                                rawDepartmentValue: '',
                                location: '',
                                image_url: u.image_url || ('/uploads/' + u.image_filename),
                                date: new Date().toISOString(),
                                status: 'Submitted'
                            };
                            saveReportToStorage(r);
                        });
                    }

                    showToast('Uploaded', `${(json.uploaded || []).length || selectedFiles.length} image(s) uploaded successfully`);
                    selectedFiles = [];
                    updateFilesDisplay();

                    // Optionally redirect to My Reports so user can see uploaded report
                    setTimeout(() => window.location.href = 'my-reports.php', 700);
                } else {
                    showToast('Upload failed', json.message || 'Server returned an error.');
                }
            } catch (err) {
                console.error(err);
                showToast('Network Error', err.message || 'Could not reach server.');
            } finally {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            }
        });
    }
}

function handleFiles(files) {
    const imageFiles = files.filter(file => file.type && file.type.startsWith('image/'));

    if (imageFiles.length !== files.length) {
        showToast('Invalid files detected', 'Only image files are allowed');
    }

    // Enforce per-file size limit: 10MB
    const tooLarge = imageFiles.some(f => f.size > 10 * 1024 * 1024);
    if (tooLarge) {
        showToast('File too large', 'One or more files exceed the 10MB limit');
        return;
    }

    // Merge selected files (avoid duplicates by name+size)
    const existingKeySet = new Set(selectedFiles.map(f => `${f.name}_${f.size}`));
    const newFiles = imageFiles.filter(f => !existingKeySet.has(`${f.name}_${f.size}`));
    selectedFiles = [...selectedFiles, ...newFiles];

    updateFilesDisplay();
}

function updateFilesDisplay() {
    if (!filesGrid || !filesContainer) return;

    if (selectedFiles.length === 0) {
        filesContainer.style.display = 'none';
        return;
    }

    filesContainer.style.display = 'block';
    fileCount.textContent = selectedFiles.length;

    filesGrid.innerHTML = '';

    selectedFiles.forEach((file, index) => {
        const fileURL = URL.createObjectURL(file);
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.innerHTML = `
            <img src="${fileURL}" alt="Preview ${index + 1}" class="file-image">
            <button class="file-remove" data-index="${index}" title="Remove">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            <div class="file-info">
                <p class="file-name">${file.name}</p>
            </div>
        `;

        filesGrid.appendChild(fileItem);

        const removeBtn = fileItem.querySelector('.file-remove');
        removeBtn.addEventListener('click', () => {
            selectedFiles.splice(index, 1);
            updateFilesDisplay();
        });
    });
}

/* -------------------------
   Support Page - Accordion
   ------------------------- */
const accordionTriggers = document.querySelectorAll('.accordion-trigger');
accordionTriggers.forEach(trigger => {
    trigger.addEventListener('click', () => {
        const item = trigger.closest('.accordion-item');
        const isActive = item.classList.contains('active');

        document.querySelectorAll('.accordion-item').forEach(i => {
            i.classList.remove('active');
        });

        if (!isActive) {
            item.classList.add('active');
        }
    });
});

/* -------------------------
   Manual Complaint Form Logic
   (sends JSON to manual_submit.php)
   ------------------------- */
const manualForm = document.getElementById('manualForm');
if (manualForm) {
    const departmentSelect = document.getElementById('department');
    const otherDeptGroup = document.getElementById('otherDeptGroup');
    const otherDeptInput = document.getElementById('otherDept');

    // Show/hide "other department" input when 'Others' selected
    if (departmentSelect) {
        departmentSelect.addEventListener('change', () => {
            if (departmentSelect.value === 'others') {
                otherDeptGroup.style.display = 'block';
                otherDeptInput.required = true;
            } else {
                otherDeptGroup.style.display = 'none';
                otherDeptInput.required = false;
                otherDeptInput.value = '';
            }
        });
    }

    manualForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = manualForm.querySelector('button[type="submit"]');
        btn.classList.add('loading');
        btn.disabled = true;

        const problem = document.getElementById('problem').value.trim();
        const department = departmentSelect ? departmentSelect.value : '';
        const otherDept = otherDeptInput ? otherDeptInput.value.trim() : '';
        const locationText = document.getElementById('locationText') ? document.getElementById('locationText').value.trim() : '';

        // Basic validation
        if (!problem) {
            showToast('Validation error', 'Please describe the problem');
            btn.classList.remove('loading');
            btn.disabled = false;
            return;
        }
        if (!department) {
            showToast('Validation error', 'Please choose a department');
            btn.classList.remove('loading');
            btn.disabled = false;
            return;
        }
        if (department === 'others' && !otherDept) {
            showToast('Validation error', 'Please specify the department under "Others"');
            btn.classList.remove('loading');
            btn.disabled = false;
            return;
        }
        if (!locationText) {
            showToast('Validation error', 'Please enter a location');
            btn.classList.remove('loading');
            btn.disabled = false;
            return;
        }

        // Build payload
        const payload = {
            problem: problem,
            department: department === 'others' ? otherDept : department,
            raw_department_value: department,
            location: locationText
        };

        try {
            const res = await fetch('manual_submit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const json = await res.json();

            if (json.success) {
                // Save a representation locally too (helps offline and immediate display)
                const rep = {
                    id: json.report && json.report.id ? json.report.id : ('rpt_' + Date.now()),
                    uuid: json.report && json.report.uuid ? json.report.uuid : null,
                    type: 'manual',
                    problem: json.report && json.report.problem ? json.report.problem : payload.problem,
                    department: json.report && json.report.department ? json.report.department : payload.department,
                    rawDepartmentValue: payload.raw_department_value,
                    location: json.report && json.report.location ? json.report.location : payload.location,
                    date: json.report && json.report.created_at ? json.report.created_at : new Date().toISOString(),
                    status: json.report && json.report.status ? json.report.status : 'Submitted'
                };
                saveReportToStorage(rep);

                showToast('Submitted', 'Your manual complaint was submitted!');
                setTimeout(() => window.location.href = 'my-reports.php', 700);
            } else {
                showToast('Error', json.message || 'Failed to submit complaint.');
            }
        } catch (err) {
            console.error(err);
            showToast('Network Error', err.message || 'Could not send complaint to server. Saved locally.');
            // fallback: save locally so user doesn't lose the data
            const fallback = {
                id: 'rpt_local_' + Date.now(),
                type: 'manual',
                problem,
                department: department === 'others' ? otherDept : department,
                rawDepartmentValue: payload.raw_department_value,
                location: locationText,
                date: new Date().toISOString(),
                status: 'Submitted (local)'
            };
            saveReportToStorage(fallback);
            setTimeout(() => window.location.href = 'my-reports.php', 700);
        } finally {
            btn.classList.remove('loading');
            btn.disabled = false;
        }
    });
}

/* -------------------------
   My Reports page - try network first, fallback to localStorage
   ------------------------- */
const reportsContainer = document.getElementById('reportsContainer');
if (reportsContainer) {
    (async function loadReports() {
        // Try network
        try {
            const res = await fetch('get_reports.php');
            const json = await res.json();
            if (json.success && Array.isArray(json.reports)) {
                renderReports(json.reports.map(r => {
                    // normalize server field names to what createReportCard expects
                    return {
                        id: r.id || null,
                        uuid: r.uuid || null,
                        type: r.type || (r.image_filename ? 'image' : 'manual'),
                        problem: r.problem || (r.type === 'image' ? 'Image report' : 'No description'),
                        department: r.department || '',
                        rawDepartmentValue: r.raw_department_value || '',
                        location: r.location || '',
                        image_url: r.image_url || (r.image_filename ? ('/uploads/' + r.image_filename) : null),
                        date: r.created_at || new Date().toISOString(),
                        status: r.status || 'Submitted'
                    };
                }));
                return;
            }
        } catch (err) {
            console.warn('Could not fetch reports from server, using local storage', err);
        }

        // fallback: localStorage
        const local = getStoredReports();
        renderReports(local);
    })();
}

function escapeHtml(str) {
    if (!str && str !== 0) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
}

function formatDate(isoStr) {
    try {
        const d = new Date(isoStr);
        return d.toLocaleString();
    } catch (e) {
        return isoStr;
    }
}

function createReportCard(report) {
    const wrap = document.createElement('div');
    wrap.className = 'report-card';
    wrap.style.border = '1px solid var(--border)';
    wrap.style.borderRadius = '0.5rem';
    wrap.style.padding = '1rem';
    wrap.style.marginBottom = '1rem';
    wrap.style.background = 'var(--card)';

    const header = document.createElement('div');
    header.style.display = 'flex';
    header.style.alignItems = 'center';
    header.style.justifyContent = 'space-between';
    header.style.marginBottom = '0.5rem';

    const left = document.createElement('div');
    left.innerHTML = `<div style="font-weight:700; margin-bottom:0.25rem;">${escapeHtml(report.problem)}</div>
                      <div style="font-size:0.875rem; color:var(--muted-foreground);">${escapeHtml(report.department)}</div>`;

    const right = document.createElement('div');
    right.style.textAlign = 'right';
    right.innerHTML = `<div style="font-weight:700; color: ${report.status === 'Submitted' ? 'var(--primary)' : 'var(--muted-foreground)'}">${escapeHtml(report.status)}</div>
                       <div style="font-size:0.75rem; color:var(--muted-foreground);">${formatDate(report.date)}</div>`;

    header.appendChild(left);
    header.appendChild(right);

    wrap.appendChild(header);

    // content area
    const content = document.createElement('div');
    content.style.display = 'flex';
    content.style.gap = '1rem';
    content.style.alignItems = 'flex-start';

    if (report.image_url) {
        const imgWrap = document.createElement('div');
        imgWrap.style.width = '7rem';
        imgWrap.style.flex = '0 0 7rem';
        imgWrap.innerHTML = `<img src="${escapeHtml(report.image_url)}" alt="report image" style="width:100%; height:100%; object-fit:cover; border-radius:0.4rem; border:1px solid var(--border)">`;
        content.appendChild(imgWrap);
    }

    const meta = document.createElement('div');
    meta.style.flex = '1';
    meta.innerHTML = `<div style="font-size:0.95rem; color:var(--foreground)">${escapeHtml(report.problem)}</div>
                      <div style="font-size:0.85rem; color:var(--muted-foreground); margin-top:0.5rem;">
                        <strong>Location:</strong> ${escapeHtml(report.location || '—')}<br>
                        <strong>Department:</strong> ${escapeHtml(report.department || report.rawDepartmentValue || '—')}
                      </div>`;

    content.appendChild(meta);
    wrap.appendChild(content);

    return wrap;
}

function renderReports(list) {
    if (!reportsContainer) return;
    reportsContainer.innerHTML = '';

    if (!Array.isArray(list) || list.length === 0) {
        reportsContainer.innerHTML = `<div class="empty-state">
            <div class="empty-icon-wrapper"><svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline></svg></div>
            <h3>No reports found</h3>
            <p class="empty-description">You haven't submitted any reports yet.</p>
        </div>`;
        return;
    }

    list.forEach(r => {
        const card = createReportCard(r);
        reportsContainer.appendChild(card);
    });
}

/* -------------------------
   Small utility: keep scripts safe if elements missing
   ------------------------- */
window.addEventListener('beforeunload', () => {
    // revoke object URLs created by createObjectURL to avoid memory leaks
    // (not strictly necessary here because they are ephemeral, but good practice)
    const imgs = document.querySelectorAll('img');
    imgs.forEach(img => {
        try {
            URL.revokeObjectURL(img.src);
        } catch (e) {}
    });
});
