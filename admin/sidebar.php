<?php
// Page detection logic - remains unchanged
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        /* Professional Blue Palette */
        --primary-blue: #1e40af; 
        --dark-navy: #0f172a;    
        --sky-accent: #38bdf8;   
        --light-blue-bg: #f0f7ff; 
        
        --sidebar-width: 280px;
        --topbar-height: 75px;
    }

    * { box-sizing: border-box; }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #eef2ff;
        background-image: 
            radial-gradient(at 0% 0%, rgba(15, 18, 28, 0.1) 0px, transparent 40%), 
            radial-gradient(at 100% 0%, rgba(14, 26, 250, 0.1) 0px, transparent 40%), 
            radial-gradient(at 50% 100%, rgba(219, 234, 254, 1) 0px, transparent 50%);
        background-attachment: fixed;
        min-height: 100vh;
    }

    /* --- SIDEBAR DESIGN FIXED --- */
    .sidebar {
        width: var(--sidebar-width);
        background-color: var(--dark-navy); 
        background-image: 
            radial-gradient(at 0% 0%, rgba(28, 29, 31, 0.9) 0px, transparent 50%), 
            radial-gradient(at 100% 0%, rgb(7, 17, 214) 0px, transparent 50%), 
            radial-gradient(at 50% 100%, rgba(216, 224, 235, 0.2) 0px, transparent 30%);
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        color: white;
        display: flex;
        flex-direction: column;
        transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 10px 0 30px rgba(0,0,0,0.3);
        z-index: 1100;
    }

    .sidebar-brand { 
        height: var(--topbar-height);
        display: flex; align-items: center; padding: 0 25px;
        font-size: 20px; font-weight: 800;
        letter-spacing: 1px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        background: rgba(0,0,0,0.2);
    }

    .sidebar-brand i { color: var(--sky-accent); margin-right: 12px; }

    .sidebar-menu { 
        list-style: none; padding: 20px 15px; margin: 0; 
        flex-grow: 1; overflow-y: auto;
    }

    .sidebar-menu::-webkit-scrollbar { width: 4px; }
    .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    .sidebar-menu li { 
        padding: 13px 18px; cursor: pointer; display: flex; align-items: center; 
        transition: 0.3s; color: #94a3b8; font-size: 14px;
        margin-bottom: 4px; border-radius: 10px;
    }

    .sidebar-menu li:hover { 
        background: rgba(255,255,255,0.05); color: white; transform: translateX(5px);
    }

    .sidebar-menu li.active { 
        background: linear-gradient(135deg, var(--primary-blue), var(--sky-accent)); 
        color: white; font-weight: 600;
        box-shadow: 0 8px 15px rgba(30, 64, 175, 0.3);
    }

    .sidebar-menu i { margin-right: 12px; width: 22px; text-align: center; font-size: 17px; }

    .admin-panel-footer {
        padding: 20px; font-size: 11px; text-align: center;
        color: #475569; border-top: 1px solid rgba(255,255,255,0.05);
    }

    /* --- TOPBAR DESIGN --- */
    .topbar {
        position: fixed; top: 0; left: var(--sidebar-width); right: 0;
        height: var(--topbar-height);
        background: rgba(30, 36, 229, 0.9);
        backdrop-filter: blur(10px);
        display: flex; justify-content: space-between; align-items: center;
        padding: 0 35px; z-index: 1000;
        border-bottom: 2px solid var(--primary-blue); 
        transition: 0.4s;
    }

    .topbar h2 {
        font-size: 17px; color: white; font-weight: 700;
        display: flex; align-items: center; gap: 12px;
    }

    .user-area { display: flex; align-items: center; gap: 20px; }
    .user-name { 
        font-size: 13px; font-weight: 600; color: var(--primary-blue);
        background: #e0e7ff; padding: 8px 16px; border-radius: 50px;
    }

    .logout-btn-top {
        background: #ef4444; color: white !important; padding: 8px 16px;
        border-radius: 8px; text-decoration: none; font-size: 13px;
        font-weight: 700; transition: 0.3s;
    }

    .logout-btn-top:hover { background: #dc2626; transform: translateY(-1px); }

    /* --- RESPONSIVE LOGIC --- */
    #menu-icon { display: none; cursor: pointer; font-size: 20px; color: white; }

    @media (max-width: 1024px) {
        .sidebar { left: -100%; }
        .sidebar.active { left: 0; }
        .topbar { left: 0; }
        #menu-icon { display: block; }
        .main-content { margin-left: 0 !important; padding: 20px; }
        .user-name { display: none; }
    }

    .main-content {
        margin-left: var(--sidebar-width);
      
        transition: 0.4s;
    }
</style>

<div id="overlay" onclick="toggleSidebar()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1050; backdrop-filter: blur(3px);"></div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fa fa-graduation-cap"></i> <span>EXAM PORTAL</span>
    </div>

    <ul class="sidebar-menu">
        <li class="<?= ($current_page == 'dascboard.php') ? 'active' : ''; ?>" onclick="location.href='dascboard.php'">
            <i class="fa fa-th-large"></i> <span>Dashboard</span>
        </li>
        <li class="<?= ($current_page == 'mange_empolyee.php') ? 'active' : ''; ?>" onclick="location.href='mange_empolyee.php'">
            <i class="fa fa-user-tie"></i> <span>Manage Employees</span>
        </li>
        <li class="<?= ($current_page == 'manage_course.php') ? 'active' : ''; ?>" onclick="location.href='manage_course.php'">
            <i class="fa fa-book"></i> <span>Manage Courses</span>
        </li>
        <li class="<?= ($current_page == 'manage_exam.php') ? 'active' : ''; ?>" onclick="location.href='manage_exam.php'">
            <i class="fa fa-edit"></i> <span>Manage Exams</span>
        </li>
        <li class="<?= ($current_page == 'manage-centers.php') ? 'active' : ''; ?>" onclick="location.href='manage-centers.php'">
            <i class="fa fa-map-marker-alt"></i> <span>Manage Centres</span>
        </li>
        <li class="<?= ($current_page == 'manage-labs.php') ? 'active' : ''; ?>" onclick="location.href='manage-labs.php'">
            <i class="fa fa-microscope"></i> <span>Manage Labs</span>
        </li>
        <li class="<?= ($current_page == 'manage-student.php') ? 'active' : ''; ?>" onclick="location.href='manage-student.php'">
            <i class="fa fa-user-graduate"></i> <span>Manage Students</span>
        </li>
        <li class="<?= ($current_page == 'manage-allocation.php') ? 'active' : ''; ?>" onclick="location.href='manage-allocation.php'">
            <i class="fa fa-chair"></i> <span>Seat Allocation</span>
        </li>
        <li class="<?= ($current_page == 'view_questions.php') ? 'active' : ''; ?>" onclick="location.href='view_questions.php'">
            <i class="fa fa-question-circle"></i> <span>Manage Questions</span>
        </li>
        <li class="<?= ($current_page == 'view_results.php') ? 'active' : ''; ?>" onclick="location.href='view_results.php'">
            <i class="fa fa-chart-bar"></i> <span>View Results</span>
        </li>
        <li class="<?= ($current_page == 'admin_attendance.php') ? 'active' : ''; ?>" onclick="location.href='admin_attendance.php'">
            <i class="fa fa-fingerprint"></i> <span>Student Attendance</span>
        </li>
        <li class="<?= ($current_page == 'admin_inbox.php' || $current_page == 'replied_history.php') ? 'active' : ''; ?>" onclick="location.href='admin_inbox.php'">
            <i class="fa fa-inbox"></i> <span>Admin Inbox</span>
        </li>
        <li class="<?= ($current_page == 'feedback.php') ? 'active' : ''; ?>" onclick="location.href='feedback.php'">
            <i class="fa fa-comment-alt"></i> <span>User Feedback</span>
        </li>
        <li style="margin-top: 25px; color: #fda4af; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;" onclick="location.href='logout.php'">
            <i class="fa fa-power-off"></i> <span>Sign Out</span>
        </li>
    </ul>

    <div class="admin-panel-footer">
        HAME Institute © 2026
    </div>
</div>

<div class="topbar">
    <h2>
        <i class="fa fa-bars" id="menu-icon" onclick="toggleSidebar()"></i> 
        System Overview
    </h2>
    <div class="user-area">
        <span class="user-name"><i class="fa fa-user-circle"></i> Welcome, Admin</span>
        <a href="logout.php" class="logout-btn-top"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        sidebar.classList.toggle('active');
        overlay.style.display = sidebar.classList.contains('active') ? 'block' : 'none';
    }
</script>

<div class="main-content">
    </div>