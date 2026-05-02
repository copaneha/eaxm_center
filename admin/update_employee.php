<?php 
include("../config.php"); 
$id = $_GET['id'];
$res = mysqli_query($conn,"SELECT * FROM employees WHERE id=$id");
$data = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Employee | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary-blue: #1a3a5f;
            --light-blue: #3498db;
            --accent-green: #2ecc71;
            --text-dark: #2c3e50;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f7f9;
            margin: 0;
           
        }

        /* Sidebar ke saath alignment ke liye */
        .main-content {
            flex: 1;
            margin-left: 50px; /* Sidebar width */
            display: flex;
            justify-content: center;
            padding: 50px 20px;
        }

        .form-card {
            background: #ffffff;
            width: 100%;
            max-width: 550px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: fadeIn 0.5s ease;
            height: fit-content;
              margin-top:50px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .form-header {
            background: linear-gradient(135deg, #1a3a5f 0%, #2b5a9e 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }

        .form-header i { font-size: 45px; margin-bottom: 10px; color: #ff9f43; }
        .form-header h2 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .form-header p { margin: 5px 0 0; opacity: 0.9; font-size: 14px; }

        .form-body { padding: 30px 40px; }

        .input-group { margin-bottom: 22px; position: relative; }
        .input-group label { display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark); font-size: 13px; }
        
        .input-group input {
            width: 100%;
            padding: 12px 15px 12px 42px;
            border: 1px solid #dcdde1;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            transition: all 0.3s;
            background: #fafafa;
        }

        .input-group i.input-icon {
            position: absolute;
            left: 15px;
            top: 38px;
            color: var(--light-blue);
        }

        .input-group input:focus {
            background: #fff;
            border-color: var(--light-blue);
            outline: none;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.2);
        }

        .row { display: flex; gap: 20px; }
        .col { flex: 1; }

        .btn-update {
            width: 100%;
            background: var(--accent-green);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            text-transform: uppercase;
        }

        .btn-update:hover {
            background: #27ae60;
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
            transform: translateY(-2px);
        }

        .cancel-link {
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
            <i class="fa fa-user-edit"></i>
            <h2>Edit Details</h2>
            <p>Updating record for: <strong><?php echo htmlspecialchars($data['name']); ?></strong></p>
        </div>

        <div class="form-body">
            <form method="post">
                <div class="input-group">
                    <label>Employee Name</label>
                    <i class="fa fa-user input-icon"></i>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" required>
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <i class="fa fa-envelope input-icon"></i>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="input-group">
                            <label>Designation</label>
                            <i class="fa fa-briefcase input-icon"></i>
                            <input type="text" name="role" value="<?php echo htmlspecialchars($data['role']); ?>">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group">
                            <label>Centre / Lab</label>
                            <i class="fa fa-building input-icon"></i>
                            <input type="text" name="centre" value="<?php echo htmlspecialchars($data['centre']); ?>">
                        </div>
                    </div>
                </div>

                <button type="submit" name="update" class="btn-update">
                    Update Record <i class="fa fa-check-circle"></i>
                </button>

                <a href="mange_empolyee.php" class="cancel-link">Cancel & Go Back</a>
            </form>
        </div>
    </div>
</div>

<?php
if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $centre = mysqli_real_escape_string($conn, $_POST['centre']);

    $update_query = "UPDATE employees SET 
                    name='$name', 
                    email='$email', 
                    role='$role', 
                    centre='$centre' 
                    WHERE id=$id";

    if(mysqli_query($conn, $update_query)){
        // SweetAlert Execution
        echo "
        <script>
            Swal.fire({
                title: 'Updated!',
                text: 'Employee details updated successfully.',
                icon: 'success',
                confirmButtonColor: '#2ecc71'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = 'mange_empolyee.php';
                }
            });
        </script>";
    } else {
        echo "<script>Swal.fire('Error', 'Update failed', 'error');</script>";
    }
}
?>

</body>
</html>