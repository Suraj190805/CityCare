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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CommunityFix</title>
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
                <a href="dashboard.html" class="nav-btn active">
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
                    <a href="index.html" class="dropdown-item text-destructive">Log out</a>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <nav class="nav-mobile">
            <a href="dashboard.php" class="nav-mobile-item active">
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

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-wrapper">
            <!-- Welcome Section -->
            <div class="text-center section-header">
                <h1 class="page-title">Report a Community Issue</h1>
                <p class="page-subtitle">Help make our community better by reporting problems in your area</p>
            </div>

            <!-- Instructions Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <svg class="icon text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        Upload Guidelines
                    </h2>
                    <p class="card-description">Follow these simple guidelines for best results</p>
                </div>
                <div class="card-content">
                    <div class="alert-grid">
                        <div class="alert alert-primary">
                            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <div>
                                <h3 class="alert-title">What to Upload</h3>
                                <p class="alert-description">Clear photos of the issue from multiple angles. Include landmarks for location context.</p>
                            </div>
                        </div>
                        
                        <div class="alert alert-accent">
                            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <div>
                                <h3 class="alert-title">Image Requirements</h3>
                                <p class="alert-description">JPG, PNG, or WEBP format. Maximum 10MB per image. Good lighting recommended.</p>
                            </div>
                        </div>
                    </div>

                    <ul class="guidelines-list">
                        <li><span class="bullet">•</span> Take photos in good lighting conditions</li>
                        <li><span class="bullet">•</span> Show the full extent of the problem</li>
                        <li><span class="bullet">•</span> Include nearby landmarks or street signs for location reference</li>
                        <li><span class="bullet">•</span> Avoid blurry or unclear images</li>
                    </ul>
                </div>
            </div>

            <!-- Upload Area -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Upload Images</h2>
                    <p class="card-description">Drag and drop your images or click to browse</p>
                </div>
                <div class="card-content">
                    <div class="upload-area" id="uploadArea">
                        <input type="file" id="fileInput" multiple accept="image/*" hidden>
                        <div class="upload-content">
                            <div class="upload-icon-wrapper">
                                <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                            </div>
                            <p class="upload-title">Drop your images here</p>
                            <p class="upload-subtitle">or click to browse from your device</p>
                        </div>
                    </div>

                    <!-- Selected Files Preview -->
                    <div id="filesContainer" style="display: none;">
                        <div class="files-header">
                            <h3 class="files-title">Selected Images (<span id="fileCount">0</span>)</h3>
                            <button class="btn btn-outline btn-sm" id="clearAllBtn">Clear All</button>
                        </div>
                        
                        <div class="files-grid" id="filesGrid"></div>

                        <button class="btn btn-accent btn-lg btn-full" id="submitBtn">Submit Report</button>
                    </div>

                    <!-- Manual / Text Complaints block -->
                    <div class="manual-block-wrapper" style="margin-top: 1.5rem;">
                        <a href="manual-complaint.html" class="manual-card" id="manualCard">
                            <div class="manual-card-content">
                                <div class="manual-icon">
                                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="manual-title">Manual / Text Complaint</h3>
                                    <p class="manual-subtitle">Write a complaint instead of uploading images. Provide problem details, choose the department, and submit.</p>
                                </div>
                                <div class="manual-cta">
                                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transform: rotate(180deg);">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

    <script src="main.js"></script>
</body>
</html>
