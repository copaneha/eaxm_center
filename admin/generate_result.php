<?php
include("../config.php");

if(!isset($_GET['submission_id'])){
    die("Invalid Access");
}

$sub_id = mysqli_real_escape_string($conn, $_GET['submission_id']);

// Main Query
$query="SELECT res.*, s.student_id as sid, s.name as student_name, s.roll_no, s.father_name, s.photo,
e.exam_name, e.subject_name 
FROM exam_submissions res
JOIN students s ON res.student_id = s.student_id
JOIN exams e ON res.exam_id = e.exam_id
WHERE res.id='$sub_id'";

$res = mysqli_query($conn, $query);
if (!$res) { die("Query Failed: " . mysqli_error($conn)); }
$data = mysqli_fetch_assoc($res);
if(!$data){ die("Result Not Found"); }

$student_id = $data['sid'];

// All exams query
$all_exams_query="SELECT res.*, e.exam_name, e.subject_name 
FROM exam_submissions res
JOIN exams e ON res.exam_id = e.exam_id
WHERE res.student_id = '$student_id'
ORDER BY res.submitted_at ASC";

$all_exams_res = mysqli_query($conn, $all_exams_query);
$total_obtained = 0;
$total_max = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official_Transcript_<?php echo $data['roll_no']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Inter:wght@400;600;700;800&family=Great+Vibes&family=Alex+Brush&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --formal-blue: #002651;
            --gold-leaf: #b08d57;
            --gov-red: #8b0000;
            --bg-paper: #ffffff;
        }

        body {
            background: #444;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 40px 0;
        }

        .certificate-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: var(--bg-paper);
            padding: 10mm;
            position: relative;
            box-sizing: border-box;
            box-shadow: 0 0 50px rgba(0,0,0,0.5);
        }

        .outer-border {
            border: 10px double var(--formal-blue);
            height: 100%;
            padding: 5px;
        }

        .inner-border {
            border: 2px solid var(--gold-leaf);
            min-height: calc(297mm - 45mm);
            padding: 30px;
            position: relative;
            background-image: radial-gradient(rgba(0,38,81,0.02) 1px, transparent 0);
            background-size: 25px 25px;
        }

        .watermark {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 150px;
            color: rgba(0, 38, 81, 0.04);
            font-family: 'Cinzel', serif;
            font-weight: 900;
            pointer-events: none;
            z-index: 0;
            white-space: nowrap;
        }

        header {
            display: flex;
            align-items: center;
            border-bottom: 3px solid var(--formal-blue);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .logo { width: 90px; height: 90px; }

        .college-header {
            flex: 1;
            text-align: center;
        }

        .college-header h1 {
            font-family: 'Cinzel', serif;
            color: var(--formal-blue);
            font-size: 30px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 900;
        }

        .college-header p {
            margin: 2px 0;
            font-weight: 700;
            color: var(--gov-red);
            font-size: 13px;
        }

        .doc-title-box {
            background: #f1f4f8;
            color: var(--formal-blue);
            text-align: center;
            padding: 10px;
            font-weight: 800;
            font-size: 22px;
            letter-spacing: 6px;
            margin-bottom: 25px;
            border: 2px solid var(--formal-blue);
            text-transform: uppercase;
        }

        .student-profile {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }

        .info-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 160px 20px 1fr;
            row-gap: 10px;
            border-left: 5px solid var(--gold-leaf);
            padding-left: 15px;
        }

        .info-label { font-weight: 700; color: #555; font-size: 12px; text-transform: uppercase; }
        .info-value { font-weight: 700; font-size: 14px; color: #000; border-bottom: 1px dotted #bbb; }

        .photo-box {
            width: 110px;
            height: 135px;
            border: 2px solid var(--formal-blue);
            padding: 3px;
            background: white;
            box-shadow: 4px 4px 0px var(--gold-leaf);
        }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; }

        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .marks-table th {
            background: var(--formal-blue);
            color: white;
            border: 1px solid #000;
            padding: 12px;
            font-size: 12px;
            text-transform: uppercase;
        }

        .marks-table td {
            border: 1px solid #333;
            padding: 10px;
            font-size: 14px;
            font-weight: 700;
            text-align: center;
        }

        .subject-col { text-align: left !important; }

        .result-summary {
            margin-top: 25px;
            display: flex;
            border: 3px solid var(--formal-blue);
        }

        .summary-item {
            flex: 1;
            padding: 15px;
            text-align: center;
            border-right: 1px solid #eee;
        }
        .summary-item:last-child { border-right: none; background: #f9f9f9; }
        .label-small { font-size: 11px; display: block; font-weight: 800; color: #666; margin-bottom: 4px; }
        .value-large { font-size: 22px; font-weight: 900; color: var(--formal-blue); }

        footer {
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 20px;
            position: relative;
        }

        .sig-container { 
            text-align: center; 
            width: 240px;
            position: relative;
        }

        .handwritten { 
            font-family: 'Great Vibes', cursive; 
            font-size: 40px;
            font-weight: 700;
            display: block;
            margin-bottom: -10px;
            transform: rotate(-3deg);
            z-index: 2;
            position: relative;
        }

        /* Special style for the Capital N in Principal Sign */
        .capital-n {
            font-family: 'Alex Brush', cursive; /* Zyada stylish font sirf N ke liye */
            font-size: 60px;
            margin-right: -8px;
            vertical-align: middle;
        }

        .sig-line { 
            border-top: 2.5px solid var(--formal-blue);
            padding-top: 8px; 
            font-size: 13px; 
            font-weight: 800; 
            color: var(--formal-blue);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .official-stamp-fixed {
            position: absolute;
            right: 140px; 
            bottom: 25px;
            opacity: 0.7; 
            z-index: 1;
            transform: rotate(10deg); 
            pointer-events: none;
        }

        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            padding: 15px 35px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            z-index: 1000;
        }

        @media print {
            body { background: white; padding: 0; }
            .certificate-container { box-shadow: none; margin: 0; padding: 5mm; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>

<button class="print-btn" onclick="window.print()">GENERATE PDF / PRINT</button>

<div class="certificate-container">
    <div class="outer-border">
        <div class="inner-border">
            
            <div class="watermark">JS ITI</div>

            <div style="display:flex; justify-content:space-between; font-size:11px; font-weight:800; margin-bottom:10px; color:#444;">
                <span>REF: ITI/OFFICIAL/<?php echo date("Y"); ?>/<?php echo $data['id']; ?></span>
                <span>DATE: <?php echo date("d-M-Y"); ?></span>
            </div>

            <header>
                <img src="../image/n.jpg" class="logo">
                <div class="college-header">
                    <p style="text-transform: uppercase; font-size: 10px; color: #555;">An ISO 9001:2015 Certified Technical Institute</p>
                    <h1>JS PRIVATE ITI COLLEGE</h1>
                    <p>Affiliated to NCVT, Govt. of India | Varanasi, U.P.</p>
                </div>
                <div style="text-align:right;">
                     <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=VERIFY:<?php echo $data['roll_no']; ?>" style="border:1px solid #eee;">
                </div>
            </header>

            <div class="doc-title-box">Academic Transcript</div>

            <div class="student-profile">
                <div class="info-grid">
                    <span class="info-label">Name of Student</span> <span>:</span> <span class="info-value"><?php echo strtoupper($data['student_name']); ?></span>
                    <span class="info-label">Father's Name</span> <span>:</span> <span class="info-value"><?php echo strtoupper($data['father_name']); ?></span>
                    <span class="info-label">Roll Number</span> <span>:</span> <span class="info-value" style="color:var(--gov-red);"><?php echo $data['roll_no']; ?></span>
                    <span class="info-label">Trade Name</span> <span>:</span> <span class="info-value">ELECTRICIAN / FITTER</span>
                </div>

                <div class="photo-area">
                    <div class="photo-box">
                        <?php
                        $photo_path="../image/".$data['photo'];
                        $img_src=(!empty($data['photo']) && file_exists($photo_path))?$photo_path:"../image/default-user.png";
                        ?>
                        <img src="<?php echo $img_src; ?>">
                    </div>
                </div>
            </div>

            <table class="marks-table">
                <thead>
                    <tr>
                        <th width="40">SR.</th>
                        <th class="subject-col">SUBJECT / MODULE DESCRIPTION</th>
                        <th width="80">MAX</th>
                        <th width="80">MIN</th>
                        <th width="100">OBTAINED</th>
                        <th width="80">RESULT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count=1;
                    while($row=mysqli_fetch_assoc($all_exams_res)){
                        $max=$row['total_questions']*4;
                        $min=round($max*0.33);
                        $total_obtained+=$row['score'];
                        $total_max+=$max;
                        $pass=($row['score']>=$min);
                    ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td class="subject-col"><?php echo strtoupper($row['exam_name']." (".$row['subject_name'].")"); ?></td>
                        <td><?php echo $max; ?></td>
                        <td><?php echo $min; ?></td>
                        <td style="color:var(--formal-blue); font-size:16px;"><?php echo $row['score']; ?></td>
                        <td style="color:<?php echo $pass?'#27ae60':'#c0392b'; ?>;">
                            <?php echo $pass?'PASS':'FAIL'; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="result-summary">
                <div class="summary-item">
                    <span class="label-small">TOTAL SECURED</span>
                    <span class="value-large"><?php echo $total_obtained; ?> / <?php echo $total_max; ?></span>
                </div>
                <div class="summary-item">
                    <span class="label-small">PERCENTAGE</span>
                    <?php $per=($total_max>0)?($total_obtained/$total_max)*100:0; ?>
                    <span class="value-large"><?php echo number_format($per,2); ?>%</span>
                </div>
                <div class="summary-item">
                    <span class="label-small">STATUS</span>
                    <span class="value-large" style="color:<?php echo ($per>=33)?'#27ae60':'#c0392b'; ?>">
                        <?php echo ($per>=33)?"QUALIFIED":"DISQUALIFIED"; ?>
                    </span>
                </div>
            </div>

            <footer>
                <div class="sig-container">
                    <span class="handwritten" style="color: #001a4d;">SnehaKumari</span>
                    <div class="sig-line">Exam Controller</div>
                </div>

                <div class="sig-container">
                    <div class="official-stamp-fixed">
                        <svg width="130" height="130" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" fill="none" stroke="#8b0000" stroke-width="1.5" stroke-dasharray="2,1"/>
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#8b0000" stroke-width="1"/>
                            <text font-size="7" fill="#8b0000" font-weight="bold">
                                <path id="circlePath" d="M 20,50 a 30,30 0 1,1 60,0 a 30,30 0 1,1 -60,0" fill="none"/>
                                <textPath href="#circlePath">JS PRIVATE ITI COLLEGE • VARANASI •</textPath>
                            </text>
                            <text x="50" y="52" text-anchor="middle" font-size="10" fill="#8b0000" font-weight="900">OFFICIAL</text>
                            <text x="50" y="62" text-anchor="middle" font-size="8" fill="#8b0000" font-weight="900">STAMP</text>
                        </svg>
                    </div>

                    <span class="handwritten" style="color: #4d0000;">
                        <span class="capital-n">N</span>andKishor
                    </span>
                    <div class="sig-line">Principal Signature</div>
                </div>
            </footer>

            <div style="text-align:center; margin-top:35px; border-top:1px solid #eee; padding-top:10px; font-size:9px; color:#888; text-transform:uppercase; letter-spacing:1px;">
                Address: Palahi Patti, Varanasi, Uttar Pradesh. Website: www.jsiti.com
            </div>

        </div>
    </div>
</div>

</body>
</html>