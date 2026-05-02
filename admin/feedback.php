<?php 
include "../config.php"; 
include "sidebar.php"; // Sidebar consistency ke liye
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Feedback | Admin</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4361ee;
            --bg: #f8fafc;
            --surface: #ffffff;
            --text-main: #2d3436;
            --text-muted: #636e72;
            --star: #ffbc00;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg); 
            margin: 0; 
            color: var(--text-main); 
        }

        .main-container { 
            padding: 20px;
            margin-top: 60px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Desktop Sidebar Gap */
        @media (min-width: 992px) {
            .main-container { padding-left: 280px; }
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* --- Feedback Grid Layout --- */
        .feedback-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .feedback-card {
            background: var(--surface);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #edf2f7;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .feedback-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(67, 97, 238, 0.1);
            border-color: var(--primary);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
        }

        .student-id {
            font-weight: 700;
            color: var(--text-main);
            font-size: 0.9rem;
            background: #eef2ff;
            padding: 4px 10px;
            border-radius: 8px;
        }

        .rating-badge {
            color: var(--star);
            font-weight: 700;
            font-size: 1rem;
        }

        .question-box {
            margin-top: 12px;
        }

        .q-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 4px;
            display: block;
        }

        .q-text {
            font-size: 14px;
            line-height: 1.5;
            color: #4b5563;
            margin-bottom: 12px;
            display: block;
            background: #f9fbff;
            padding: 10px;
            border-radius: 8px;
        }

        /* Empty State */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px;
            color: var(--text-muted);
        }

        @media (max-width: 576px) {
            .page-title { font-size: 20px; }
            .feedback-card { padding: 18px; }
        }
    </style>
</head>
<body>

<div class="main-container">
    <h3 class="page-title">
        <i class="fa-solid fa-comments"></i> Student Feedback Analytics
    </h3>

    <div class="feedback-grid">
        <?php
        $res = mysqli_query($conn, "SELECT * FROM feedback ORDER BY id DESC");

        if(mysqli_num_rows($res) > 0) {
            while($row = mysqli_fetch_assoc($res)) {
                ?>
                <div class="feedback-card">
                    <div class="card-header">
                        <span class="student-id">
                            <i class="fa-solid fa-user-graduate me-1"></i> ID: <?php echo $row['student_id']; ?>
                        </span>
                        <span class="rating-badge">
                            <?php 
                                // Stars loop based on rating
                                for($i=1; $i<=5; $i++){
                                    if($i <= $row['rating']) echo '<i class="fa-solid fa-star"></i>';
                                    else echo '<i class="fa-regular fa-star" style="color:#cbd5e0"></i>';
                                }
                            ?>
                        </span>
                    </div>

                    <div class="question-box">
                        <span class="q-label">Q1: Overall Experience</span>
                        <span class="q-text"><?php echo $row['q1']; ?></span>

                        <span class="q-label">Q2: Content Quality</span>
                        <span class="q-text"><?php echo $row['q2']; ?></span>

                        <span class="q-label">Q3: Suggestions</span>
                        <span class="q-text"><?php echo $row['q3']; ?></span>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div class='empty-state'>
                    <i class='fa-regular fa-face-meh' style='font-size:48px; margin-bottom:15px; display:block;'></i>
                    No feedback received yet.
                  </div>";
        }
        ?>
    </div>
</div>

</body>
</html>