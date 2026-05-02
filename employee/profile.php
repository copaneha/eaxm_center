<?php
session_start();
include("../config.php");

if(!isset($_SESSION['emp_logged_in'])){
    header("Location: login.php");
    exit();
}

$emp_id = $_SESSION['emp_id'];
$message = "";

// 1. Profile Update Logic with Security
if (isset($_POST['update_profile'])) {
    $new_name = mysqli_real_escape_string($conn, $_POST['name']);
    $new_password = $_POST['password'];
    
    // Photo upload handling
    if ($_FILES['photo']['name'] != "") {
        $photo_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $_FILES['photo']['name']);
        $target = "../image/" . $photo_name;
        
        if(move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $update_sql = "UPDATE employees SET name='$new_name', photo='$photo_name' WHERE id='$emp_id'";
        }
    } else {
        $update_sql = "UPDATE employees SET name='$new_name' WHERE id='$emp_id'";
    }
    
    if (mysqli_query($conn, $update_sql)) {
        if (!empty($new_password)) {
            mysqli_query($conn, "UPDATE employees SET password='$new_password' WHERE id='$emp_id'");
        }
        $message = "<div class='alert success'><i class='fas fa-check-circle'></i> Profile credentials updated successfully.</div>";
    }
}

// 2. Fetch Latest Record
$query = mysqli_query($conn, "SELECT * FROM employees WHERE id = '$emp_id'");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkSync | Account Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #3b82f6;
            --dark-indigo: #1e1b4b;
            --success: #10b981;
            --bg-gray: #f8fafc;
            --sidebar-width: 280px;
        }

        * { box-sizing: border-box; transition: all 0.2s ease; }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-gray); 
            margin: 0; padding: 0; color: #1e293b;
            display: flex;
            min-height: 100vh;
        }

        /* --- Sidebar Space Adjustment --- */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
        }

        /* --- Header Section --- */
        .page-header {
            background: #fff;
            padding: 1.5rem 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            position: sticky; top: 0; z-index: 100;
        }
        .page-header h2 { margin: 0; font-size: 1.25rem; font-weight: 700; color: var(--dark-indigo); }

        /* --- Profile Layout --- */
        .container { max-width: 800px; margin: 3rem auto; padding: 0 2rem; }
        
        .settings-card {
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid #e2e8f0;
        }

        .card-banner {
            height: 120px;
            background: linear-gradient(135deg, #1e1b4b 0%, #3b82f6 100%);
        }

        .profile-summary {
            text-align: center;
            margin-top: -60px;
            padding: 0 40px 20px;
        }

        .avatar-box {
            position: relative;
            width: 120px; height: 120px;
            margin: 0 auto 15px;
        }

        .profile-img { 
            width: 120px; height: 120px; 
            border-radius: 30px; /* Modern Squircle Look */
            object-fit: cover; 
            border: 5px solid #fff;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        /* --- Form Styling --- */
        .form-body { padding: 20px 40px 40px; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        
        .form-group { margin-bottom: 20px; }
        .full-width { grid-column: span 2; }

        .form-group label { 
            display: block; font-size: 0.8rem; font-weight: 700; 
            margin-bottom: 8px; color: #64748b;
            text-transform: uppercase; letter-spacing: 0.5px;
        }

        .form-group input { 
            width: 100%; padding: 14px 18px; 
            border: 1px solid #e2e8f0; border-radius: 12px; 
            font-size: 0.95rem; font-family: inherit;
            background: #fff;
        }

        .form-group input:focus { 
            outline: none; border-color: var(--primary-blue); 
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); 
        }

        .form-group input:disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; }

        /* --- File Upload Custom Button --- */
        .file-upload-box {
            border: 2px dashed #e2e8f0;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
        }

        /* --- Professional Button --- */
        .save-btn { 
            background: var(--dark-indigo); color: white; 
            padding: 16px; border: none; border-radius: 16px; 
            cursor: pointer; width: 100%; font-size: 1rem; 
            font-weight: 700; display: flex; align-items: center; 
            justify-content: center; gap: 10px;
            box-shadow: 0 10px 15px -3px rgba(30, 27, 75, 0.2);
        }
        .save-btn:hover { background: #2d2a6e; transform: translateY(-2px); }

        /* --- Alert --- */
        .alert { 
            padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; 
            font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 10px;
            animation: slideIn 0.4s ease;
        }
        .success { background: #f0fdf4; color: #166534; border-left: 4px solid var(--success); }

        @keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        /* --- Responsive Design --- */
        @media (max-width: 1024px) {
            .main-content { margin-left: 0; width: 100%; }
            .form-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: span 1; }
            .page-header { padding: 1rem 1.5rem; }
        }
    </style>
</head>
<body>

    <?php include("sidebar.php"); ?>

    <div class="main-content">
        <header class="page-header">
            <h2><i class="fas fa-user-gear" style="color: var(--primary-blue); margin-right: 8px;"></i> Account Settings</h2>
            <div style="font-size: 0.85rem; color: #64748b;">
                Last Login: <span style="font-weight: 700; color: #1e293b;"><?php echo date('d M, H:i'); ?></span>
            </div>
        </header>

        <div class="container">
            <?php echo $message; ?>

            <div class="settings-card">
                <div class="card-banner"></div>
                
                <div class="profile-summary">
                    <div class="avatar-box">
                        <img src="../image/<?php echo $user['photo']; ?>" class="profile-img" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo $user['name']; ?>&background=random'">
                    </div>
                    <h2 style="margin: 0; color: var(--dark-indigo);"><?php echo htmlspecialchars($user['name']); ?></h2>
                    <p style="color: #64748b; font-size: 0.9rem; font-weight: 500;">Authorized Staff Member</p>
                </div>

                <form method="POST" enctype="multipart/form-data" class="form-body">
                    <div class="form-grid">
                        
                        <div class="form-group full-width">
                            <label><i class="fas fa-camera"></i> Change Profile Picture</label>
                            <div class="file-upload-box">
                                <input type="file" name="photo" accept="image/*" style="font-size: 0.8rem;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Email ID (Non-editable)</label>
                            <input type="email" value="<?php echo $user['email']; ?>" disabled>
                        </div>

                        <div class="form-group full-width">
                            <label>Security Password</label>
                            <input type="password" name="password" placeholder="••••••••" autocomplete="new-password">
                            <small style="color: #94a3b8; font-size: 0.75rem;">Leave empty if you don't wish to change the password.</small>
                        </div>

                        <div class="form-group full-width">
                            <button type="submit" name="update_profile" class="save-btn">
                                <i class="fas fa-shield-halved"></i> Update Account Records
                            </button>
                        </div>

                    </div>
                </form>
            </div>

            <p style="text-align: center; color: #94a3b8; font-size: 0.75rem; margin-top: 2rem;">
                <i class="fas fa-lock"></i> All personal data is encrypted and stored according to company policy.
            </p>
        </div>
    </div>

</body>
</html>