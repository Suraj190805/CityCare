<?php include 'auth-protect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Complaint - CommunityFix</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header (same pattern) -->
    <header class="header">
        <div class="container header-content">
            <a href="dashboard.php" class="logo-link">
                <span class="logo-text" style="font-size:40px;">CityCare</span>
            </a>

            <!-- Desktop Navigation -->
            <nav class="nav-desktop">
                <a href="dashboard.php" class="nav-btn">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Home
                </a>
                <a href="my-reports.php" class="nav-btn">
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
                    <!-- logout: JS will call logout.php, fallback points to index.html -->
                    <a href="index.html" id="logoutLink" class="dropdown-item text-destructive">Log out</a>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <nav class="nav-mobile">
            <a href="dashboard.php" class="nav-mobile-item">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Home</span>
            </a>
            <a href="my-reports.php" class="nav-mobile-item">
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

    <!-- Main -->
    <main class="main-content">
        <div class="content-wrapper">
            <div class="text-center section-header">
                <h1 class="page-title">Manual / Text Complaint</h1>
                <p class="page-subtitle">Describe the issue and choose the appropriate department</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Submit a Manual Complaint</h2>
                    <p class="card-description">Fill in the information below and submit your complaint</p>
                </div>
                <div class="card-content">
                    <!-- added action + method and name attributes to inputs for server fallback -->
                    <form id="manualForm" class="form" action="manual_submit.php" method="post">
                        <div class="form-group">
                            <label for="problem" class="label">Problem Description</label>
                            <textarea id="problem" name="problem" class="input" placeholder="Describe the problem in detail" rows="5" required style="resize: vertical;"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="department" class="label">Department</label>
                            <select id="department" name="department" class="input" required>
                                <option value="" disabled selected>Select department</option>
                                <option value="road">Road Dept</option>
                                <option value="electrical">Electrical Dept</option>
                                <option value="garbage">Garbage Dept</option>
                                <option value="streetlight">Street Light Dept</option>
                                <option value="water">Water Dept</option>
                                <option value="sanitation">Sanitation Dept</option>
                                <option value="others">Others</option>
                            </select>
                        </div>

                        <div class="form-group" id="otherDeptGroup" style="display:none;">
                            <label for="otherDept" class="label">If Others, specify department</label>
                            <input type="text" id="otherDept" name="otherDept" class="input" placeholder="Specify department">
                        </div>

                        <div class="form-group">
                            <label for="locationText" class="label">Location</label>
                            <input type="text" id="locationText" name="location" class="input" placeholder="Enter location (street, landmark, city)" required>
                        </div>

                        <button type="submit" class="btn btn-accent btn-lg btn-full">
                            <span class="btn-text">Submit Complaint</span>
                            <span class="btn-loading" style="display:none;">Submitting...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

    <script src="main.js"></script>

    <script>
    // Small inline helpers for this page (keeps behavior consistent with main.js)
    (function(){
      const department = document.getElementById('department');
      const otherGroup = document.getElementById('otherDeptGroup');
      const otherInput = document.getElementById('otherDept');
      const cancelBtn = document.getElementById('cancelBtn');
      const logoutLink = document.getElementById('logoutLink');

      if (department) {
        department.addEventListener('change', () => {
          if (department.value === 'others') {
            otherGroup.style.display = 'block';
            otherInput.required = true;
          } else {
            otherGroup.style.display = 'none';
            otherInput.required = false;
            otherInput.value = '';
          }
        });
      }

      // optional cancel button behavior (if present elsewhere)
      if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
          window.location.href = 'dashboard.php';
        });
      }

      // logout: call logout.php (POST) and redirect to login page
      if (logoutLink) {
        logoutLink.addEventListener('click', async (e) => {
          e.preventDefault();
          try {
            await fetch('logout.php', { method: 'POST' });
          } catch (err) {
            // ignore network errors
          } finally {
            window.location.href = 'index.html';
          }
        });
      }
    })();
    </script>
</body>
</html>
