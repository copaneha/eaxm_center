<?php
include("../config.php"); 

// 1. UNIQUE COURSES FETCH KARNA
$course_list_query = mysqli_query($conn, "SELECT DISTINCT course FROM students WHERE course IS NOT NULL AND course != ''");

// 2. DYNAMIC CENTRES FETCH KARNA (Table: exam_centres)
$center_list_query = mysqli_query($conn, "SELECT id, centre_name, centre_code, city, address FROM exam_centres WHERE status = 'Active' ORDER BY centre_name ASC");

if(isset($_POST['add_exam'])){
    $course_name = mysqli_real_escape_string($conn, $_POST['course_name']); 
    $centre = mysqli_real_escape_string($conn, $_POST['centre']);
    
    // Total students fetch logic
    $count_query = "SELECT COUNT(*) as total FROM students WHERE course = '$course_name'";
    $count_result = mysqli_query($conn, $count_query);
    $count_data = mysqli_fetch_assoc($count_result);
    $total_students = $count_data['total']; 

    $start_date = $_POST['start_date'];
    $start_time = date("H:i:s", strtotime($_POST['start_time'])); 
    $end_time = date("H:i:s", strtotime($_POST['end_time']));

    $gap_days = (int)$_POST['gap_days'];
    $current_date = new DateTime($start_date);

    foreach($_POST['subjects'] as $key => $subject_val) {
        $subject_name = mysqli_real_escape_string($conn, $subject_val);
        $date_str = $current_date->format('Y-m-d');

        // Note: Yahan 'centre' column mein hum centre_name save kar rahe hain
        $sql_exam = "INSERT INTO exams (exam_name, subject_name, exam_date, exam_time, exam_end_time, centre, total_students, status)
                     VALUES ('$course_name', '$subject_name', '$date_str', '$start_time', '$end_time', '$centre', '$total_students', 'Active')";
        
        mysqli_query($conn, $sql_exam);
        $current_date->modify("+$gap_days days");
    }
    
    echo "<script>window.location.href='manage_exam.php?status=success';</script>";
    exit();
}
?>

