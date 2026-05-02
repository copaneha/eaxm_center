<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Exam Centre Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Base Reset */
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
            overflow-x: hidden;
        }

        /* --- Hero Section --- */
        .hero {
            background: 
                        url("image/unnamed (1).jpg"); /* Added overlay for text readability */
            background-size: cover;
            background-position: center;
            min-height: 450px;
            display: flex;
            align-items: center;
            padding: 40px 5%;
            color: white;
            text-align: left;
        }

        .hero-content { max-width: 700px; }

        .hero h1 {
            font-size: clamp(28px, 6vw, 55px);
            margin: 0;
            line-height: 1.1;
            font-weight: 800;
        }

        .hero p {
            font-size: clamp(16px, 2vw, 20px);
            margin: 20px 0;
            opacity: 0.9;
        }

        .hero .get-started {
            background-color: #ff9800;
            color: white;
            padding: 15px 35px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .hero .get-started:hover { background-color: #e68a00; transform: scale(1.05); }

        /* --- Feature Section --- */
       /* Login Buttons */

     /* --- Main Content Area (The "i" class) --- */
.i {
    /* Base Color */

    
    /* Background Image - Education Pattern */
    background-image:  
                      url('image/adacbf05-3642-4542-8bc1-a5fb1c5a310a.png');
    
    background-size: cover;
   
    /* Isse background scroll nahi hoga, professional dikhega */
    
   
    text-align: center;
    position: relative;
}

/* Optional: Bottom Blue Shade like the image */
.i::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100px;
 
    pointer-events: none;
}


        /* --- Login Buttons (Same as Image) --- */
        .login-container {
            margin-bottom: 20px;
            margin-top:0px;
        }

        .login-btn {
            padding: 12px 40px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 18px;
            margin-top: 5px;
            margin: 10px;
            border: none;
            color: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: inline-block;
            text-decoration: none;
            transition: 0.3s;
        }

        .student-btn { background: linear-gradient(180deg, #f39c12 0%, #d35400 100%); }
        .admin-btn { background: linear-gradient(180deg, #2980b9 0%, #1a5276 100%); }
  .empoly-btn{ background: linear-gradient(180deg, #1426e8 0%, #7f17da 100%); }
        /* --- Top Feature Cards (With Vertical Dividers) --- */
        .main-features-row {
            border-bottom: 1px solid #e1e8ef;
            padding-bottom: 40px;
            margin-bottom: 20px;
        }

        .feature-box {
            padding: 20px;
            position: relative;
        }

        /* Vertical divider lines between cards (Desktop only) */
        @media (min-width: 768px) {
            .feature-box:not(:last-child)::after {
                content: "";
                position: absolute;
                right: 0;
                top: 20%;
                height: 60%;
                width: 1px;
                background-color: #d1d9e0;
                border-style: dashed;
            }
        }

        .feature-icon-lg {
            width: 250px;
            margin-bottom: 20px;
        }

        .feature-box h5 {
            color: #1a3a6d;
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .feature-box p {
            color: #555;
            font-size: 14px;
            line-height: 1.4;
            max-width: 200px;
            margin: 0 auto;
        }

        /* --- Bottom Key Features (Grid Layout) --- */
        .key-feature-card {
            background: white;
            border: 1px solid #eef2f6;
            border-radius: 12px;
            padding: 25px 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            transition: 0.3s ease;
            height: 100%;
        }

        .key-feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .key-feature-card img {
            width: 200px;
            margin-bottom: 15px;
        }
          .key-feature-card h3 {
            color: #1a3a6d;
            font-weight: 600;
            font-size: 24px;
            margin: 0;
        }

        .key-feature-card h4 {
            color: #060607;
            font-weight: 600;
            font-size: 17px;
            margin: 0;
        }

        .section-title {
            font-weight: 700;
            font-size: 40px;
            color: #1a3a6d;
            margin: 40px 0 30px;
            position: relative;
            display: inline-block;
        }

        .section-title::before, .section-title::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 60px;
            height: 1px;
            background: #d1d9e0;
        }
        .section-title::before { left: -70px; }
        .section-title::after { right: -70px; }

        .learn-btn {
            background:linear-gradient(180deg, #f39c12 0%, #d35400 100%);
            color: white;
            padding: 12px 60px;
            border-radius: 6px;
            border: none;
            font-weight: 700;
            margin-top: 50px;
            margin-bottom: 40px;
            box-shadow: 0 4px 10px rgba(43, 95, 184, 0.3);
        }

        /* --- Responsive Adjustments --- */
        @media (max-width: 768px) {
            .section-title::before, .section-title::after { display: none; }
            .feature-box:not(:last-child) { border-bottom: 1px dashed #d1d9e0; padding-bottom: 30px; margin-bottom: 20px; }
            .login-btn { width: 90%; }
        }
    /* Hero heading ka size mobile ke liye thoda chota karein */
    .hero h1 {
        font-size: 28px;
    }

        footer {
            background-color: #0b2e8c;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<?php include "header.php" ?>

<section class="hero">
    <div class="hero-content">
        <h1>Digital Exam Hub</h1>
        <p>A smart way to manage exam centres, room allocations, and student seating plans with real-time reporting.</p>
        <button class="get-started" onclick="window.location.href='add-student.php'">
    Get Started Now
</button>
    </div>
</section>
<div class="i">
    <div class="container">

        <div class="login-container">
            <a href="logi.php" class="login-btn student-btn">Student Login</a>
            <a href="admin/index.php" class="login-btn admin-btn">Admin / Proctor Login</a>
              <a href="employee/login.php" class="login-btn empoly-btn">Empolyee / Proctor Login</a>
        </div>

        <div class="row main-features-row g-0">
            <div class="col-md-4">
                <div class="feature-box">
                    <img src="image/ChatGPT Image Mar 16, 2026, 03_01_46 PM.png" class="feature-icon-lg" alt="Exams">
                    <h3>Online Exams</h3>
                    <h5>Easily create and manage exams for your courses</h5>
                </div>
            </div>

          <div class="col-md-4">
    <div class="feature-box text-center p-3">
        <img src="image/ChatGPT Image Mar 16, 2026, 03_03_01 PM.png" 
             class="feature-icon-lg mb-3" 
             alt="Automatic Attendance">

        <h3>Automatic Attendance</h3>
        <h5>Attendance is marked instantly when students click the attendance button</h5>
    </div>
</div>

            <div class="col-md-4">
                <div class="feature-box">
                    <img src="image/ChatGPT Image Mar 16, 2026, 03_02_11 PM.png" class="feature-icon-lg" alt="Results">
                    <h3>Result & Reports</h3>
                    <h5>Instant Results & Detailed Analysis</h5>
                </div>
            </div>
        </div>

        <h1 class="section-title">Our Key Features</h1>

        <div class="row g-4 justify-content-center">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="key-feature-card">
                    <img src="image/ChatGPT Image Mar 16, 2026, 03_03_01 PM.png">
                    <h3>Secure Platform</h3>
                      <h4>Ensuring data safety and privacy</h4>
                </div>
            </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="key-feature-card text-center p-3">
        <img src="image/c29a8562-e543-4186-9509-d632e64b0c9e.png" class="img-fluid mb-3">

        <h3>Student Login Portal</h3>
        <h4>Secure and quick access for students to log in and start exams easily</h4>
    </div>
</div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="key-feature-card">
                    <img src="image/ChatGPT Image Mar 16, 2026, 03_03_18 PM.png">
                    <h3>Automated Results</h3>
                    <h4>Instant grading and detailed analysis</h4>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="key-feature-card">
                    <img src="image/ChatGPT Image Mar 16, 2026, 03_03_28 PM.png">
                    <h3>Easy Scheduling</h3>
                    <h4>Schedule exams with flexibilit & ease</h4>
                </div>
            </div>
        </div>

        <div class="text-center">
          <button class="learn-btn" onclick="window.location.href='about.php'">
    Learn More
</button>
        </div>

    </div>
</div>
<footer>
    <p>© 2026 Online Exam Centre Management System. All Rights Reserved.</p>
    <p style="opacity: 0.6; font-size: 12px;">Developed for Education Efficiency</p>
</footer>

<script>
    const mobileMenu = document.getElementById('mobile-menu');
    const navList = document.getElementById('nav-list');

    if(mobileMenu) {
        mobileMenu.addEventListener('click', () => {
            navList.classList.toggle('active');
            const icon = mobileMenu.querySelector('i');
            if (navList.classList.contains('active')) {
                icon.classList.replace('fa-bars', 'fa-times');
            } else {
                icon.classList.replace('fa-times', 'fa-bars');
            }
        });
    }
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Exam Centre Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            margin: 0;
            background-color: #f0f4f8;
        }

        /* --- Main Content Container --- */
        
    </style>
</head>
<body>



</body>
</html>