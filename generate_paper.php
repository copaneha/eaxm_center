<?php
session_start();
include "config.php";

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Student session check
if (!isset($_SESSION['student_id'])) {
    $_SESSION['student_id'] = 1; 
}

// GET se ID le rahe hain, default 1
$examId = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : 1;
$studentId = $_SESSION['student_id'];

/**
 * FIX 1: Table columns update kiye gaye hain
 * Query mein 'id' ki jagah 'exam_id' use kiya hai
 */
$exam_info_query = "SELECT * FROM exams WHERE exam_id = '$examId'";
$exam_info = mysqli_query($conn, $exam_info_query);

if (!$exam_info || mysqli_num_rows($exam_info) == 0) {
    die("<div style='color:red; padding:20px; border:1px solid red; font-family:sans-serif;'>
            <strong>Error:</strong> Exam ID '$examId' not found in 'exams' table. 
            <br>Aapki table mein 'exam_id' column check karein.
         </div>");
}

$exam_data = mysqli_fetch_assoc($exam_info);

/**
 * FIX 2: Aapki table mein column 'exam_end_time' hai, 'end_time' nahi.
 * Hum date aur time ko combine kar rahe hain countdown ke liye.
 */
$examDate = $exam_data['exam_date']; // e.g. 2026-03-23
$examEndTime = $exam_data['exam_end_time']; // e.g. 03:50:00
$fullEndDateTime = $examDate . " " . $examEndTime; 

// 2. Questions Fetch Karein
$query = "SELECT * FROM question_bank WHERE exam_id = '$examId' ORDER BY id ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database Error in question_bank: " . mysqli_error($conn));
}

$questions = mysqli_fetch_all($result, MYSQLI_ASSOC);
$totalQuestions = count($questions);

