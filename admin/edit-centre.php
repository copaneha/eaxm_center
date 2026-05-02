<?php 
include "../config.php"; 
include "sidebar.php"; 

// 1. Get existing data
if(isset($_GET['id'])){
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $res = mysqli_query($conn, "SELECT * FROM exam_centres WHERE id='$id'");
    $data = mysqli_fetch_assoc($res);
    
    if(!$data){
        header("Location:manage-centers.php");
        exit();
    }
}

// 2. Handle Update Logic
if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($conn, $_POST['centre_name']);
    $code = mysqli_real_escape_string($conn, $_POST['centre_code']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact_no']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $update_query = "UPDATE exam_centres SET 
                    centre_name='$name', 
                    centre_code='$code', 
                    address='$address', 
                    city='$city', 
                    contact_no='$contact', 
                    email='$email', 
                    status='$status' 
                    WHERE id='$id'";
    
    if(mysqli_query($conn, $update_query)){
        $status_msg = "success";
    } else {
        $status_msg = "error";
        $err_text = mysqli_error($conn);
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    :root { --primary-blue: #2c52a1; --bg-gray: #f4f7fa; }

    body { font-family: 'Inter', sans-serif; background-color: var(--bg-gray); margin: 0; display: flex; }
    .main-wrapper { flex-grow: 1; padding: 60px 40px; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; margin-top:50px; }
    
    .form-card { 
        background: #fff; width: 100%; max-width: 800px; 
        border-radius: 16px; overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.08); 
    }

    .card-header { background: #281fa7; padding: 25px 30px; color: white; font-size: 20px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
    .card-body { padding: 40px; }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .full-width { grid-column: span 2; }

    .form-group { margin-bottom: 5px; }
    label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 14px; }

    input, textarea, select { 
        width: 100%; padding: 12px 15px; border: 1.5px solid #eee; 
        border-radius: 10px; font-size: 14px; transition: 0.3s;
    }

    input:focus, select:focus, textarea:focus { border-color: var(--primary-blue); outline: none; background: #fcfdff; box-shadow: 0 0 0 4px rgba(40, 31, 167, 0.05); }

    .btn-update { 
        background: #281fa7; color: white; padding: 14px; border: none; 
        border-radius: 10px; cursor: pointer; font-size: 16px; font-weight: 700; 
        width: 100%; transition: 0.3s; margin-top: 20px; box-shadow: 0 4px 12px rgba(40, 31, 167, 0.2);
    }
    .btn-update:hover { background: #1c1ea0; transform: translateY(-2px); }

    .back-btn { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; color: #636e72; text-decoration: none; font-weight: 500; transition: 0.2s; }
    .back-btn:hover { color: var(--primary-blue); }
</style>

<div class="main-wrapper">
    <div style="width: 100%; max-width: 800px;">
        <a href="manage-centers.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
        
        <div class="form-card">
            <div class="card-header">
                <i class="fa-solid fa-pen-to-square"></i> Edit Exam Centre Details
            </div>
            
            <div class="card-body">
                <form method="post">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Centre Name</label>
                            <input type="text" name="centre_name" value="<?php echo $data['centre_name']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Centre Code</label>
                            <input type="text" name="centre_code" value="<?php echo $data['centre_code']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" value="<?php echo $data['city']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="Active" <?php if($data['status'] == 'Active') echo 'selected'; ?>>Active</option>
                                <option value="Inactive" <?php if($data['status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact_no" value="<?php echo $data['contact_no']; ?>">
                        </div>

                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?php echo $data['email']; ?>">
                        </div>

                        <div class="form-group full-width">
                            <label>Full Address</label>
                            <textarea name="address" rows="3"><?php echo $data['address']; ?></textarea>
                        </div>
                    </div>

                    <button name="update" class="btn-update">
                        <i class="fa-solid fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// PHP se data check karke SweetAlert show karega
<?php if(isset($status_msg) && $status_msg == "success"): ?>
    Swal.fire({
        icon: 'success',
        title: 'Updated!',
        text: 'Centre details have been updated successfully.',
        showConfirmButton: false,
        timer: 2000
    }).then(() => {
        window.location.href = 'manage-centers.php';
    });
<?php elseif(isset($status_msg) && $status_msg == "error"): ?>
    Swal.fire({
        icon: 'error',
        title: 'Update Failed',
        text: 'Error: <?php echo $err_text; ?>'
    });
<?php endif; ?>
</script>