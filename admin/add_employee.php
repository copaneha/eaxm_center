<?php include("../config.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee | Premium Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary-blue: #1a3a5f;
            --light-blue: #3498db;
            --accent-orange: #ff9f43;
            --text-dark: #2c3e50;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f7f9;
            margin: 0;
          
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            padding: 40px 20px;
        }

        .form-card {
            background: #ffffff;
            width: 100%;
            max-width: 550px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: slideUp 0.5s ease;
            height: fit-content;
            margin-top:50px;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-header {
            background: linear-gradient(135deg, #1a3a5f 0%, #2b5a9e 100%);
            padding: 30px 50px;
            text-align: center;
            color: white;
        }

        .form-header h2 { margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 1.5px; }
        .form-header p { margin: 8px 0 0; font-size: 13px; opacity: 0.8; }
        .form-header i { font-size: 35px; margin-bottom: 10px; color: var(--accent-orange); }

        .form-body { padding: 30px 40px; }

        .input-group { margin-bottom: 20px; position: relative; }
        .input-group label { display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark); font-size: 13px; }
        
        .input-group input {
            width: 100%;
            padding: 12px 15px 12px 42px;
            border: 1px solid #dcdde1;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            transition: all 0.3s;
        }

        .input-group i.input-icon {
            position: absolute;
            left: 15px;
            top: 38px;
            color: var(--light-blue);
        }

        .row { display: flex; gap: 20px; }
        .col { flex: 1; }

        input[type="file"] { padding: 8px; background: #f8f9fa; border: 1px dashed #3498db; }

        .btn-submit {
            width: 100%;
            background: var(--primary-blue);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit:hover { background: #0d253f; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #7f8c8d;
            text-decoration: none;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; }
            .row { flex-direction: column; gap: 0; }
        }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main-content">
    <div class="form-card">
        <div class="form-header">
            <i class="fa fa-user-plus"></i>
            <h2>Employee Registration</h2>
            <p>Enter details to create a new profile</p>
        </div>

        <div class="form-body">
            <form method="post" enctype="multipart/form-data">
                <div class="input-group">
                    <label>Full Name</label>
                    <i class="fa fa-user input-icon"></i>
                    <input type="text" name="name" placeholder="John Doe" required>
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <i class="fa fa-envelope input-icon"></i>
                    <input type="email" name="email" placeholder="john@example.com" required>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="input-group">
                            <label>Role</label>
                            <i class="fa fa-briefcase input-icon"></i>
                            <input type="text" name="role" placeholder="Manager">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group">
                            <label>Centre / Lab</label>
                            <i class="fa fa-building input-icon"></i>
                            <input type="text" name="centre" placeholder="Main Lab">
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <label>Account Password</label>
                    <i class="fa fa-lock input-icon"></i>
                    <input type="password" name="password" placeholder="Create a strong password" required>
                </div>

                <div class="input-group">
                    <label>Profile Image</label>
                    <input type="file" name="photo" accept="image/*" required>
                </div>

                <button type="submit" name="save" class="btn-submit">
                    SAVE EMPLOYEE <i class="fa fa-check-circle"></i>
                </button>

                <a href="mange_empolyee.php" class="back-link">
                    <i class="fa fa-arrow-left"></i> Back to Dashboard
                </a>
            </form>
        </div>
    </div>
</div>

<?php
if(isset($_POST['save'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $centre = mysqli_real_escape_string($conn, $_POST['centre']);
    
    // MD5 Encryption lagaya gaya hai
    $password = md5($_POST['password']); 
    
    $photo = $_FILES['photo']['name'];
    $temp_name = $_FILES['photo']['tmp_name'];
    $folder = "../image/".$photo;

    if(move_uploaded_file($temp_name, $folder)){
        $sql = "INSERT INTO employees(name,email,role,centre,photo,password) VALUES('$name','$email','$role','$centre','$photo','$password')";
        
        if(mysqli_query($conn, $sql)){
            echo "
            <script>
                Swal.fire({
                    title: 'Success!',
                    text: 'Employee record has been saved with MD5 encryption.',
                    icon: 'success',
                    confirmButtonColor: '#1a3a5f'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location = 'mange_empolyee.php';
                    }
                });
            </script>";
        } else {
            echo "<script>Swal.fire('Error', 'Something went wrong', 'error');</script>";
        }
    }
}
?>

</body>
</html>