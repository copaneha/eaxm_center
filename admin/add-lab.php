<?php 
include "../config.php"; 
include "sidebar.php"; 

if(isset($_POST['save'])){
    // Centre ID ek hi baar select hogi
    $centre = mysqli_real_escape_string($conn, $_POST['centre_id']);
    
    // Baki fields arrays hain
    $labs    = $_POST['lab_name'];
    $codes   = $_POST['lab_code'];
    $floors  = $_POST['floor_no'];
    $totals  = $_POST['total'];
    $statuses = $_POST['status'];

    $success_count = 0;
    $error_occurred = false;

    // Loop through each lab row
    foreach($labs as $key => $val) {
        // Agar lab name khali nahi hai, tabhi insert karein
        if(!empty($val)) {
            $lab_name = mysqli_real_escape_string($conn, $labs[$key]);
            $lab_code = mysqli_real_escape_string($conn, $codes[$key]);
            $floor    = mysqli_real_escape_string($conn, $floors[$key]);
            $total    = mysqli_real_escape_string($conn, $totals[$key]);
            $status   = mysqli_real_escape_string($conn, $statuses[$key]);

            $query = "INSERT INTO labs (centre_id, lab_name, lab_code, floor_no, total_computers, status) 
                      VALUES ('$centre', '$lab_name', '$lab_code', '$floor', '$total', '$status')";
            
            if(mysqli_query($conn, $query)){
                $success_count++;
            } else {
                $error_occurred = true;
                $err_msg = mysqli_error($conn);
            }
        }
    }

    if($success_count > 0){
        $res = "success";
        $msg = "$success_count Labs Added Successfully";
    } else {
        $res = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Add Labs | Exam Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');
        :root { --primary: #2c52a1; --bg: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg); margin: 0; padding: 20px; display: flex; justify-content: center; }
        .form-container { background: #fff;margin-top:70px; padding: 2rem; border-radius: 16px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); width: 100%; max-width: 900px; border: 1px solid #edf2f7; }
        .form-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .lab-row { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr; gap: 10px; background: #fcfcfc; padding: 15px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #f0f0f0; }
        label { font-weight: 600; font-size: 12px; color: #555; display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 10px; border: 1.5px solid #edf2f7; border-radius: 8px; font-size: 13px; box-sizing: border-box; }
        .section-title { font-size: 14px; font-weight: bold; color: var(--primary); margin-bottom: 15px; }
        button { width: 100%; padding: 14px; background-color: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; margin-top: 20px; transition: 0.3s; }
        button:hover { background-color: #1e3a8a; }
    </style>
</head>
<body>

<div class="form-container">
    <div class="form-header">
        <a href="manage-labs.php" style="text-decoration: none; color: #666;"><i class="fa-solid fa-arrow-left"></i> Back</a>
        <h3 style="margin:0; color:var(--primary);">Bulk Add Labs</h3>
    </div>

    <form method="post">
        <div style="margin-bottom: 25px; background: #f0f4ff; padding: 20px; border-radius: 10px;">
            <label><i class="fa-solid fa-building"></i> Select Exam Centre (Common for all labs below)</label>
            <select name="centre_id" required>
                <option value="">-- Choose Centre --</option>
                <?php
                $c = mysqli_query($conn,"SELECT * FROM exam_centres");
                while($r=mysqli_fetch_assoc($c)){
                    echo "<option value='{$r['id']}'>{$r['centre_name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="section-title">Lab Details (Fill up to 3 labs)</div>

        <?php for($i=1; $i<=3; $i++): ?>
        <div class="lab-row">
            <div>
                <label>Lab Name <?php echo $i; ?></label>
                <input type="text" name="lab_name[]" placeholder="IT Lab <?php echo $i; ?>" <?php if($i==1) echo 'required'; ?>>
            </div>
            <div>
                <label>Code</label>
                <input type="text" name="lab_code[]" placeholder="L-<?php echo $i; ?>">
            </div>
            <div>
                <label>Floor</label>
                <input type="text" name="floor_no[]" placeholder="Floor">
            </div>
            <div>
                <label>Total PCs</label>
                <input type="number" name="total[]" placeholder="0">
            </div>
            <div>
                <label>Status</label>
                <select name="status[]">
                    <option value="Active">Active</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
            </div>
        </div>
        <?php endfor; ?>

        <button name="save" type="submit"><i class="fa-solid fa-save"></i> Save All Lab Records</button>
    </form>
</div>

<script>
<?php if(isset($res) && $res == "success"): ?>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '<?php echo $msg; ?>',
        timer: 2500,
        showConfirmButton: false
    }).then(() => { window.location.href = 'manage-labs.php'; });
<?php elseif(isset($res) && $res == "error"): ?>
    Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong!' });
<?php endif; ?>
</script>

</body>
</html>