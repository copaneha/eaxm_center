<?php 
include("../config.php"); 
$id = $_GET['id'];

// Employee ka naam nikalne ke liye taaki confirmation mein dikha sakein
$res = mysqli_query($conn, "SELECT name FROM employees WHERE id=$id");
$data = mysqli_fetch_assoc($res);

if(isset($_POST['confirm_delete'])){
    mysqli_query($conn, "DELETE FROM employees WHERE id=$id");
    header("location:mange_empolyee.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Delete</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #ffffff;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .delete-card {
            background: #fff;
            width: 100%;
            max-width: 400px;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid #eee;
            animation: shake 0.4s ease-in-out;
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(5px); }
            50% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
            100% { transform: translateX(0); }
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: #fff5f5;
            color: #dc3545;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 40px;
            margin: 0 auto 20px;
            border: 2px solid #feb2b2;
        }

        h2 { color: #2d3748; margin: 0 0 10px; font-size: 22px; }
        p { color: #718096; font-size: 15px; line-height: 1.5; margin-bottom: 30px; }
        strong { color: #e53e3e; }

        .btn-group {
            display: flex;
            gap: 15px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
            border: none;
            font-size: 14px;
        }

        .btn-confirm {
            background: #dc3545;
            color: white;
        }

        .btn-confirm:hover {
            background: #c53030;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .btn-cancel {
            background: #edf2f7;
            color: #4a5568;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
        }
    </style>
</head>
<body>

<div class="delete-card">
    <div class="icon-box">
        <i class="fa fa-exclamation-triangle"></i>
    </div>
    <h2>Are you sure?</h2>
    <p>You are about to delete <strong><?php echo $data['name']; ?></strong>. This action cannot be undone.</p>
    
    <form method="post">
        <div class="btn-group">
            <a href="mange_empolyee.php" class="btn btn-cancel">No, Keep it</a>
            <button type="submit" name="confirm_delete" class="btn btn-confirm">Yes, Delete</button>
        </div>
    </form>
</div>

</body>
</html>