<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentId = $_SESSION['student_id'];
    $examId = mysqli_real_escape_string($conn, $_POST['exam_id']);
    $userAnswers = isset($_POST['ans']) ? $_POST['ans'] : [];
    $ip = $_SERVER['REMOTE_ADDR'];

    // 1. Correct answers fetch karein aur Score calculate karein
    $res = mysqli_query($conn, "SELECT correct_option FROM question_bank WHERE exam_id = '$examId' ORDER BY id ASC");
    
    $correct = 0; 
    $wrong = 0; 
    $total_q = mysqli_num_rows($res);
    $i = 0;

    while($row = mysqli_fetch_assoc($res)) {
        if(isset($userAnswers[$i]) && !empty($userAnswers[$i])) {
            if(strtoupper(trim($userAnswers[$i])) == strtoupper(trim($row['correct_option']))) { 
                $correct++; 
            } else { 
                $wrong++; 
            }
        }
        $i++;
    }

    $score = ($correct * 4) - ($wrong * 1); 

    // 2. AUTOMATIC ATTENDANCE LOGIC
    // Pehle check karein ki attendance already marked toh nahi hai
    $check = mysqli_query($conn, "SELECT id FROM attendance WHERE student_id = '$studentId' AND exam_id = '$examId'");
    if(mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO attendance (student_id, exam_id, status, ip_address) VALUES ('$studentId', '$examId', 'Present', '$ip')");
    }

    // 3. Database mein Result save karein
    $sql = "INSERT INTO exam_submissions (student_id, exam_id, total_questions, correct_ans, wrong_ans, score, status, submitted_at) 
            VALUES ('$studentId', '$examId', '$total_q', '$correct', '$wrong', '$score', 'Completed', NOW())";

    $save_status = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam Submitted</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; }
    </style>
</head>
<body>

<?php
    if($save_status) {
        echo "<script>
            // Timer clear karein
            localStorage.removeItem('exam_time_left');

            // Success SweetAlert
            Swal.fire({
                icon: 'success',
                title: 'Exam Submitted!',
                text: 'Aapka exam aur attendance successfully record ho gaya hai.',
                confirmButtonColor: '#002366',
                confirmButtonText: 'Go to Dashboard',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'feedback.php?exam_id=<?php echo $examId; ?>';
                }
            });
        </script>";
    } else {
        $error = mysqli_error($conn);
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                text: 'Error: $error',
            }).then(() => {
                window.location.href = 'dashboard.php';
            });
        </script>";
    }
?>

</body>
</html>

<?php
    exit();
} else {
    // Agar koi bina POST request ke aaye toh wapas bhej do
    header("Location: dashboard.php");
    exit();
}
?>