<?php
include("../config.php");

$exam_id   = $_POST['exam_id']   ?? '';
$exam_name = $_POST['exam_name'] ?? '';

if($exam_id == '' && $exam_name == ''){
    echo "Select exam first";
    exit;
}

/* ================= exam_id logic ================= */

if($exam_id != ''){

    $exam_query = "SELECT course, exam_name FROM exams WHERE exam_id = '$exam_id'";
    $exam_info = mysqli_query($conn, $exam_query);

    if (!$exam_info) {
        die("Exam Query Failed: " . mysqli_error($conn));
    }

    $exam_data = mysqli_fetch_assoc($exam_info);

    // agar course empty hai to exam_name use karo
    $course = $exam_data['course'] ?: $exam_data['exam_name'];

}

/* ================= exam_name direct ================= */

else{
    $course = $exam_name;
}

/* ================= students load ================= */

$q_text = "SELECT * FROM students WHERE course = '$course'";

$q = mysqli_query($conn, $q_text);

if (!$q) {
    die("Students Query Failed: " . mysqli_error($conn));
}

if(mysqli_num_rows($q) == 0){
    echo "<b style='color:red'>No students found</b>";
    exit;
}

while($row = mysqli_fetch_assoc($q)){
?>
<div style="padding:10px;border-bottom:1px solid #ddd;">
    <strong><?= htmlspecialchars($row['name']) ?></strong>
    (Roll: <?= htmlspecialchars($row['roll_no']) ?>)
</div>
<?php } ?>