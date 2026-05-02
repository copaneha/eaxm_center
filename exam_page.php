<?php
session_start();
include("config.php");

date_default_timezone_set("Asia/Kolkata");
$current_time_str = date("Y-m-d H:i:s");

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Student details fetch
$student_res = mysqli_query($conn, "SELECT course FROM students WHERE student_id = '$student_id' LIMIT 1");
$student_data = mysqli_fetch_assoc($student_res);
$course = $student_data['course'] ?? ''; 

// Exams fetch
$query = mysqli_query($conn, "SELECT * FROM exams 
    WHERE exam_name = '$course' 
    AND status = 'Active' 
    ORDER BY exam_date ASC, exam_time ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Exams | CBT Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #2563eb;
            --dark-navy: #1e293b;
            --live-red: #ef4444;
        }
        body { 
            background-color: #f1f5f9; 
            font-family: 'Inter', 'Segoe UI', sans-serif; 
            padding-top: 80px; /* Space for fixed navbar */
        }
        
        /* Professional Navbar Styles */
        .exam-navbar {
            background: var(--dark-navy);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 12px 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        .portal-brand {
            color: #ffffff;
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .portal-brand:hover { color: #cbd5e1; }
        
        .btn-nav-home {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 16px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-nav-home:hover {
            background: #ffffff;
            color: var(--dark-navy) !important;
            transform: translateY(-2px);
        }

        /* Exam Card Styles */
        .exam-card { 
            border: none;
            border-radius: 16px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .exam-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
        
        .card-accent { height: 5px; background: var(--primary-blue); }
        .card-accent.live { background: var(--live-red); animation: pulse-bg 2s infinite; }

        @keyframes pulse-bg { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

        .status-live { 
            color: var(--live-red); 
            font-weight: 700; 
            font-size: 0.75rem;
            background: rgba(239, 68, 68, 0.1);
            padding: 4px 10px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .dot { height: 8px; width: 8px; background-color: var(--live-red); border-radius: 50%; animation: blink 1s infinite; }
        @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0; } 100% { opacity: 1; } }

        .timer-box { 
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 15px;
            border-radius: 10px;
            font-weight: 700;
            color: #475569;
        }
    </style>
</head>

<body>

<nav class="exam-navbar">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="dashboard.php" class="portal-brand">
            <i class="fas fa-laptop-code text-primary"></i>
            <span>EXAM<span class="text-primary">PORTAL</span></span>
        </a>
        
        <div class="d-flex align-items-center gap-3">
            <span class="d-none d-md-block text-white-50 small">
                <i class="fas fa-user-circle me-1"></i> ID: <?php echo $student_id; ?>
            </span>
            <a href="dashboard.php" class="btn-nav-home">
                <i class="fas fa-home me-1"></i> Back to Home
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row align-items-end mb-4">
        <div class="col-md-8">
            <h3 class="fw-bold text-dark mb-1">Current Active Examinations</h3>
            <p class="text-muted mb-0">
                <i class="far fa-clock me-1"></i> System Time: 
                <span id="live-clock" class="fw-bold text-primary"><?php echo date("d M, Y | h:i:s A"); ?></span>
            </p>
        </div>
    </div>

    <div class="row g-4">
    <?php 
    if(mysqli_num_rows($query) > 0) {
        while($row = mysqli_fetch_assoc($query)) {
            $exam_id = $row['exam_id']; 
            
            $checkSub = mysqli_query($conn, "SELECT * FROM exam_submissions WHERE student_id = '$student_id' AND exam_id = '$exam_id'");
            $is_submitted = (mysqli_num_rows($checkSub) > 0);
            
            $start_time = $row['exam_date'] . ' ' . $row['exam_time'];
            $end_time   = $row['exam_date'] . ' ' . $row['exam_end_time'];
    ?>
        <div class="col-lg-6">
            <div class="exam-card card h-100" id="card-<?php echo $exam_id; ?>">
                <div class="card-accent" id="accent-<?php echo $exam_id; ?>"></div>
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-light text-primary mb-2"><?php echo $row['exam_name']; ?></span>
                            <h4 class="fw-bold text-dark"><?php echo strtoupper($row['subject_name']); ?></h4>
                        </div>
                        <div id="live-indicator-<?php echo $exam_id; ?>"></div>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">EXAM DATE</div>
                            <div class="fw-bold"><i class="far fa-calendar-check me-2 text-primary"></i><?php echo date("d M, Y", strtotime($start_time)); ?></div>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                            <div class="timer-box d-inline-block shadow-sm" id="timer-<?php echo $exam_id; ?>">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" style="opacity: 0.08;">

                    <div class="action-area" id="action-<?php echo $exam_id; ?>">
                        <?php if($is_submitted) { ?>
                            <button class="btn btn-success w-100 rounded-3 fw-bold py-2 shadow-sm" disabled>
                                <i class="fas fa-check-double me-2"></i>SUBMISSION RECEIVED
                            </button>
                        <?php } else { ?>
                            <button class="btn btn-light w-100 rounded-3 py-2 border" disabled>Checking Status...</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function() {
                const examId = "<?php echo $exam_id; ?>";
                const startTime = new Date("<?php echo $start_time; ?>").getTime();
                const endTime = new Date("<?php echo $end_time; ?>").getTime();
                const isSubmitted = <?php echo $is_submitted ? 'true' : 'false'; ?>;

                const timerInterval = setInterval(function() {
                    const now = new Date().getTime();
                    const timerLabel = document.getElementById("timer-" + examId);
                    const actionArea = document.getElementById("action-" + examId);
                    const indicator = document.getElementById("live-indicator-" + examId);
                    const accent = document.getElementById("accent-" + examId);

                    if (isSubmitted) {
                        timerLabel.innerHTML = "<span class='text-success small'>Completed</span>";
                        clearInterval(timerInterval);
                        return;
                    }

                    if (now < startTime) {
                        let diff = startTime - now;
                        let h = Math.floor(diff / (3600000));
                        let m = Math.floor((diff % 3600000) / 60000);
                        let s = Math.floor((diff % 60000) / 1000);
                        
                        timerLabel.innerHTML = `<small class='text-muted'>Starts in:</small> ${h}h ${m}m ${s}s`;
                        actionArea.innerHTML = '<button class="btn btn-outline-secondary w-100 rounded-3 py-2 fw-500" disabled><i class="fas fa-lock me-2"></i>NOT YET OPEN</button>';
                    } 
                    else if (now >= startTime && now <= endTime) {
                        let diff = endTime - now;
                        let m = Math.floor(diff / 60000);
                        let s = Math.floor((diff % 60000) / 1000);
                        
                        accent.classList.add('live');
                        timerLabel.innerHTML = `<small class='text-danger'>Ending in:</small> ${m}m ${s}s`;
                        indicator.innerHTML = '<span class="status-live"><span class="dot"></span> LIVE NOW</span>';
                        actionArea.innerHTML = '<a href="generate_paper.php?id=' + examId + '" class="btn btn-primary w-100 rounded-3 fw-bold py-2 shadow border-0">START EXAMINATION <i class="fas fa-arrow-right ms-2"></i></a>';
                    } 
                    else {
                        timerLabel.innerHTML = "<span class='text-muted small'>Closed</span>";
                        indicator.innerHTML = "";
                        accent.style.background = "#94a3b8";
                        actionArea.innerHTML = '<button class="btn btn-light w-100 text-muted rounded-3 py-2 border" disabled>SESSION EXPIRED</button>';
                        clearInterval(timerInterval);
                    }
                }, 1000);
            })();
        </script>
    <?php 
        } 
    } else {
        echo "
        <div class='col-12 text-center py-5'>
            <div class='card border-0 shadow-sm py-5 rounded-4'>
                <div class='mb-3'><i class='fas fa-exclamation-circle fa-3x text-light-emphasis'></i></div>
                <h5 class='text-muted'>No active examinations scheduled for your course at this moment.</h5>
            </div>
        </div>";
    }
    ?>
    </div>
</div>

<script>
    // Live Clock functionality
    setInterval(function() {
        const now = new Date();
        document.getElementById('live-clock').innerText = now.toLocaleString('en-IN', { 
            day: '2-digit', month: 'short', year: 'numeric', 
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true 
        });
    }, 1000);
</script>

</body>
</html>