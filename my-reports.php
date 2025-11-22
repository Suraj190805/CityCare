<?php include 'auth-protect.php'; ?>
<!-- rest of your HTML markup here -->

<?php
// auth-protect.php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: index.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>My Reports - CommunityFix</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container header-content">
            <a href="dashboard.html" class="logo-link">

                <span class="logo-text" style="font-size :40px;">CityCare</span>
            </a>

            <!-- Desktop Navigation -->
            <nav class="nav-desktop">
                <a href="dashboard.html" class="nav-btn">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Home
                </a>
                <a href="my-reports.html" class="nav-btn active">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    My Reports
                </a>
                <a href="support.html" class="nav-btn">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    Support / Help
                </a>
            </nav>

            <!-- User Profile Dropdown -->
            <div class="dropdown">
                <button class="btn-icon dropdown-trigger">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </button>
                <div class="dropdown-menu">
                    <div class="dropdown-label">My Account</div>
                    <div class="dropdown-separator"></div>
                    <a href="#" class="dropdown-item">Profile</a>
                    <a href="#" class="dropdown-item">Settings</a>
                    <div class="dropdown-separator"></div>
                    <a href="index.html" class="dropdown-item text-destructive">Log out</a>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <nav class="nav-mobile">
            <a href="dashboard.html" class="nav-mobile-item">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Home</span>
            </a>
            <a href="my-reports.html" class="nav-mobile-item active">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
                <span>My Reports</span>
            </a>
            <a href="support.html" class="nav-mobile-item">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span>Support</span>
            </a>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-wrapper">
            <div class="section-header">
                <h1 class="page-title">My Reports</h1>
                <p class="page-subtitle">View and track all your submitted community reports</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <svg class="icon text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        Report History
                    </h2>
                    <p class="card-description">Your previously submitted reports will appear here</p>
                </div>

                <!-- Dynamic reports container -->
                <div class="card-content" id="reportsContainer" style="min-height: 8rem;">
                    <!-- Reports will be inserted here by JS -->
                </div>
            </div>
        </div>
    </main>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

    <script>
        // Page-only script to render reports saved in localStorage
        (function () {
            const REPORTS_KEY = 'communityfix_reports';
            const container = document.getElementById('reportsContainer');

            function getStoredReports() {
                try {
                    const raw = localStorage.getItem(REPORTS_KEY);
                    return raw ? JSON.parse(raw) : [];
                } catch (err) {
                    console.error('Failed to read reports from localStorage', err);
                    return [];
                }
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

                const meta = document.createElement('div');
                meta.style.display = 'flex';
                meta.style.justifyContent = 'space-between';
                meta.style.alignItems = 'center';
                meta.style.gap = '1rem';

                const locationDiv = document.createElement('div');
                locationDiv.style.fontSize = '0.875rem';
                locationDiv.style.color = 'var(--muted-foreground)';
                locationDiv.innerHTML = `<strong>Location:</strong> ${escapeHtml(report.location)}`;

                meta.appendChild(locationDiv);

                wrap.appendChild(header);
                wrap.appendChild(meta);

                return wrap;
            }

            // small helper to avoid injecting raw HTML
            function escapeHtml(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function render() {
                const reports = getStoredReports();
                container.innerHTML = '';

                if (!reports || reports.length === 0) {
                    // show empty state
                    const emptyWrap = document.createElement('div');
                    emptyWrap.className = 'empty-state';
                    emptyWrap.innerHTML = `
                        <div class="empty-icon-wrapper">
                            <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                        </div>
                        <p class="empty-title">No reports yet</p>
                        <p class="empty-description">Once you submit your first community report, it will appear here for you to track its status and resolution.</p>
                    `;
                    container.appendChild(emptyWrap);
                    return;
                }

                // render each report
                const list = document.createElement('div');
                list.style.display = 'grid';
                list.style.gridTemplateColumns = '1fr';
                list.style.gap = '1rem';

                // If screen width >= 768px, show two columns using CSS media query is already in styles
                // but for simplicity we'll keep single column and rely on CSS for layout adjustments if needed.

                reports.forEach(r => {
                    const card = createReportCard(r);
                    list.appendChild(card);
                });

                container.appendChild(list);
            }

            // initial render
            render();

            // expose render on window so other pages (if adding deletion, etc.) can call it
            window.__CF_RENDER_REPORTS = render;
        })();
    </script>

    <script src="main.js"></script>
</body>
</html>
