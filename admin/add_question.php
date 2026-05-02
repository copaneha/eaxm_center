<?php
include "../config.php";

// Connection check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$error_desc = "";

// --- FORM SUBMISSION LOGIC ---
if (isset($_POST['submit_question'])) {
    $exam_id      = $_POST['exam_id'];
    $exam_name    = $_POST['exam_name']; 
    $subject_val  = $_POST['subject_name']; 
    $marks        = $_POST['marks'];
    $question     = $_POST['question'];
    $a            = $_POST['a'];
    $b            = $_POST['b'];
    $c            = $_POST['c'];
    $d            = $_POST['d'];
    $correct      = $_POST['correct'];

    $sql = "INSERT INTO question_bank (exam_id, exam_name, subject, question, option_a, option_b, option_c, option_d, correct_option, marks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("issssssssi", $exam_id, $exam_name, $subject_val, $question, $a, $b, $c, $d, $correct, $marks);
        if ($stmt->execute()) {
            $message = "success";
        } else {
            $message = "error";
            $error_desc = $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "error";
        $error_desc = $conn->error;
    }
}

// Exams list fetch karein - Table name aur status check karein
$exam_query = mysqli_query($conn, "SELECT exam_id, exam_name, subject_name FROM exams WHERE status = 'Active'");
if (!$exam_query) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary: #2c5ba9; --bg-body: #f4f7fa; }
        body { background-color: var(--bg-body); font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; }
        
        /* Sidebar and Layout Adjustments */
        .main-wrapper { 
            padding: 30px; 
            transition: all 0.3s;
            margin-left: 270px; /* Sidebar space */
             
        }
        
        @media (max-width: 992px) {
            .main-wrapper { margin-left: 0; }
        }

        .card { border: none; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header-box { 
            background: var(--primary); 
            color: white; 
            padding: 15px 20px; 
            border-radius: 12px 12px 0 0; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-top:60px;
           
        }
        .form-label { font-weight: 600; font-size: 0.8rem; color: #555; }
        .option-card { background: #fff; padding: 10px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 10px; }
        .btn-submit { background: var(--primary); border: none; color: white; padding: 12px; font-weight: bold; width: 100%; border-radius: 6px; }
        .btn-submit:hover { opacity: 0.9; }
    </style>
</head>
<body>

<?php 
// Sidebar file check karein ki path sahi hai ya nahi
if(file_exists("sidebar.php")) {
    include("sidebar.php"); 
}
?>

<div class="main-wrapper">
    <div class="card">
        <div class="header-box">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Add New Question</h5>
            <a href="view_questions.php" class="btn btn-sm btn-light"><i class="fas fa-list"></i> View All</a>
        </div>
        
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="exam_id" id="exam_id_input">

                <div class="row mb-3">
                    <div class="col-md-5">
                        <label class="form-label">TARGET EXAM</label>
                        <select name="exam_name" id="exam_select" class="form-select" required onchange="syncSubject()">
                            <option value="">-- Select Exam --</option>
                            <?php while($row = mysqli_fetch_assoc($exam_query)): ?>
                                <option value="<?= $row['exam_name']; ?>" 
                                        data-id="<?= $row['exam_id']; ?>" 
                                        data-subject="<?= $row['subject_name']; ?>">
                                    <?= strtoupper($row['exam_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">SUBJECT</label>
                        <select name="subject_name" id="subject_dropdown" class="form-select" required>
                            <option value="">-- Select Exam First --</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">MARKS</label>
                        <input type="number" name="marks" class="form-control" value="1" min="1" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">QUESTION TEXT</label>
                    <textarea name="question" class="form-control" rows="3" placeholder="Type your question here..." required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="option-card">
                            <label class="text-primary small fw-bold">Option A</label>
                            <input type="text" name="a" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="option-card">
                            <label class="text-primary small fw-bold">Option B</label>
                            <input type="text" name="b" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="option-card">
                            <label class="text-primary small fw-bold">Option C</label>
                            <input type="text" name="c" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="option-card">
                            <label class="text-primary small fw-bold">Option D</label>
                            <input type="text" name="d" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="p-3 mb-3" style="background: #eef9f1; border-radius: 8px; border: 1px dashed #28a745;">
                    <label class="form-label text-success">SELECT CORRECT ANSWER</label>
                    <select name="correct" class="form-select" required>
                        <option value="">-- Select Correct Option --</option>
                        <option value="A">Option A</option>
                        <option value="B">Option B</option>
                        <option value="C">Option C</option>
                        <option value="D">Option D</option>
                    </select>
                </div>

                <button type="submit" name="submit_question" class="btn btn-submit">
                    <i class="fas fa-save me-2"></i> SAVE QUESTION TO DATABASE
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function syncSubject() {
    const examSelect = document.getElementById('exam_select');
    const subjectDropdown = document.getElementById('subject_dropdown');
    const examIdInput = document.getElementById('exam_id_input');
    
    const selectedOption = examSelect.options[examSelect.selectedIndex];
    
    if(selectedOption.value !== "") {
        const subject = selectedOption.getAttribute('data-subject');
        const examId = selectedOption.getAttribute('data-id');
        
        examIdInput.value = examId;
        
        subjectDropdown.innerHTML = `<option value="${subject}">${subject.toUpperCase()}</option>`;
    } else {
        examIdInput.value = "";
        subjectDropdown.innerHTML = '<option value="">-- Select Exam First --</option>';
    }
}

// Success/Error Messages
<?php if($message == "success"): ?>
    Swal.fire({ icon: 'success', title: 'Saved!', text: 'Question added successfully!', timer: 2000, showConfirmButton: false });
<?php elseif($message == "error"): ?>
    Swal.fire({ icon: 'error', title: 'Database Error', text: '<?= addslashes($error_desc) ?>' });
<?php endif; ?>
</script>

</body>
</html>