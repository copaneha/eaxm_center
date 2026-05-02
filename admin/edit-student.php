<?php
include("../config.php");

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM students WHERE id=$id"));

$status = "";

if (isset($_POST['update'])) {
    $name   = mysqli_real_escape_string($conn, $_POST['name']);
    $phone  = mysqli_real_escape_string($conn, $_POST['phone']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $roll   = mysqli_real_escape_string($conn, $_POST['roll']);

    // Photo Handling
    if ($_FILES['photo']['name'] != "") {
        // Agar nayi photo select ki hai
        $photo_name = time() . "_" . $_FILES['photo']['name'];
        $tmp = $_FILES['photo']['tmp_name'];
        move_uploaded_file($tmp, "../image/" . $photo_name);
    } else {
        // Agar nayi photo select nahi ki, to purani wali hi rakhein
        $photo_name = $data['photo'];
    }

    $sql = "UPDATE students SET 
            name='$name', 
            email='$email', 
            phone='$phone', 
            roll_no='$roll', 
            course='$course', 
            photo='$photo_name' 
            WHERE id=$id";
    
    if (mysqli_query($conn, $sql)) {
        $status = "success";
    } else {
        $status = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #1d4ce9;
            --bg: #f8f9fc;
            --white: #ffffff;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--bg);
            margin: 0;
            display: flex;
        }

        .main-wrapper {
            flex-grow: 1;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }

        .form-card {
            background: var(--white);
            width: 100%;
            max-width: 600px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-top: 50px;
        }

        .card-header {
            background: var(--primary);
            padding: 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-back {
            text-decoration: none;
            color: #ffeb3b; /* Stylish Yellow */
            font-size: 0.9rem;
            border: 1px solid white;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: 600;
        }

        form { padding: 30px; }

        .input-box { margin-bottom: 18px; }

        .input-box label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }

        input[type="text"], input[type="email"], input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .current-photo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 2px solid #ddd;
        }

        .grid-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        button.btn-update {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button.btn-update:hover { background: #dd230a; }

        @media (max-width: 600px) {
            .grid-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main-wrapper">
    <div class="form-card">
        <div class="card-header">
            <h2>Edit Student Details</h2>
            <a href="Manage-Student.php" class="btn-back">← Back</a>
        </div>

        <form method="post" enctype="multipart/form-data">
            <div class="input-box">
                <label>Full Name</label>
                <input type="text" name="name" value="<?= $data['name'] ?>" required>
            </div>

            <div class="input-box">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= $data['email'] ?>" required>
            </div>

            <div class="grid-row">
                <div class="input-box">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?= $data['phone'] ?>" required>
                </div>
                <div class="input-box">
                    <label>Roll Number</label>
                    <input type="text" name="roll" value="<?= $data['roll_no'] ?>" required>
                </div>
            </div>

            <div class="input-box">
                <label>Course</label>
                <input type="text" name="course" value="<?= $data['course'] ?>" required>
            </div>

            <div class="input-box">
                <label>Change Photo (Leave blank if no change)</label><br>
                <img src="../image/<?= $data['photo'] ?>" class="current-photo" alt="Current Photo">
                <input type="file" name="photo" accept="image/*">
            </div>

            <button type="submit" name="update" class="btn-update">Update Student Record</button>
        </form>
    </div>
</div>

<?php if($status == "success"): ?>
<script>
    Swal.fire({
        title: 'Updated!',
        text: 'Student record has been updated successfully.',
        icon: 'success',
        confirmButtonColor: '#1d4ce9'
    }).then((result) => {
        window.location.href = 'Manage-Student.php';
    });
</script>
<?php endif; ?>

</body>
</html>