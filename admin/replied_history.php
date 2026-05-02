<?php
include "../config.php"; 

// Database se Replied messages nikalna
$sql = "SELECT * FROM contact_messages WHERE status='Replied' ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Replied History | Admin Panel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --success-color: #10b981;
            --primary-color: #4361ee;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            margin: 0;
        }

        /* --- Layout --- */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            transition: all 0.3s ease;
        }

        /* --- Header Section --- */
        .history-header {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .history-header h2 {
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            font-size: 1.5rem;
        }

        /* --- Table Styling --- */
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }

        .table thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border: none;
            padding: 15px;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        /* --- Message Styles --- */
        .user-msg {
            display: block;
            font-size: 0.85rem;
            color: #64748b;
            font-style: italic;
            margin-top: 5px;
            border-left: 2px solid #e2e8f0;
            padding-left: 10px;
        }

        .admin-reply-box {
            font-size: 0.9rem;
            color: var(--success-color);
            font-weight: 500;
            background: #f0fdf4;
            padding: 8px 12px;
            border-radius: 8px;
            display: inline-block;
        }

        .status-pill {
            background: #d1fae5;
            color: #065f46;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        /* --- Responsive --- */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main-content">
    <div class="container-fluid">
        
        <div class="history-header">
            <h2><i class="fa-solid fa-clock-rotate-left text-success me-2"></i> Replied History</h2>
            <a href="admin_inbox.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Inbox
            </a>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User Details</th>
                            <th>Inquiry Message</th>
                            <th>Admin Reply</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-nowrap small text-muted">
                                        <i class="fa-regular fa-calendar-check me-1"></i>
                                        <?= date('d M, Y', strtotime($row['created_at'])); ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($row['name']); ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($row['email']); ?></div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold"><?= htmlspecialchars($row['subject']); ?></div>
                                        <span class="user-msg">"<?= htmlspecialchars($row['message']); ?>"</span>
                                    </td>
                                    <td>
                                        <div class="admin-reply-box">
                                            <i class="fa-solid fa-reply me-1"></i>
                                            <?= htmlspecialchars($row['admin_reply']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-pill shadow-sm">
                                            <i class="fa-solid fa-check-double me-1"></i> <?= $row['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fa-3x mb-3 opacity-25"></i>
                                    <p>No history records found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>