<?php include("sidebar.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Time-Table Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --bg: #f8f9fc;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        body {
            background-color: var(--bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #2d3436;
            margin: 0;
        }

        .main-content { 
            padding: 20px; 
            margin-left: 250px; 
            transition: 0.3s;
        }

        @media (max-width: 991px) {
            .main-content { margin-left: 0; padding: 15px; }
        }

        .form-container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            max-width: 1000px;
            margin: auto;
            border: 1px solid rgba(0,0,0,0.03);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 30px 20px;
            color: white;
            text-align: center;
        }

        .form-header h3 { font-size: 1.5rem; font-weight: 700; margin-bottom: 5px; }

        .config-section {
            background: #fdfdfd;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            border: 1.5px solid #f1f3f5;
        }

        .form-label { font-weight: 600; font-size: 0.8rem; color: #4b5563; text-transform: uppercase; }

        .subject-row {
            background: #ffffff;
            padding: 12px 15px;
            border: 1px solid #f1f3f5;
            border-radius: 12px;
            margin-bottom: 10px;
            transition: 0.2s;
        }

        .subject-row:hover { border-color: var(--primary); }

        .count-badge {
            width: 35px; height: 35px;
            background: #f1f3f5;
            color: var(--primary);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
        }

        .btn-generate {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white; border: none; padding: 15px;
            border-radius: 12px; font-weight: 700; width: 100%;
            margin-top: 15px; transition: 0.3s;
        }

        .btn-generate:hover { opacity: 0.9; transform: translateY(-2px); color: white; }

        .remove-icon { color: #ff7675; cursor: pointer; font-size: 1.2rem; }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="form-container">
        <div class="form-header">
            <h3><i class="fa fa-magic me-2"></i> Scheduler Pro</h3>
            <p class="mb-0 d-none d-sm-block">Auto-generate professional examination time-tables.</p>
        </div>

        <form method="post" class="p-3 p-md-5">
            <div class="config-section">
                <h6 class="mb-4 text-primary fw-bold"><i class="fa fa-cog me-2"></i>Step 1: Exam Configuration</h6>
                
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-4">
                        <label class="form-label">Choose Course</label>
                        <select name="course_name" class="form-select" required>
                            <option value="">-- Select Course --</option>
                            <?php 
                            mysqli_data_seek($course_list_query, 0); 
                            while($row = mysqli_fetch_assoc($course_list_query)) {
                                echo "<option value='".$row['course']."'>".strtoupper($row['course'])."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-12 col-md-6 col-lg-4">
                        <label class="form-label">Exam Centre</label>
                        <select name="centre" class="form-select" required>
                            <option value="">-- Select Centre --</option>
                            <?php 
                            if($center_list_query && mysqli_num_rows($center_list_query) > 0) {
                                while($c_row = mysqli_fetch_assoc($center_list_query)) {
                                    // Yahan Aapki Table ke hisaab se formatting hai:
                                    // Example: JS ITI (Code: 2_) - VARANASI
                                    $display_text = strtoupper($c_row['centre_name']) . " (" . $c_row['centre_code'] . ") - " . strtoupper($c_row['city']);
                                    echo "<option value='".htmlspecialchars($c_row['centre_name'])."'>".$display_text."</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-12 col-md-6 col-lg-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="col-6 col-md-4">
                        <label class="form-label">Day Gap</label>
                        <input type="number" name="gap_days" class="form-control" value="1" min="0">
                    </div>

                    <div class="col-6 col-md-4">
                        <label class="form-label">Session Start</label>
                        <input type="time" name="start_time" class="form-control" value="10:00">
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label">Session End</label>
                        <input type="time" name="end_time" class="form-control" value="13:00">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-primary fw-bold"><i class="fa fa-list me-2"></i>Step 2: Subjects</h6>
                <button type="button" class="btn btn-sm btn-outline-primary px-3" onclick="addSubject()">
                    <i class="fa fa-plus me-1"></i>Add Subject
                </button>
            </div>

            <div id="subject-list">
                <div class="subject-row">
                    <div class="row align-items-center g-2">
                        <div class="col-auto">
                            <div class="count-badge">1</div>
                        </div>
                        <div class="col">
                            <input type="text" name="subjects[]" class="form-control border-0 shadow-none bg-transparent" placeholder="Enter Subject Name" required>
                        </div>
                        <div class="col-auto text-end" style="width: 40px;">
                            <i class="fa fa-times-circle remove-icon" onclick="removeSubject(this)"></i>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" name="add_exam" class="btn btn-generate shadow">
                <i class="fa fa-check-double me-2"></i> GENERATE TIME-TABLE
            </button>
        </form>
    </div>
</div>

<script>
    function addSubject() {
        const container = document.getElementById('subject-list');
        const count = document.querySelectorAll('.subject-row').length + 1;
        const div = document.createElement('div');
        div.className = 'subject-row';
        div.innerHTML = `
            <div class="row align-items-center g-2">
                <div class="col-auto"><div class="count-badge">${count}</div></div>
                <div class="col"><input type="text" name="subjects[]" class="form-control border-0 shadow-none bg-transparent" placeholder="Enter Subject Name" required></div>
                <div class="col-auto text-end" style="width: 40px;"><i class="fa fa-times-circle remove-icon" onclick="removeSubject(this)"></i></div>
            </div>`;
        container.appendChild(div);
    }

    function removeSubject(btn) {
        if(document.querySelectorAll('.subject-row').length > 1) {
            btn.closest('.subject-row').remove();
            document.querySelectorAll('.count-badge').forEach((el, index) => el.innerText = index + 1);
        } else {
            alert("At least one subject is required.");
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>