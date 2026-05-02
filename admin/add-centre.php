<?php 
include "../config.php"; 
// Note: Sidebar and Config inclusion should be handled carefully to avoid header issues
include "sidebar.php"; 

$res = "";
$err_msg = "";

if(isset($_POST['save'])){
    // mysqli_real_escape_string use karein (security ke liye)
    $name = mysqli_real_escape_string($conn, $_POST['centre_name']);
    $code = mysqli_real_escape_string($conn, $_POST['centre_code']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact_no']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "INSERT INTO exam_centres (centre_name, centre_code, address, city, contact_no, email, status) 
              VALUES ('$name', '$code', '$address', '$city', '$contact', '$email', '$status')";
    
    if(mysqli_query($conn, $query)){
        $res = "success";
    } else {
        $res = "error";
        $err_msg = mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Exam Centre</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root { --primary-blue: #2c52a1; --bg-gray: #f0f2f5; }
        body { font-family: 'Segoe UI', sans-serif; background-color: var(--bg-gray); margin: 0; display: flex; }
        .main-wrapper { flex-grow: 1; padding: 40px; display: flex; justify-content: center; margin-top:50px; }
        .form-card { background: #fff; width: 100%; max-width: 700px; border-radius: 12px; padding: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h3 { color: var(--primary-blue); margin-top: 0; font-size: 24px; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 14px;}
        input, textarea, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 15px; }
        .btn-save { background: var(--primary-blue); color: white; padding: 14px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; width: 100%; margin-top: 20px; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #666; text-decoration: none; }
        @media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } .full-width { grid-column: span 1; } }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="form-card">
        <h3><i class="fas fa-plus-circle"></i> Add New Exam Centre</h3>
        
        <form method="post" id="centreForm">
            <div class="form-grid">
                <div class="form-group">
                    <label>Centre Name</label>
                    <input type="text" name="centre_name" placeholder="e.g. National Institute" required>
                </div>

                <div class="form-group">
                    <label>Centre Code</label>
                    <input type="text" name="centre_code" placeholder="e.g. CEN-001" required>
                </div>

                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" placeholder="e.g. Mumbai" required>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" name="contact_no" placeholder="e.g. 9876543210">
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="centre@example.com">
                </div>

                <div class="form-group full-width">
                    <label>Full Address</label>
                    <textarea name="address" rows="3" placeholder="Complete office address..."></textarea>
                </div>
            </div>

            <button name="save" type="submit" class="btn-save">Save Centre Details</button>
            <a href="manage-centers.php" class="back-link">← Back to Centers List</a>
        </form>
    </div>
</div>

<script>
<?php if($res == "success"): ?>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Exam Centre has been added successfully.',
        timer: 2000,
        showConfirmButton: false
    }).then(() => {
        window.location.href = 'manage-centers.php';
    });
<?php elseif($res == "error"): ?>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Something went wrong! <?php echo addslashes($err_msg); ?>'
    });
<?php endif; ?>
</script>

</body>
</html>