<?php 
include("../config.php"); 
require 'mail_config.php'; 

// Fetch Employee Details
$id = mysqli_real_escape_string($conn, $_GET['id']);
$emp_res = mysqli_query($conn, "SELECT name, role, email FROM employees WHERE id=$id");
$emp_data = mysqli_fetch_assoc($emp_res);

if(!$emp_data) {
    die("Employee not found!");
}

// Current Date for validation
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Duty | Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary-blue: #1a3a5f; --teal-color: #17a2b8; }
        body { font-family: 'Poppins', sans-serif; background: #f4f7f9; margin: 0; }
        
        /* Layout Fix for Sidebar */
        .wrapper { display: flex; min-height: 100vh; }
        
        .main-content { 
            flex: 1; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            padding: 20px; 
            box-sizing: border-box;
        }

        .assign-card { 
            background: #fff; 
            width: 100%; 
            max-width: 550px; 
            border-radius: 15px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.1); 
            overflow: hidden; 
            margin: 20px auto;
        }

        .header-section { 
            background: linear-gradient(135deg, #1a3a5f, #17a2b8); 
            padding: 30px 20px; 
            text-align: center; 
            color: #fff; 
        }

        .emp-badge { 
            background: rgba(255,255,255,0.2); 
            padding: 6px 15px; 
            border-radius: 20px; 
            display: inline-block; 
            margin-top: 10px; 
            font-size: 13px; 
        }

        .form-body { padding: 30px; }
        .input-group { margin-bottom: 20px; position: relative; }
        .input-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #333; }
        .input-group input { 
            width: 100%; 
            padding: 12px 15px 12px 45px; 
            border-radius: 8px; 
            border: 1px solid #ddd; 
            box-sizing: border-box; 
            font-size: 14px;
        }
        .input-group i { position: absolute; left: 15px; top: 38px; color: #17a2b8; }

        .btn-assign { 
            width: 100%; 
            padding: 15px; 
            background: #1a3a5f; 
            color: #fff; 
            border: none; 
            border-radius: 10px; 
            font-size: 16px; 
            font-weight: 700; 
            cursor: pointer; 
            transition: 0.3s; 
            margin-top: 10px;
        }
        .btn-assign:hover { background: #142d4a; transform: translateY(-2px); }

        .cancel-link { 
            display: block; 
            text-align: center; 
            margin-top: 20px; 
            color: #7f8c8d; 
            text-decoration: none; 
            font-size: 14px; 
        }

        /* Responsive Media Queries */
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 15px; }
            .header-section { padding: 20px 15px; }
            .header-section h2 { font-size: 1.2rem; }
            .form-body { padding: 20px; }
        }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main-content">
    <div class="assign-card">
        <div class="header-section">
            <i class="fa fa-calendar-check fa-3x"></i>
            <h2>Assign Exam Duty</h2>
            <div class="emp-badge">
                <i class="fa fa-user"></i> <?php echo htmlspecialchars($emp_data['name']); ?> 
            </div>
        </div>
        <div class="form-body">
            <form method="post">
                <div class="input-group">
                    <label>Exam Name</label>
                    <i class="fa fa-book"></i>
                    <input type="text" name="exam_name" placeholder="e.g. Final Term 2026" required>
                </div>

                <div class="input-group">
                    <label>Examination Date</label>
                    <i class="fa fa-calendar-alt"></i>
                    <input type="date" name="exam_date" min="<?php echo $today; ?>" required>
                </div>

                <div class="input-group">
                    <label>Exam Time / Shift</label>
                    <i class="fa fa-clock"></i>
                    <input type="text" name="exam_time" placeholder="e.g. 10:00 AM - 01:00 PM" required>
                </div>

                <div class="input-group">
                    <label>Room Number / Hall</label>
                    <i class="fa fa-door-open"></i>
                    <input type="text" name="room" placeholder="Enter Room No." required>
                </div>

                <button type="submit" name="assign" class="btn-assign">CONFIRM & SEND NOTIFICATION</button>
                <a href="mange_empolyee.php" class="cancel-link"><i class="fa fa-arrow-left"></i> Cancel & Go Back</a>
            </form>
        </div>
    </div>
</div>

<?php
if(isset($_POST['assign'])){
    global $mail; 

    $exam_name = mysqli_real_escape_string($conn, $_POST['exam_name']);
    $exam_date = mysqli_real_escape_string($conn, $_POST['exam_date']);
    $exam_time = mysqli_real_escape_string($conn, $_POST['exam_time']);
    $room = mysqli_real_escape_string($conn, $_POST['room']);

    // Backend validation for date
    if($exam_date < $today) {
        echo "<script>Swal.fire('Error', 'You cannot assign duty for a past date!', 'error');</script>";
    } else {
        $q = "INSERT INTO duties (employee_id, exam_name, exam_date, exam_time, room) 
              VALUES ('$id', '$exam_name', '$exam_date', '$exam_time', '$room')";

        if(mysqli_query($conn, $q)){
            $mail_sent = false;

            if (isset($mail)) {
                try {
                    $mail->clearAddresses();
                    $mail->addAddress($emp_data['email'], $emp_data['name']);
                    $mail->isHTML(true);
                    $mail->Subject = "Official Notification: Exam Duty Assignment - $exam_name";
                    
                    $mail->Body = "
                    <div style='background-color: #f9f9f9; padding: 20px; font-family: sans-serif;'>
                        <div style='background-color: #ffffff; padding: 30px; border-radius: 10px; border-top: 5px solid #1a3a5f;'>
                            <h2 style='color: #1a3a5f;'>Duty Allocation Notice</h2>
                            <p>Hello <b>{$emp_data['name']}</b>,</p>
                            <p>You have been assigned for invigilation duty:</p>
                            <div style='background-color: #f1f8ff; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                                <p><b>Exam:</b> $exam_name</p>
                                <p><b>Date:</b> $exam_date</p>
                                <p><b>Time:</b> $exam_time</p>
                                <p><b>📍 Room:</b> $room</p>
                            </div>
                            <p>Regards,<br><b>Administration Department</b></p>
                        </div>
                    </div>";

                    if($mail->send()) { $mail_sent = true; }
                } catch (Exception $e) { }
            }

            $msg = $mail_sent ? "Duty assigned and email sent successfully!" : "Duty saved, but email failed.";
            $icon = $mail_sent ? "success" : "warning";

            echo "<script>
                Swal.fire({
                    title: 'Process Completed!',
                    text: '$msg',
                    icon: '$icon',
                    confirmButtonColor: '#1a3a5f'
                }).then(() => { window.location = 'mange_empolyee.php'; });
            </script>";
        } else {
            echo "<script>Swal.fire('Error', 'Database insertion failed', 'error');</script>";
        }
    }
}
?>
</body>
</html>