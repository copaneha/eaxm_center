<?php
include "../config.php";
// Latest questions upar dikhane ke liye ORDER BY id DESC use kiya hai
$result = mysqli_query($conn, "SELECT * FROM question_bank ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Bank Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Google Font Import */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f0f2f5;
            padding: 40px 20px;
            
        }

        /* Sidebar ke liye space - agar sidebar 250px-200px ka hai */
        .container {

            max-width: 1300px;
            margin: 0 auto;
            margin-left: 300px; 
            transition: all 0.3s ease;
        }

        /* Header Styling */
        .header-flex {
          margin-top:100px;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        h2 {
            color: #1e293b;
            font-size: 22px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        /* Button Actions Group */
        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-add {
            background-color: #2563eb;
            color: white;
        }

        .btn-add:hover { background-color: #1d4ed8; transform: translateY(-1px); }

        .btn-print {
            background-color: #10b981;
            color: white;
        }

        .btn-print:hover { background-color: #059669; transform: translateY(-1px); }

        /* Table Card Container */
        .table-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
            padding: 16px;
            border-bottom: 2px solid #edf2f7;
        }

        td {
            padding: 16px;
            color: #334155;
            font-size: 14px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: middle;
        }

        tr:hover { background-color: #f8fafc; }

        .badge-marks {
            background-color: #f1f5f9;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            color: #475569;
        }

        .correct-ans {
            color: #059669;
            background: #ecfdf5;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
        }

        .btn-delete {
            color: #dc2626;
            text-decoration: none;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px;
        }

        .btn-delete:hover { background-color: #fef2f2; }

        /* Mobile Responsive */
        @media (max-width: 992px) {
            .container { margin-left: 0; padding: 20px; }
            .header-flex { flex-direction: column;  align-items: flex-start; }
        }
    </style>
</head>
<body>

<?php if(file_exists("sidebar.php")) { include("sidebar.php"); } ?>

<div class="container">
    <div class="header-flex">
        <h2>
            <i class="fa-solid fa-layer-group" style="color: #2563eb; margin-right: 12px;"></i>
            Question Bank Management
        </h2>
        <div class="action-buttons">
            <a href="generate_paper.php" class="btn btn-print">
                <i class="fa-solid fa-file-pdf"></i> Generate Paper
            </a>
            <a href="add_question.php" class="btn btn-add">
                <i class="fa fa-plus"></i> Add Question
            </a>
        </div>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Question Description</th>
                        <th>Correct</th>
                        <th>Marks</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)){ ?>
                        <tr>
                            <td><span style="color: #94a3b8;">#<?php echo $row['id']; ?></span></td>
                            <td><strong><?php echo htmlspecialchars($row['subject']); ?></strong></td>
                            <td style="max-width: 400px;"><?php echo htmlspecialchars($row['question']); ?></td>
                            <td><span class="correct-ans"><?php echo htmlspecialchars($row['correct_option']); ?></span></td>
                            <td><span class="badge-marks"><?php echo $row['marks']; ?></span></td>
                            <td style="text-align: center;">
                                <a href="delete_question.php?id=<?php echo $row['id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Kya aap sach mein ise delete karna chahte hain?')">
                                   <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 50px; color: #94a3b8;">
                                <i class="fa-solid fa-folder-open" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                                No questions found in the database.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>