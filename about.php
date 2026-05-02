<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | Online Exam Center</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --c1: #0d6efd;
            --c2: #6f42c1;
            --c3: #20c997;
            --grad: linear-gradient(45deg, var(--c1), var(--c2));
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            color: #333;
            overflow-x: hidden;
        }

        /* --- HERO SLIDER --- */
        #slider {
            position: relative;
        }

        .carousel-item {
            height: 70vh;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        /* Caption inside slider - Transparent and Overlaid */
        .slider-bottom-caption {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            z-index: 10;
            background: transparent !important;
            text-align: center;
            /* Text shadow taaki image ke upar readable rahe */
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.9); 
        }

        .slider-bottom-caption h1 {
            color: #ffffff !important;
            font-weight: 700;
            font-size: 2.5rem;
        }
        
        .slider-bottom-caption .lead {
            color: #ffffff !important;
            font-size: 1.2rem;
        }

        .slider-bottom-caption .text-muted {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        /* --- TITLES --- */
        .title {
            text-align: center;
            font-weight: 800;
            margin-bottom: 40px;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2.2rem;
            background-image: var(--grad);
        }

        /* --- FEATURE CARDS --- */
        .key-feature-card {
            background: #fff;
            padding: 25px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: 0.4s;
            height: 100%;
            border: 1px solid #f0f0f0;
        }

        .key-feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: var(--c1);
        }

        .key-feature-card img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .key-feature-card h3 { font-size: 1.15rem; font-weight: 700; margin-bottom: 10px; }
        .key-feature-card h4 { font-size: 0.85rem; color: #6c757d; font-weight: 400; line-height: 1.5; }

        /* --- ADVANCED FEATURES --- */
        .card-custom {
            border: 1px solid #eee;
            border-radius: 20px;
            transition: 0.3s;
            background: #fff;
            height: 100%;
        }

        .card-custom:hover {
            background: var(--grad);
            color: #fff;
            transform: scale(1.03);
        }

        .icon {
            font-size: 40px;
            color: var(--c1);
            margin-bottom: 15px;
            transition: 0.3s;
        }
        .card-custom:hover .icon { color: #fff; }

        /* --- TEAM SECTION --- */
        .team-card {
            border: none;
            border-radius: 20px;
            padding: 25px;
            transition: 0.4s;
            background: #fff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            text-align: center;
        }

        .team-img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 4px solid #f8f9fa;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .team-card:hover .team-img {
            border-color: var(--c1);
            transform: rotate(5deg);
        }

        /* --- CTA SECTION --- */
        .cta {
            background: var(--grad);
            color: #fff;
            padding: 50px 20px;
            border-radius: 25px;
            text-align: center;
        }

        .bottom-bar {
            background: #111;
            color: #ccc;
            padding: 20px;
            text-align: center;
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .carousel-item { height: 50vh; }
            .title { font-size: 1.7rem; }
            .slider-bottom-caption h1 { font-size: 1.5rem; }
        }
    </style>
</head>

<body>

<?php include "header.php"?>

<div id="slider" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active" style="background-image:url('image/77d01961-14e6-4b88-8bcf-b40599a78b67.png')"></div>
        <div class="carousel-item" style="background-image:url('image/594fa375-47d5-41fc-8339-697dc7234187.png')"></div>
        <div class="carousel-item" style="background-image:url('image/0b203d82-becd-4377-ad3b-63f71443b0c1.png')"></div>
    </div>

    <section class="slider-bottom-caption">
        <div class="container">
            <h1 class="fw-bold">Smart Online Exam System</h1>
            <p class="lead">Empowering Digital Education with Speed, Security & Transparency</p>
            <p class="text-muted small">AI Monitoring | Secure Login | Instant Results</p>
        </div>
    </section>

    <button class="carousel-control-prev" data-bs-target="#slider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" data-bs-target="#slider" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<section class="py-5">
    <div class="container">
        <h2 class="title">About System</h2>
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <p class="mb-4 text-muted" style="text-align: justify; line-height: 1.7; font-size: 1.05rem;">
                    This Online Exam Center Management System is designed to provide a seamless and efficient digital examination experience. 
                    It allows administrators to manage the entire system while students can easily participate in exams without any hassle.
                </p>
                <div class="row g-3">
                    <div class="col-6"><i class="bi bi-check-circle-fill text-primary me-2"></i> Full Admin Control</div>
                    <div class="col-6"><i class="bi bi-check-circle-fill text-primary me-2"></i> Easy Registration</div>
                    <div class="col-6"><i class="bi bi-check-circle-fill text-primary me-2"></i> Smooth Exam Process</div>
                    <div class="col-6"><i class="bi bi-check-circle-fill text-primary me-2"></i> Instant Results</div>
                    <div class="col-6"><i class="bi bi-check-circle-fill text-primary me-2"></i> Auto Attendance</div>
                    <div class="col-6"><i class="bi bi-check-circle-fill text-primary me-2"></i> One-Click Batch Results</div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="image/df5fefbd-4414-442c-b27c-28584ed3aa2a.png" class="img-fluid rounded-4 shadow-lg w-100" alt="About System">
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="title">Why Choose Our Exam Center?</h2>
        <p class="text-center mb-5 text-muted">We provide a reliable, secure, and high-performance environment.</p>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="key-feature-card">
                    <img src="image/ChatGPT Image Mar 16, 2026, 03_03_01 PM.png" alt="Secure">
                    <h3>Secure Platform</h3>
                    <h4>Ensuring data safety and privacy during every exam session.</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="key-feature-card">
                    <img src="image/c29a8562-e543-4186-9509-d632e64b0c9e.png" alt="Portal">
                    <h3>Student Portal</h3>
                    <h4>Secure and quick access for students to log in and start exams easily.</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="key-feature-card">
                    <img src="image/ChatGPT Image Mar 16, 2026, 03_03_18 PM.png" alt="Results">
                    <h3>Automated Results</h3>
                    <h4>Instant grading and detailed analysis for every participant.</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="key-feature-card">
                    <img src="image/ChatGPT Image Mar 16, 2026, 03_03_28 PM.png" alt="Scheduling">
                    <h3>Easy Scheduling</h3>
                    <h4>Schedule exams with flexibility and ease for all departments.</h4>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container text-center">
        <h2 class="title mb-5">Our Core Management Team</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="team-card h-100 p-3 shadow-sm border rounded">
                    <img src="image/nand.jpeg" class="team-img rounded-circle mb-3" alt="Nand Kishor" style="width:120px; height:120px; object-fit:cover;" onerror="this.src='https://via.placeholder.com/150'">
                    <h5 class="fw-bold mb-1">Nand Kishor</h5>
                    <p class="text-primary small fw-bold mb-2">Senior Web Developer & IT Head</p>
                    <span class="badge mb-3" style="background-color: #0d6efd; color: #fff;">JS ITI - Tech Wing</span>
                    <p class="text-muted small px-2"><strong>Architecting the digital backbone of JS ITI with technical innovation.</strong></p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="team-card h-100 p-3 shadow-sm border rounded">
                    <img src="image/neha.jpeg" class="team-img rounded-circle mb-3" alt="Neha Maurya" style="width:120px; height:120px; object-fit:cover;" onerror="this.src='https://via.placeholder.com/150'">
                    <h5 class="fw-bold mb-1">Neha Maurya</h5>
                    <p class="text-primary small fw-bold mb-2">Principal & Administrator</p>
                    <span class="badge mb-3" style="background-color: #0d6efd; color: #fff;">JS ITI - Admin</span>
                    <p class="text-muted small px-2"><strong>Specializing in the strategic operations and security of JS ITI.</strong></p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="team-card h-100 p-3 shadow-sm border rounded">
                    <img src="image/sneha.jpeg" class="team-img rounded-circle mb-3" alt="Sneha Kumari" style="width:120px; height:120px; object-fit:cover;" onerror="this.src='https://via.placeholder.com/150'">
                    <h5 class="fw-bold mb-1">Sneha Kumari</h5>
                    <p class="text-primary small fw-bold mb-2">Support & Invigilation</p>
                    <span class="badge mb-3" style="background-color: #0d6efd; color: #fff;">JS ITI - Support</span>
                    <p class="text-muted small px-2"><strong>Delivering seamless real-time technical support for all assessments.</strong></p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="bottom-bar mt-5">
    © 2026 Online Exam Center | Designed & Developed for Excellence
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>