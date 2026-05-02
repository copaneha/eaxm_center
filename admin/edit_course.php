<?php
ob_start();
include("../config.php");
include("sidebar.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($conn, "SELECT * FROM courses WHERE id = $id");
    $course = mysqli_fetch_assoc($res);
    if (!$course) { header("Location: manage_course.php?msg=error"); exit(); }
} else { header("Location: manage_course.php"); exit(); }

if (isset($_POST['update_course'])) {
    $c_name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $c_desc = mysqli_real_escape_string($conn, $_POST['course_description']);
    $c_dur  = mysqli_real_escape_string($conn, $_POST['duration']);
    $c_fees = mysqli_real_escape_string($conn, $_POST['fees']);

    $update_query = "UPDATE courses SET course_name='$c_name', course_description='$c_desc', duration='$c_dur', fees='$c_fees' WHERE id=$id";
    if (mysqli_query($conn, $update_query)) {
        header("Location: manage_course.php?msg=updated");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Program | Admin Console</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --brand-primary: #4f46e5;
            --brand-accent: #0ea5e9;
            --brand-gradient: linear-gradient(135deg, #4f46e5 0%, #0ea5e9 100%);
            --bg-soft: #f8fafc;
            --sidebar-width: 260px;
        }

        body { 
            background: var(--bg-soft); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #1e293b;
            font-size: 0.9rem;
        }

        .main-wrapper {
            padding: 30px 15px;
            transition: all 0.3s ease;
        }

        @media (min-width: 992px) {
            .main-wrapper { margin-left: var(--sidebar-width); }
        }

        /* Compact Header */
        .page-header-box {
            max-width: 800px;
            margin: 0 auto 25px auto;
        }

        .fw-800 { font-weight: 800; letter-spacing: -0.5px; color: #0f172a; }

        /* Compact Glass Card */
        .glass-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 30px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
            max-width: 800px;
            margin: 0 auto;
        }

        .section-tag {
            font-size: 0.65rem;
            text-transform: uppercase;
            font-weight: 800;
            color: var(--brand-primary);
            letter-spacing: 1px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-tag::after {
            content: '';
            height: 1px;
            background: #e2e8f0;
            flex-grow: 1;
        }

        /* Compact Form Controls */
        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #475569;
            margin-bottom: 6px;
        }

        .custom-input-group {
            position: relative;
            margin-bottom: 18px;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px 12px 10px 42px;
            border: 1.5px solid #f1f5f9;
            background: #f8fafc;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background: #fff;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.95rem;
            transition: 0.2s;
        }

        .form-control:focus + .input-icon {
            color: var(--brand-primary);
        }

        textarea.form-control {
            padding-left: 15px !important;
            height: 100px;
        }

        /* Responsive Buttons */
        .action-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }

        .btn-prime {
            background: var(--brand-gradient);
            color: white;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            border: none;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
        }

        .btn-prime:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(79, 70, 229, 0.3);
            opacity: 0.9;
        }

        .btn-ghost {
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: 0.2s;
        }

        .btn-ghost:hover { background: #f1f5f9; color: #1e293b; }

        /* Mobile Optimization */
        @media (max-width: 576px) {
            .glass-card { padding: 20px; }
            .action-flex { flex-direction: column-reverse; gap: 12px; }
            .btn-prime { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="container-fluid">
        <div class="page-header-box">
            <h3 class="fw-800 m-0">Edit Program</h3>
            <p class="text-muted small">Update course details and pricing.</p>
        </div>

        <div class="glass-card">
            <form method="POST">
                <span class="section-tag">Basic Information</span>
                
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Course Title</label>
                        <div class="custom-input-group">
                            <i class="fas fa-book-open input-icon"></i>
                            <input type="text" name="course_name" class="form-control" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="course_description" class="form-control" required><?php echo htmlspecialchars($course['course_description']); ?></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Duration</label>
                        <div class="custom-input-group">
                            <i class="fas fa-calendar-alt input-icon"></i>
                            <input type="text" name="duration" class="form-control" value="<?php echo htmlspecialchars($course['duration']); ?>" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Course Fees (INR)</label>
                        <div class="custom-input-group">
                            <i class="fas fa-indian-rupee-sign input-icon"></i>
                            <input type="number" name="fees" class="form-control" value="<?php echo htmlspecialchars($course['fees']); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="action-flex">
                    <a href="manage_course.php" class="btn-ghost">
                        <i class="fas fa-chevron-left me-1"></i> Cancel
                    </a>
                    <button type="submit" name="update_course" class="btn-prime">
                        <i class="fas fa-check-circle me-1"></i> Save Updates
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
<?php ob_end_flush(); ?>