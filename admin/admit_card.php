<?php
include("../config.php");

if (!isset($_GET['student_id']) || empty($_GET['student_id'])) {
    die("<b style='color:red'>Error: Student ID missing!</b>");
}

$student_id = mysqli_real_escape_string($conn, $_GET['student_id']);

$student_query = mysqli_query($conn, "SELECT * FROM students WHERE student_id = '$student_id'");
if(!$student_query){ die("Student SQL Error: ".mysqli_error($conn)); }
$student = mysqli_fetch_assoc($student_query);
if (!$student) { die("Student Record Not Found!"); }

$exam_query = "SELECT e.exam_name, e.subject_name, e.exam_date, e.exam_time, e.exam_end_time, 
                        l.lab_name, c.centre_name, c.city, sa.seat_no as pc_no 
                 FROM seat_allocation sa
                 JOIN labs l ON sa.lab_id = l.id
                 JOIN exams e ON sa.exam_id = e.exam_id
                 JOIN exam_centres c ON sa.centre_id = c.id
                 WHERE sa.student_id = '$student_id'
                 ORDER BY e.exam_date ASC";

$exams_result = mysqli_query($conn, $exam_query);
$exam_data = mysqli_fetch_assoc($exams_result);
$dynamic_exam_name = ($exam_data) ? $exam_data['exam_name'] : "N/A";
mysqli_data_seek($exams_result, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Admit Card - <?php echo $student['name']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:wght@700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --primary: #002d5b; --secondary: #d41414; --border: #002d5b; }
        
        body { background: #f0f2f5; font-family: 'Poppins', sans-serif; margin: 0; padding: 20px; }
        
        .admit-card-wrapper { 
            max-width: 850px; margin: 0 auto; background: #fff; 
            border: 4px double var(--primary); padding: 5px; box-shadow: 0 0 30px rgba(0,0,0,0.1);
            position: relative;
        }

        .inner-border { border: 1px solid var(--primary); padding: 30px; position: relative; overflow: hidden; }

        /* Professional Watermark */
        .inner-border::after {
            content: "JS ITI VARANASI";
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 70px; font-weight: 900;
            color: rgba(0, 45, 91, 0.04);
            white-space: nowrap; z-index: 0; pointer-events: none;
        }

        /* Header Logo Adjustments */
        .header { display: flex; align-items: center; border-bottom: 3px solid var(--primary); padding-bottom: 15px; margin-bottom: 20px; gap: 10px; }
        
        /* Left Logo Size */
        .logo { width: 70px; height: auto; }
        
        .inst-title { flex-grow: 1; text-align: center; }
        .inst-title h1 { margin: 0; color: var(--primary); font-size: 22px; font-family: 'Playfair Display', serif; text-transform: uppercase; }
        .inst-title p { margin: 2px 0; font-size: 10px; font-weight: 600; color: #333; }

        /* Right Logo Size */
        .lo img { width: 70px; height: auto; border: 1px solid #eee; padding: 2px; }

        .hall-ticket-head { text-align: center; margin-bottom: 25px; }
        .hall-ticket-head span { 
            border: 2px solid var(--primary); padding: 5px 30px; 
            font-weight: 800; text-transform: uppercase; letter-spacing: 2px;
            background: #f8fbff; font-size: 13px;
        }

        .details-grid { display: grid; grid-template-columns: 1fr 180px; gap: 20px; margin-bottom: 20px; }
        .info-table { width: 100%; font-size: 14px; border-collapse: collapse; }
        .info-table td { padding: 8px 5px; border-bottom: 1px solid #eee; }
        .label { font-weight: 700; color: #555; width: 140px; font-size: 12px; }
        .value { font-weight: 700; color: #000; text-transform: uppercase; font-size: 13px; }

        .photo-area { text-align: center; }
        .photo-box { border: 2px solid var(--primary); padding: 3px; background: #fff; position: relative; }
        .photo-box img { width: 100%; height: 180px; object-fit: cover; }
        .candidate-sign { border: 1px dashed #999; margin-top: 5px; height: 35px; font-size: 9px; color: #999; padding-top: 20px; }

        .exam-table { width: 100%; border-collapse: collapse; margin-top: 10px; z-index: 1; position: relative; }
        .exam-table th { background: var(--primary); color: #fff; padding: 10px; font-size: 11px; border: 1px solid #000; text-transform: uppercase; }
        .exam-table td { padding: 10px; border: 1px solid #000; font-size: 12px; font-weight: 600; background: rgba(255,255,255,0.8); }
        .pc-no { font-size: 18px; color: var(--secondary); font-weight: 800; text-align: center; background: #fff1f1 !important; }

        .footer-section { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 35px; position: relative; }
        
        .stamp-circle {
            position: absolute; right: 50px; bottom: 10px;
            width: 110px; height: 110px;
            border: 3px double rgba(0, 45, 91, 0.4);
            border-radius: 50%;
            display: flex; align-items: center; text-align: center;
            font-size: 8px; font-weight: bold; color: rgba(0, 45, 91, 0.4);
            text-transform: uppercase; pointer-events: none;
            transform: rotate(-15deg); justify-content: center;
        }

        .signature-box { text-align: center; position: relative; z-index: 2; width: 200px; }
        .sign-img { 
            font-family: 'Great Vibes', cursive; font-size: 32px; 
            color: #000b41; margin-bottom: -10px;
            transform: rotate(-2deg);
        }
        .sign-line { border-top: 2px solid #000; padding-top: 5px; font-weight: 700; font-size: 12px; }

        .instructions { 
            margin-top: 20px; padding: 10px; border: 1px solid #ddd; 
            border-left: 5px solid var(--secondary); background: #fffcfc; 
        }
        .instructions h4 { margin: 0 0 5px 0; font-size: 11px; color: var(--secondary); }
        .instructions li { font-size: 10px; line-height: 1.3; margin-bottom: 2px; }

        @media print {
            body { padding: 0; background: #fff; }
            .no-print { display: none; }
            .admit-card-wrapper { border: 4px double #000; width: 100%; box-shadow: none; margin: 0; }
        }
    </style>
</head>
<body>

<div class="admit-card-wrapper">
    <div class="inner-border">
        
        <div class="header">
            <img src="https://cdn-icons-png.flaticon.com/512/2231/2231649.png" class="logo" alt="Main Logo">
            
            <div class="inst-title">
                <h1>JS Private Industrial Training Institute</h1>
                <p>Ncvt Affiliation No: DGT-6/24/147/2014-TC | Institute Code: PR09000574</p>
                <p>Varanasi, Uttar Pradesh, Pin: 221003</p>
                <p style="background: #000; color: #fff; display: inline-block; padding: 1px 8px; margin-top: 3px; font-size: 9px;">A Unit of JS Educational Trust</p>
            </div>

            <div class="lo">
              <img src="../image/n.jpg" class="watermark-overlay">
            </div>
        </div>

        <div class="hall-ticket-head">
            <span>PROVISIONAL HALL TICKET</span>
        </div>

        <div class="details-grid">
            <div class="info-side">
                <table class="info-table">
                    <tr>
                        <td class="label">Roll Number</td>
                        <td class="value">: <?php echo $student['roll_no']; ?></td>
                    </tr>
                    <tr>
                        <td class="label">Candidate Name</td>
                        <td class="value">: <?php echo strtoupper($student['name']); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Father's Name</td>
                        <td class="value">: <?php echo strtoupper($student['father_name']); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Trade / Exam</td>
                        <td class="value">: <span style="color: var(--secondary);"><?php echo strtoupper($dynamic_exam_name); ?></span></td>
                    </tr>
                    <tr>
                        <td class="label">Shift / Session</td>
                        <td class="value">: 2024-2026 (Regular)</td>
                    </tr>
                </table>
            </div>
            <div class="photo-area">
                <div class="photo-box">
                    <?php 
                        $photo_path = "../image/" . $student['photo'];
                        $display_photo = (file_exists($photo_path) && !empty($student['photo'])) ? $photo_path : "https://via.placeholder.com/160x180?text=Photo";
                    ?>
                    <img src="<?php echo $display_photo; ?>" alt="Candidate">
                </div>
               
            </div>
        </div>

        <table class="exam-table">
            <thead>
                <tr>
                    <th width="20%">Date & Time</th>
                    <th width="35%">Subject Description</th>
                    <th width="35%">Examination Venue</th>
                    <th width="10%">PC/Seat</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(mysqli_num_rows($exams_result) > 0) {
                    while($row = mysqli_fetch_assoc($exams_result)) {
                ?>
                <tr>
                    <td align="center">
                        <b><?php echo date('d-m-Y', strtotime($row['exam_date'])); ?></b><br>
                        <small><?php echo date('h:i A', strtotime($row['exam_time'])); ?></small>
                    </td>
                    <td>
                        <div style="color:var(--primary); font-weight: 700;"><?php echo strtoupper($row['subject_name']); ?></div>
                        <small>Module: CBT Online Exam</small>
                    </td>
                    <td>
                        <b><?php echo $row['centre_name']; ?></b><br>
                        <small><?php echo $row['lab_name']; ?>, <?php echo $row['city']; ?></small>
                    </td>
                    <td class="pc-no"><?php echo $row['pc_no']; ?></td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>

        <div class="instructions">
            <h4><i class="fa fa-info-circle"></i> CANDIDATE GUIDELINES:</h4>
            <ul style="margin:0; padding-left:15px;">
                <li>CANDIDATE MUST CARRY ORIGINAL AADHAR CARD ALONG WITH THIS ADMIT CARD.</li>
                <li>Reach the exam centre 45 minutes before the commencement of the exam.</li>
                <li>Digital devices like mobile phones, smartwatches are strictly prohibited.</li>
            </ul>
        </div>

        <div class="footer-section">
            <div class="signature-box">
                <div style="height:35px;"></div>
                <div class="sign-line">Candidate Signature</div>
            </div>

            <div class="stamp-circle">
                JS PVT ITI<br>VARANASI<br>OFFICIAL SEAL
            </div>

            <div class="signature-box">
                <div class="sign-img">Nandkishor</div>
                <div class="sign-line">Controller of Examination</div>
                <div style="font-size: 9px; color: var(--primary); font-weight: bold;">(Digitally Verified)</div>
            </div>
        </div>

    </div>
</div>

<div class="no-print" style="text-align:center; margin-top: 30px;">
    <button onclick="window.print()" style="background: var(--primary); color: white; padding: 10px 30px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
        <i class="fa fa-print"></i> PRINT ADMIT CARD
    </button>
</div>

</body>
</html>