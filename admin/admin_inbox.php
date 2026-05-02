<?php
// session_start(); // Agar zaroorat ho toh enable karein
include "../config.php";

// ✅ Query for Pending Messages
$sql = "SELECT * FROM contact_messages WHERE status='Pending' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if(!$result){
    die("Query Failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Inbox | Online Exam Center</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-bg: #f4f7f6;
            --accent-color: #4361ee;
            --sidebar-width: 260px;
        }

        body {
            background-color: var(--primary-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* --- Layout --- */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            transition: 0.3s;
        }

        /* --- Header Section --- */
        .inbox-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 20px 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .inbox-header h2 {
            margin: 0;
            font-weight: 700;
            color: #333;
            font-size: 22px;
        }

        /* --- Message Cards --- */
        .msg-card {
            background: #fff;
            border: none;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.04);
            border-left: 5px solid var(--accent-color);
        }

        .user-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .user-name {
            font-weight: 600;
            color: #2d3436;
            font-size: 16px;
        }

        .user-email {
            color: #636e72;
            font-size: 14px;
        }

        .msg-body {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            color: #444;
            margin-bottom: 20px;
            border: 1px solid #edf2f7;
        }

        /* --- Reply Form --- */
        .reply-textarea {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px;
            font-size: 14px;
            transition: 0.3s;
            resize: none;
        }

        .reply-textarea:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .btn-reply {
            background: var(--accent-color);
            color: #fff;
            border: none;
            padding: 8px 25px;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn-reply:hover {
            background: #3651d1;
            transform: translateY(-2px);
        }

        /* --- Mobile Responsive --- */
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 15px; }
            .inbox-header { flex-direction: column; gap: 15px; text-align: center; }
        }
    </style>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main-content">
    <div class="container-fluid">
        
        <div class="inbox-header">
            <div>
                <h2><i class="fa-solid fa-inbox text-primary me-2"></i> Admin Inbox</h2>
                <small class="text-muted">Manage all student and visitor queries</small>
            </div>
            <a href="replied_history.php" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-clock-rotate-left me-1"></i> View Replied History
            </a>
        </div>

        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="msg-card">
                    <div class="user-meta">
                        <div>
                            <span class="user-name d-block"><?= htmlspecialchars($row['name']); ?></span>
                            <span class="user-email"><i class="fa-regular fa-envelope me-1"></i> <?= htmlspecialchars($row['email']); ?></span>
                        </div>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-2">
                            Pending
                        </span>
                    </div>

                    <div class="msg-body">
                        <strong><i class="fa-solid fa-comment-dots me-2 text-secondary"></i> Message:</strong><br>
                        <p class="mt-2 mb-0"><?= nl2br(htmlspecialchars($row['message'])); ?></p>
                    </div>

                    <form action="reply_logic.php" method="POST">
                        <input type="hidden" name="msg_id" value="<?= $row['id']; ?>">
                        <input type="hidden" name="user_email" value="<?= $row['email']; ?>">
                        
                        <div class="form-group">
                            <textarea name="reply_msg" class="form-control reply-textarea" rows="3" placeholder="Type your response here..." required></textarea>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn-reply">
                                <i class="fa-solid fa-paper-plane me-2"></i> Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="100" class="mb-3 opacity-50">
                <h4 class="text-muted">All caught up!</h4>
                <p class="text-secondary">No pending messages in your inbox.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>