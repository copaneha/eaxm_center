<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            /* 3-Color Gradient Palette */
            --gradient-1: #1e3a8a; /* Deep Blue */
            --gradient-2: #3b82f6; /* Bright Blue */
            --gradient-3: #1e1b4b; /* Dark Navy */
            --sidebar-width: 280px;
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Nav Toggle Hidden */
        #nav-toggle { display: none; }

        /* Sidebar Styling */
        .sidebar { 
            width: var(--sidebar-width); 
            /* Premium 3-Color Gradient */
            background: linear-gradient(135deg, var(--gradient-3) 0%, var(--gradient-1) 50%, var(--gradient-2) 100%); 
            height: 100vh; 
            position: fixed; 
            left: 0; 
            top: 0; 
            color: white; 
            z-index: 1000; 
            transition: var(--transition);
            box-shadow: 4px 0 15px rgba(0,0,0,0.2);
            overflow-y: auto;
        }

        .sidebar-header { 
            padding: 40px 20px; 
            text-align: center; 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
        }

        .logo-icon {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            width: 55px; height: 55px; 
            border-radius: 16px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0 auto;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        }

        .sidebar-menu { list-style: none; padding: 25px 15px; }
        .sidebar-menu li { margin-bottom: 8px; }

        .sidebar-menu a { 
            color: rgba(255,255,255,0.7); 
            text-decoration: none; 
            display: flex; 
            align-items: center; 
            gap: 15px; 
            padding: 14px 20px; 
            border-radius: 14px; 
            font-weight: 500; 
            transition: var(--transition);
            position: relative;
        }

        /* Hover & Active Effects */
        .sidebar-menu a:hover, 
        .sidebar-menu a.active { 
            background: rgba(255, 255, 255, 0.15); 
            color: #fff; 
            backdrop-filter: blur(5px);
            transform: translateX(5px);
        }

        .sidebar-menu a.active::before {
            content: '';
            position: absolute;
            left: 0;
            width: 4px;
            height: 20px;
            background: #60a5fa;
            border-radius: 0 4px 4px 0;
        }

        .sidebar-menu i { font-size: 1.2rem; width: 25px; text-align: center; }

        /* Logout Special Style */
        .logout-link { margin-top: 40px !important; }
        .logout-link a { color: #fca5a5 !important; background: rgba(239, 68, 68, 0.1) !important; }
        .logout-link a:hover { background: rgba(239, 68, 68, 0.2) !important; color: #fee2e2 !important; }

        /* --- RESPONSIVE LOGIC --- */

        /* Mobile Menu Button (Hamburger) */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1100;
            background: var(--gradient-1);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        @media (max-width: 1024px) {
            .sidebar {
                left: -100%; /* Hide sidebar on mobile */
            }
            .mobile-toggle {
                display: flex; /* Show hamburger on mobile */
            }
            
            /* Toggle Sidebar when checkbox is checked */
            #nav-toggle:checked ~ .sidebar {
                left: 0;
            }

            /* Overlay effect when menu is open */
            #nav-toggle:checked ~ .body-overlay {
                display: block;
            }
        }

        .body-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            backdrop-filter: blur(3px);
        }

    </style>
</head>
<body>

    <label for="nav-toggle" class="mobile-toggle">
        <i class="fas fa-bars"></i>
    </label>

    <input type="checkbox" id="nav-toggle">
    
    <label for="nav-toggle" class="body-overlay"></label>

    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-icon">
                <i class="fas fa-shield-alt" style="color: #fff; font-size: 24px;"></i>
            </div>
            <h4 style="margin-top:18px; font-weight: 800; letter-spacing: 2px; font-size: 1.1rem;">STAFF PORTAL</h4>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="employee_dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'employee_dashboard.php') ? 'active' : ''; ?>">
                    <i class="fas fa-th-large"></i> <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="my_duties.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'my_duties.php') ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list"></i> <span>My Duties</span>
                </a>
            </li>
            <li>
                <a href="mark_attendance.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'mark_attendance.php') ? 'active' : ''; ?>">
                    <i class="fas fa-user-check"></i> <span>Attendance</span>
                </a>
            </li>
            <li>
                <a href="profile.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">
                    <i class="fas fa-fingerprint"></i> <span>Profile Settings</span>
                </a>
            </li>
            
            <li class="logout-link">
                <a href="logout.php">
                    <i class="fas fa-power-off"></i> <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

</body>
</html>