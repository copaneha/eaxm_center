<?php
include("../config.php"); 
include("sidebar.php"); 

$status = "";

if(isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $fees = mysqli_real_escape_string($conn, $_POST['fees']);

    $query = "INSERT INTO courses (course_name, course_description, duration, fees) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $name, $desc, $duration, $fees);

    if($stmt->execute()) {
        $status = "success";
    } else {
        $status = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --secondary-bg: #f1f5f9;
            --input-focus: #8b5cf6;
        }

        body { 
            background: #f0f2f5; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }

        .content-wrapper {
            padding: 40px 20px;
        }

        @media (min-width: 992px) {
            .content-wrapper { margin-left: 280px; }
        }

        .card-custom { 
            border: none; 
            border-radius: 30px; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.05); 
            background: #ffffff;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        /* Top Color Bar */
        .card-custom::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: var(--primary-gradient);
        }

        .section-title { 
            font-weight: 800; 
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.75rem;
        }

        /* Animated Icon */
        .header-icon {
            background: #f3e8ff;
            color: #a855f7;
            width: 50px; height: 50px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 15px;
            font-size: 1.5rem;
        }

        /* Styled Labels & Inputs */
        .form-label {
            font-size: 0.9rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 10px;
            display: flex; align-items: center; gap: 8px;
        }

        .form-label i { color: #a855f7; font-size: 0.8rem; }

        .form-control {
            border: 2px solid #edf2f7;
            border-radius: 16px;
            padding: 14px;
            background-color: #f8fafc;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #a855f7;
            background-color: #fff;
            box-shadow: 0 10px 20px rgba(168, 85, 247, 0.1);
        }

        /* Colored Input Groups */
        .input-duration:focus { border-color: #3b82f6; box-shadow: 0 10px 20px rgba(59, 130, 246, 0.1); }
        .input-fees:focus { border-color: #10b981; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.1); }

        /* Button Styling */
        .btn-submit { 
            background: var(--primary-gradient);
            border: none; 
            padding: 16px; 
            font-weight: 700; 
            border-radius: 18px;
            color: white;
            box-shadow: 0 10px 25px rgba(168, 85, 247, 0.3);
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-submit:hover {
            transform: scale(1.02);
            box-shadow: 0 15px 30px rgba(168, 85, 247, 0.4);
            color: white;
        }

        .view-link {
            color: #6366f1;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: 0.3s;
        }
        .view-link:hover { color: #a855f7; }

    </style>
</head>
<body>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">
                
                <div class="card-custom">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="header-icon"><i class="fas fa-plus"></i></div>
                            <div>
                                <h4 class="section-title m-0">Add New Course</h4>
                                <p class="text-muted m-0">Fill in the information to launch a new program</p>
                            </div>
                        </div>
                        <a href="manage_course.php" class="view-link">
                            <i class="fas fa-arrow-right me-1"></i> Go to Management
                        </a>
                    </div>

                    <form action="" method="POST">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-book"></i> Course Title</label>
                                <input type="text" name="course_name" class="form-control" placeholder="Enter course name..." required>
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <label class="form-label"><i class="fas fa-clock" style="color:#3b82f6"></i> Duration</label>
                                <input type="text" name="duration" class="form-control input-duration" placeholder="e.g. 2 Years">
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <label class="form-label"><i class="fas fa-tags" style="color:#10b981"></i> Course Fees</label>
                                <input type="number" name="fees" class="form-control input-fees" placeholder="₹ 0.00">
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class="fas fa-align-left"></i> Course Description</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Write a brief overview..."></textarea>
                            </div>

                            <div class="col-12 mt-5 text-center">
                                <button type="submit" name="submit" class="btn btn-submit px-5">
                                    <i class="fas fa-rocket me-2"></i> Create Course Now
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    <?php if($status == "success"): ?>
        Swal.fire({
            icon: 'success',
            title: 'Successfully Created!',
            text: 'Your new course has been added to the catalog.',
            background: '#ffffff',
            confirmButtonColor: '#a855f7',
            iconColor: '#a855f7'
        });
    <?php elseif($status == "error"): ?>
        Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', confirmButtonColor: '#6366f1' });
    <?php endif; ?>
</script>

</body>
</html>