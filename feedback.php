<?php
session_start();
include "config.php";

// Agar student session me nahi hai toh login par bhej de
if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit();
}

$studentId = $_SESSION['student_id'];
$examId = isset($_GET['exam_id']) ? $_GET['exam_id'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Feedback | Portal</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --bg-gradient: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --star-color: #ffbc00;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .feedback-container {
            max-width: 650px;
            width: 100%;
            background: var(--glass-bg);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header-section {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 40px 30px;
            color: white;
            text-align: center;
        }

        .header-section h3 { font-weight: 700; letter-spacing: -0.5px; }
        .header-section p { opacity: 0.85; font-size: 0.95rem; }

        .form-content { padding: 40px; }

        /* --- Star Rating Style --- */
        .rating-wrapper {
            background: #f9fbff;
            padding: 20px;
            border-radius: 16px;
            text-align: center;
            margin-bottom: 30px;
            border: 1.5px dashed #d1d9e6;
        }

        .stars-container {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 10px;
        }

        .star {
            font-size: 35px;
            cursor: pointer;
            color: #d1d9e6;
            transition: all 0.2s ease;
        }

        .star:hover { transform: scale(1.2); }
        .star.checked {
            color: var(--star-color);
            text-shadow: 0 0 10px rgba(255, 188, 0, 0.4);
        }

        /* --- Input Styles --- */
        .form-label {
            font-weight: 600;
            color: #4b5563;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1.5px solid #e9ecef;
            transition: all 0.3s ease;
            resize: none;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.5px;
            margin-top: 20px;
            transition: 0.3s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.25);
        }

        /* Responsive Mobile Adjustments */
        @media (max-width: 576px) {
            .form-content { padding: 25px 20px; }
            .header-section { padding: 30px 20px; }
            .star { font-size: 28px; }
        }
    </style>
</head>
<body>

<div class="feedback-container">
    <div class="header-section">
        <i class="fa-solid fa-pen-nib mb-3" style="font-size: 2rem;"></i>
        <h3>Exam Feedback</h3>
        <p>Your honest feedback helps us improve your learning experience.</p>
    </div>

    <div class="form-content">
        <form id="feedbackForm" action="save_feedback.php" method="POST">
            <input type="hidden" name="exam_id" value="<?php echo htmlspecialchars($examId); ?>">

            <div class="rating-wrapper">
                <label class="form-label d-block">How would you rate your overall experience?</label>
                <div class="stars-container" id="stars">
                    <i class="fa-solid fa-star star" data-value="1"></i>
                    <i class="fa-solid fa-star star" data-value="2"></i>
                    <i class="fa-solid fa-star star" data-value="3"></i>
                    <i class="fa-solid fa-star star" data-value="4"></i>
                    <i class="fa-solid fa-star star" data-value="5"></i>
                </div>
                <input type="hidden" name="rating" id="rating_val" required>
                <small class="text-muted mt-2 d-block" id="rating-text">Tap to rate</small>
            </div>

            <div class="mb-4">
                <label class="form-label"><i class="fa-solid fa-file-lines me-2"></i>How was the exam difficulty?</label>
                <textarea name="q1" rows="2" class="form-control" placeholder="Tell us about the questions..." required></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label"><i class="fa-solid fa-laptop-code me-2"></i>Any system or technical issues?</label>
                <textarea name="q2" rows="2" class="form-control" placeholder="Slow loading, interface issues, etc..."></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label"><i class="fa-solid fa-lightbulb me-2"></i>Any suggestions for improvement?</label>
                <textarea name="q3" rows="2" class="form-control" placeholder="What can we do better?"></textarea>
            </div>

            <button type="submit" class="btn submit-btn w-100">
                <i class="fa-solid fa-paper-plane me-2"></i> SUBMIT FEEDBACK
            </button>
        </form>
    </div>
</div>

<script>
    // Star Rating Logic
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating_val');
    const ratingText = document.getElementById('rating-text');
    const ratingLabels = ["Poor", "Fair", "Good", "Very Good", "Excellent!"];

    stars.forEach(star => {
        star.addEventListener('click', function() {
            let value = this.getAttribute('data-value');
            ratingInput.value = value;
            ratingText.innerText = ratingLabels[value - 1];
            ratingText.style.color = "var(--primary)";
            ratingText.style.fontWeight = "bold";

            // Update UI
            stars.forEach((s, index) => {
                if (index < value) {
                    s.classList.add('checked');
                } else {
                    s.classList.remove('checked');
                }
            });
        });

        // Hover Effect
        star.addEventListener('mouseover', function() {
            let value = this.getAttribute('data-value');
            stars.forEach((s, index) => {
                if (index < value) s.style.color = "#ffbc00";
            });
        });

        star.addEventListener('mouseout', function() {
            stars.forEach((s) => {
                if (!s.classList.contains('checked')) s.style.color = "#d1d9e6";
            });
        });
    });

    // Form Validation with SweetAlert
    document.getElementById('feedbackForm').addEventListener('submit', function(e) {
        if(!ratingInput.value) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Rating Required',
                text: 'Please select at least one star before submitting!',
                confirmButtonColor: '#4361ee'
            });
        }
    });
</script>

</body>
</html>