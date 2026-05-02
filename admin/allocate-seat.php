<?php
include("../config.php");
require 'mail_config.php';

$msg="";
$status="";
$selected_exam = $_POST['exam_id'] ?? '';

/* ===== COURSE FETCH ===== */
$course_filter = '';
if($selected_exam!=''){
    $q = mysqli_query($conn,"SELECT exam_name FROM exams WHERE exam_id='$selected_exam'");
    $d = mysqli_fetch_assoc($q);
    $course_filter = $d['exam_name'] ?? '';
}

/* ===== ALLOCATION LOGIC ===== */
if(isset($_POST['allocate'])){
    $exam_id = $_POST['exam_id'];
    $centre_id = $_POST['centre_id'];
    $lab_id = $_POST['lab_id'];
    $start = (int)$_POST['start_pc'];
    $students = $_POST['students'] ?? [];

    if(empty($students)){
        $msg="No student selected!";
        $status="error";
    }else{
        $exam_q = mysqli_query($conn,"SELECT * FROM exams WHERE exam_id='$exam_id'");
        $exam = mysqli_fetch_assoc($exam_q);
        
        $pc=$start;
        $success=0;

        foreach($students as $sid){
            $seat="PC-$pc";
            mysqli_query($conn,"INSERT INTO seat_allocation (exam_id,centre_id,lab_id,student_id,seat_no) 
                                VALUES('$exam_id','$centre_id','$lab_id','$sid','$seat')");

            $stu_q = mysqli_query($conn,"SELECT * FROM students WHERE student_id='$sid'");
            $stu = mysqli_fetch_assoc($stu_q);

            if(!empty($stu['email'])){
                $mail->clearAddresses();
                $mail->addAddress($stu['email']);
                $mail->Subject="Seat Allotted - " . $exam['exam_name'];
                $mail->isHTML(true);
                $mail->Body="
                <div style='font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px; border-radius: 10px;'>
                    <h2 style='color: #4361ee;'>Hello, {$stu['name']}</h2>
                    <p>Your seat has been successfully allotted for the upcoming exam.</p>
                    <hr>
                    <p><strong>Exam:</strong> {$exam['exam_name']}</p>
                    <p><strong>Seat Number:</strong> <span style='background: #e7f0ff; padding: 5px 10px; border-radius: 5px;'>$seat</span></p>
                    <br>
                    <a href='https://ungrindable-aleida-subcandidly.ngrok-free.dev/managment/admin/admit_card.php?student_id=$sid&exam_id=$exam_id' 
                       style='background: #4361ee; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>
                       Download Admit Card
                    </a>
                </div>";
                $mail->send();
            }
            $pc++;
            $success++;
        }
        $msg="Successfully allotted seats to $success students and emails sent!";
        $status="success";
    }
}

$exams = mysqli_query($conn,"SELECT * FROM exams");
$centres = mysqli_query($conn,"SELECT * FROM exam_centres");
$labs = mysqli_query($conn,"SELECT * FROM labs");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Allocation Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary: #4361ee; --primary-hover: #3730a3; --bg: #f3f4f6; --card: #ffffff; --text-main: #1f2937; --text-muted: #6b7280; --border: #e5e7eb; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text-main); margin: 0; padding: 0; }
        
        /* Layout Adjustments */
        .main-wrapper { max-width: 1100px;margin-left:25%; margin-top:100px; background: var(--card); border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; }
        
        /* Navigation/Header */
        .header { background: var(--primary); padding: 25px; color: white; display: flex; justify-content: space-between; align-items: center; }
        .header h2 { margin: 0; font-size: 1.5rem; font-weight: 600; }
        .btn-home { background: rgba(255,255,255,0.2); color: white; text-decoration: none; padding: 8px 15px; border-radius: 8px; font-size: 0.9rem; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
        .btn-home:hover { background: rgba(255,255,255,0.3); }

        .content-body { padding: 30px; }
        .config-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .input-group { display: flex; flex-direction: column; }
        .input-group label { font-size: 0.75rem; font-weight: 700; margin-bottom: 6px; color: var(--text-muted); text-transform: uppercase; }
        select, input { padding: 12px; border: 1.5px solid var(--border); border-radius: 8px; font-size: 14px; outline: none; transition: 0.2s; }
        select:focus, input:focus { border-color: var(--primary); }

        .selection-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-top: 1px solid var(--border); padding-top: 15px; flex-wrap: wrap; gap: 10px; }
        .search-box { width: 100%; max-width: 300px; }
        
        .student-table-container { border: 1px solid var(--border); border-radius: 10px; max-height: 350px; overflow-y: auto; background: #fafafa; }
        .student-row { display: flex; align-items: center; padding: 12px 15px; border-bottom: 1px solid var(--border); cursor: pointer; transition: 0.2s; }
        .student-row:hover { background: #f1f4ff; }
        .student-info { display: flex; flex-direction: column; margin-left: 12px; }
        .student-name { font-weight: 600; color: var(--text-main); }
        .student-id { font-size: 12px; color: var(--text-muted); }

        .action-bar { margin-top: 30px; display: flex; justify-content: flex-end; }
        .btn-allocate { background: var(--primary); color: white; border: none; padding: 15px 40px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 10px; }
        .btn-allocate:hover { background: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3); }

        /* Responsive */
        @media (max-width: 768px) {
            .main-wrapper { margin: 10px; border-radius: 0; }
            .header { flex-direction: column; gap: 15px; }
            .search-box { max-width: 100%; }
        }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main-wrapper">
    <div class="header">
        <h2><i class="fas fa-chair me-2"></i> Seat Allocation</h2>
        <a href="dascboard.php" class="btn-home"><i class="fas fa-home"></i> Go to Dashboard</a>
    </div>

    <div class="content-body">
        <form method="POST" id="allocationForm">
            <div class="config-grid">
                <div class="input-group">
                    <label>Exam Name</label>
                    <select name="exam_id" id="exam_select" required onchange="this.form.submit()">
                        <option value="">-- Select Exam --</option>
                        <?php mysqli_data_seek($exams, 0); while($e=mysqli_fetch_assoc($exams)){ ?>
                            <option value="<?= $e['exam_id'] ?>" <?= ($selected_exam==$e['exam_id'])?'selected':'' ?>><?= $e['exam_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Exam Centre</label>
                    <select name="centre_id" required>
                        <?php while($c=mysqli_fetch_assoc($centres)){ ?>
                            <option value="<?= $c['id'] ?>"><?= $c['centre_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Lab / Room</label>
                    <select name="lab_id" required>
                        <?php while($l=mysqli_fetch_assoc($labs)){ ?>
                            <option value="<?= $l['id'] ?>"><?= $l['lab_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Start PC No.</label>
                    <input type="number" name="start_pc" value="1" min="1" required>
                </div>
            </div>

            <div class="selection-header">
                <label style="font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" id="selectAll" style="width: 18px; height: 18px;"> Select All Available Students
                </label>
                <input type="text" id="studentSearch" class="search-box" placeholder="🔍 Search student name...">
            </div>

            <div class="student-table-container">
                <?php
                $q_str = ($course_filter!='') ? "SELECT * FROM students WHERE course='$course_filter'" : "SELECT * FROM students";
                $stu = mysqli_query($conn, $q_str);
                if(mysqli_num_rows($stu) > 0){
                    while($s=mysqli_fetch_assoc($stu)){ ?>
                    <div class="student-row">
                        <input type="checkbox" name="students[]" value="<?= $s['student_id'] ?>" class="stu" style="width: 18px; height: 18px;">
                        <div class="student-info">
                            <span class="student-name"><?= $s['name'] ?></span>
                            <span class="student-id">Roll No: <?= $s['student_id'] ?> | <?= $s['course'] ?></span>
                        </div>
                    </div>
                <?php } } else { echo "<div style='padding:40px; text-align:center; color:#999;'>No students found for this exam filter.</div>"; } ?>
            </div>

            <div class="action-bar">
                <button type="button" id="submitBtn" class="btn-allocate">
                    <i class="fas fa-check-circle"></i> Confirm Allocation
                </button>
                <input type="hidden" name="allocate" value="1">
            </div>
        </form>
    </div>
</div>

<script>
    // 1. Result Alerts
    <?php if($status == "success"): ?>
        Swal.fire({ icon: 'success', title: 'Done!', text: '<?= $msg ?>', confirmButtonColor: '#4361ee' });
    <?php elseif($status == "error"): ?>
        Swal.fire({ icon: 'error', title: 'Error', text: '<?= $msg ?>', confirmButtonColor: '#4361ee' });
    <?php endif; ?>

    // 2. Submit Logic with Spinner
    document.getElementById('submitBtn').addEventListener('click', function(e) {
        const selectedCount = document.querySelectorAll('.stu:checked').length;
        
        if(selectedCount === 0) {
            Swal.fire({ icon: 'warning', title: 'Attention', text: 'Please select at least one student!' });
            return;
        }

        Swal.fire({
            title: 'Confirm Allocation?',
            text: "Total " + selectedCount + " students will receive allotment emails. This might take a few moments.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4361ee',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Allot & Send Mail'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show Loading Spinner
                Swal.fire({
                    title: 'Allocating Seats...',
                    html: 'Processing student data and sending emails. <b>Please do not refresh.</b>',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                document.getElementById('allocationForm').submit();
            }
        });
    });

    // 3. Search & Select All Logic
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.stu').forEach(cb => {
            if(cb.closest('.student-row').style.display !== 'none') cb.checked = this.checked;
        });
    });

    document.getElementById('studentSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        document.querySelectorAll('.student-row').forEach(row => {
            let name = row.querySelector('.student-name').innerText.toLowerCase();
            row.style.display = name.includes(filter) ? 'flex' : 'none';
        });
    });

    // Make row clickable
    document.querySelectorAll('.student-row').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox') {
                const cb = this.querySelector('input[type="checkbox"]');
                cb.checked = !cb.checked;
            }
        });
    });
</script>
</body>
</html>