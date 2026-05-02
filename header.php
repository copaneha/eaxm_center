<?php 
// Yeh line detect karti hai ki abhi kaun sa page open hai
$current_page = basename($_SERVER['PHP_SELF']); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive ExamCentre Header</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Base Reset */
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0; padding: 0;
            background-color: #f0f4f8;
        }

        /* --- Navigation Header --- */
        header {
            background-color: #0e4af0;
            color: white;
            padding: 0 5%;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        header .logo {
            font-size: 22px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Desktop Navigation */
        header nav {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        header nav a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        /* Hover Effect */
        header nav a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        /* --- ACTIVE CLASS (Jis page pe user hai wo aisa dikhega) --- */
        header nav a.active {
            background: #ce1717; /* Red background for active page */
            color: white !important;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        header nav .login-btn {
            background-color: #ff9800;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        header nav .login-btn:hover {
            background-color: #e68a00;
            transform: translateY(-2px);
        }

        .menu-toggle {
            display: none;
            font-size: 24px;
            cursor: pointer;
        }

        /* --- Responsive Design --- */
        @media (max-width: 1024px) {
            .menu-toggle { display: block; }

            header nav {
                position: absolute;
                top: 70px;
                left: -100%;
                flex-direction: column;
                background-color: #0c3dc5;
                width: 100%;
                height: calc(100vh - 70px);
                padding: 40px 0;
                transition: 0.4s ease-in-out;
            }

            header nav.active { left: 0; }

            header nav a {
                font-size: 18px;
                padding: 20px;
                width: 80%;
                text-align: center;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }
            
            /* Mobile mein active link thoda alag dikhe */
            header nav a.active {
                background: rgba(255,255,255,0.2);
                border-bottom: 2px solid #ff9800;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <i class="fas fa-user-graduate"></i> ExamHub
    </div>
    
    <div class="menu-toggle" id="mobile-menu">
        <i class="fas fa-bars"></i>
    </div>

    <nav id="nav-list">
        <a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
        <a href="about.php" class="<?= ($current_page == 'about.php') ? 'active' : ''; ?>">About</a>
        <a href="admin/index.php" class="<?= (strpos($_SERVER['PHP_SELF'], 'admin/') !== false) ? 'active' : ''; ?>">Admin Panel</a>
        <a href="employee/login.php" class="<?= (strpos($_SERVER['PHP_SELF'], 'employee/') !== false) ? 'active' : ''; ?>">Employee Panel</a>
        <a href="add-student.php" class="<?= ($current_page == 'add-student.php') ? 'active' : ''; ?>">Student Zone</a>
        <a href="contact.php" class="<?= ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a>
        <a href="logi.php" class="login-btn <?= ($current_page == 'login.php') ? 'active' : ''; ?>">Student Login</a>
    </nav>
</header>

<script>
    const mobileMenu = document.getElementById('mobile-menu');
    const navList = document.getElementById('nav-list');
    const menuIcon = mobileMenu.querySelector('i');

    mobileMenu.addEventListener('click', () => {
        navList.classList.toggle('active');
        if (navList.classList.contains('active')) {
            menuIcon.classList.replace('fa-bars', 'fa-times');
            document.body.style.overflow = 'hidden';
        } else {
            menuIcon.classList.replace('fa-times', 'fa-bars');
            document.body.style.overflow = 'auto';
        }
    });
</script>

</body>
</html>