if($totalQuestions == 0) { 
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h2>No questions found for Exam ID: $examId</h2>
            <p>Please add questions to the 'question_bank' table first.</p>
         </div>"); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBT Exam Portal | 2026</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root { --primary: #2c3e50; --accent: #3498db; --success: #27ae60; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; height: 100vh; overflow: hidden; }

        .exam-header {
            background: var(--primary); color: white; padding: 12px 25px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        #timer { font-weight: 700; font-size: 1.2rem; background: #e74c3c; padding: 5px 15px; border-radius: 5px; }

        .main-wrapper { display: flex; height: calc(100vh - 65px); padding: 20px; gap: 20px; }
        .question-section { flex: 1; overflow-y: auto; }
        .sidebar-section { width: 320px; }

        .q-container { display: none; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .active-q { display: block; animation: fadeIn 0.3s; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .options-label {
            display: flex; align-items: center; padding: 15px;
            border: 2px solid #edf2f7; margin-bottom: 12px;
            cursor: pointer; border-radius: 10px; transition: 0.2s;
        }
        .options-label:hover { background: #f8f9fa; border-color: var(--accent); }
        input[type="radio"] { width: 20px; height: 20px; margin-right: 15px; }

        .q-palette { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; }
        .q-num {
            width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;
            border: 1px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 600;
        }
        .q-num.active { background: var(--accent); color: white; border-color: var(--accent); }
        .q-num.answered { background: var(--success); color: white; border-color: var(--success); }

        footer { background: white; padding: 15px; border-radius: 10px; margin-top: 20px; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<header class="exam-header">
    <div><h5 class="mb-0"><i class="fa-solid fa-laptop-code me-2"></i> <?php echo strtoupper($exam_data['exam_name'] ?? 'CBT Exam'); ?></h5></div>
    <div id="timer">00:00:00</div>
</header>

<div class="main-wrapper">
    <div class="question-section">
        <form id="examForm" method="POST" action="submitted.php">
            <input type="hidden" name="exam_id" value="<?php echo $examId; ?>">

            <?php foreach($questions as $index => $row): ?>
            <div class="q-container <?php echo $index==0?'active-q':''; ?>" id="qbox-<?php echo $index; ?>">
                <div class="d-flex justify-content-between border-bottom mb-4 pb-2">
                    <span class="badge bg-info text-dark"><?php echo strtoupper($row['subject']); ?></span>
                    <span class="fw-bold">Question <?php echo ($index+1); ?> of <?php echo $totalQuestions; ?></span>
                </div>
                
                <h4 class="mb-4"><?php echo htmlspecialchars($row['question']); ?></h4>
                
                <div class="options-list">
                    <?php foreach(['a','b','c','d'] as $o): ?>
                    <label class="options-label">
                        <input type="radio" name="ans[<?php echo $row['id']; ?>]" value="<?php echo strtoupper($o); ?>" onchange="markAnswered(<?php echo $index; ?>)"> 
                        <span><strong><?php echo strtoupper($o); ?>)</strong> <?php echo htmlspecialchars($row['option_'.$o]); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <footer>
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" id="prevBtn" onclick="moveBack()" disabled>Previous</button>
                    <div>
                        <button type="button" class="btn btn-success me-2" onclick="confirmSubmit()">Submit Exam</button>
                        <button type="button" class="btn btn-primary" id="nextBtn" onclick="moveNext()">Save & Next</button>
                    </div>
                </div>
            </footer>
        </form>
    </div>

    <div class="sidebar-section">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Question Palette</div>
            <div class="card-body">
                <div class="q-palette">
                    <?php for($i=0; $i<$totalQuestions; $i++): ?>
                        <div class="q-num <?php echo $i==0?'active':''; ?>" id="p-<?php echo $i; ?>" onclick="jumpTo(<?php echo $i; ?>)">
                            <?php echo ($i+1); ?>
                        </div>
                    <?php endfor; ?>
                </div>
                <hr>
                <div class="small text-muted">
                    <div class="mb-1"><i class="fa-solid fa-square text-success"></i> Answered</div>
                    <div><i class="fa-solid fa-square text-primary"></i> Current</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let current = 0;
    const total = <?php echo $totalQuestions; ?>;
    
    // Timer Logic: Date aur Time combine karke
    const targetTime = new Date("<?php echo $fullEndDateTime; ?>").getTime();

    const timerInterval = setInterval(function() {
        const now = new Date().getTime();
        const distance = targetTime - now;

        if (distance < 0) {
            clearInterval(timerInterval);
            document.getElementById("timer").innerHTML = "TIME UP!";
            document.getElementById("examForm").submit();
            return;
        }

        const h = Math.floor((distance / (1000 * 60 * 60)));
        const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const s = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("timer").innerHTML = 
            (h<10?"0"+h:h) + ":" + (m<10?"0"+m:m) + ":" + (s<10?"0"+s:s);
    }, 1000);

    function updateUI() {
        document.querySelectorAll('.q-container').forEach(q => q.classList.remove('active-q'));
        document.getElementById('qbox-'+current).classList.add('active-q');
        document.querySelectorAll('.q-num').forEach(n => n.classList.remove('active'));
        document.getElementById('p-'+current).classList.add('active');
        document.getElementById('prevBtn').disabled = (current === 0);
        document.getElementById('nextBtn').style.display = (current === total - 1) ? 'none' : 'inline-block';
    }

    function moveNext() { if(current < total - 1) { current++; updateUI(); } }
    function moveBack() { if(current > 0) { current--; updateUI(); } }
    function jumpTo(n) { current = n; updateUI(); }

    function markAnswered(index) {
        document.getElementById('p-'+index).classList.add('answered');
    }

    function confirmSubmit() {
        Swal.fire({
            title: 'Submit Exam?',
            text: "Final submit ke baad badlav nahi honge!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#27ae60',
            confirmButtonText: 'Yes, Submit!'
        }).then((result) => {
            if (result.isConfirmed) { document.getElementById("examForm").submit(); }
        });
    }
</script>

</body>
</html>