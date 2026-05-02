<?php
include("../config.php");
include("mail_config.php");

// Get message status for SweetAlert
$msg_status = isset($_GET['msg']) ? $_GET['msg'] : "";

/* ---------------- BULK APPROVE ---------------- */
if (isset($_POST['bulk_approve']) && !empty($_POST['student_id'])) {
    $selected_ids = $_POST['student_id'];
    foreach ($selected_ids as $id) {
        $id = mysqli_real_escape_string($conn, $id);
        $res = mysqli_query($conn,"SELECT * FROM students WHERE student_id='$id' AND status='pending'");
        $data = mysqli_fetch_assoc($res);

        if ($data) {
            $user_email = $data['email'];
            $user_name = $data['name'];
            $gen_roll_no = 1000 + $id;
            $gen_password = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"),0,8);

            $update="UPDATE students 
            SET roll_no='$gen_roll_no',password='$gen_password',status='approved'
            WHERE student_id='$id'";

            if(mysqli_query($conn,$update)){
                send_credential_mail($user_email,$user_name,$gen_roll_no,$gen_password);
            }
        }
    }
    header("Location: Manage-Student.php?msg=bulk_approved");
    exit();
}

/* ---------------- BULK REJECT ---------------- */
if (isset($_POST['bulk_reject']) && !empty($_POST['student_id'])) {
    $selected_ids = $_POST['student_id'];
    $reason = mysqli_real_escape_string($conn,$_POST['bulk_reason']);
    foreach ($selected_ids as $id) {
        $id = mysqli_real_escape_string($conn,$id);
        $res=mysqli_query($conn,"SELECT * FROM students WHERE student_id='$id'");
        $data=mysqli_fetch_assoc($res);
        if($data){
            $user_email=$data['email'];
            $user_name=$data['name'];
            $update="UPDATE students SET status='rejected',rejection_reason='$reason' WHERE student_id='$id'";
            if(mysqli_query($conn,$update)){
                send_rejection_mail($user_email,$user_name,$reason);
            }
        }
    }
    header("Location: Manage-Student.php?msg=bulk_rejected");
    exit();
}

/* ---------------- SINGLE APPROVE ---------------- */
if(isset($_GET['approve_id'])){
    $id=mysqli_real_escape_string($conn,$_GET['approve_id']);
    $res=mysqli_query($conn,"SELECT * FROM students WHERE student_id='$id'");
    $data=mysqli_fetch_assoc($res);
    if($data){
        $user_email=$data['email'];
        $user_name=$data['name'];
        $gen_roll_no=1000+$id;
        $gen_password=substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"),0,8);
        $update="UPDATE students SET roll_no='$gen_roll_no',password='$gen_password',status='approved' WHERE student_id='$id'";
        if(mysqli_query($conn,$update)){
            send_credential_mail($user_email,$user_name,$gen_roll_no,$gen_password);
            header("Location: Manage-Student.php?msg=success");
            exit();
        }
    }
}

/* ---------------- SINGLE REJECT ---------------- */
if(isset($_POST['reject_student'])){
    $id=mysqli_real_escape_string($conn,$_POST['student_id']);
    $reason=mysqli_real_escape_string($conn,$_POST['reason']);
    $res=mysqli_query($conn,"SELECT * FROM students WHERE student_id='$id'");
    $data=mysqli_fetch_assoc($res);
    if($data){
        $user_email=$data['email'];
        $user_name=$data['name'];
        $update="UPDATE students SET status='rejected',rejection_reason='$reason' WHERE student_id='$id'";
        if(mysqli_query($conn,$update)){
            send_rejection_mail($user_email,$user_name,$reason);
            header("Location: Manage-Student.php?msg=rejected");
            exit();
        }
    }
}

/* ---------------- FETCH LISTS ---------------- */
$pending_list=mysqli_query($conn,"SELECT * FROM students WHERE status='pending'");
$approved_list=mysqli_query($conn,"SELECT * FROM students WHERE status='approved'");
$rejected_list=mysqli_query($conn,"SELECT * FROM students WHERE status='rejected'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f9; margin: 0; padding: 10px; }
        .container { margin-top: 100px; max-width: 1300px; margin-left: auto; margin-right: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        @media (min-width: 992px) { .container { margin-left: 280px; } }
        h2 { color: #333; border-bottom: 2px solid #4e73df; padding-bottom: 10px; font-size: 1.5rem; margin-top: 30px; }
        .table-wrapper { width: 100%; overflow-x: auto; margin-bottom: 30px; border-radius: 8px; border: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; min-width: 700px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #4e73df; color: white; font-weight: 600; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f4ff; }
        .btn-approve { background: #28a745; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; font-size: 14px; display: inline-block; transition: 0.3s; }
        .btn-approve:hover { background: #218838; }
        .btn-reject { background: #dc3545; color: white; padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; transition: 0.3s; }
        .btn-reject:hover { background: #c82333; }
        .bulk-bar { background: #fff; border: 2px solid #6f42c1; padding: 15px; margin-bottom: 15px; display: none; flex-wrap: wrap; gap: 10px; justify-content: space-between; align-items: center; border-radius: 8px; position: sticky; top: 70px; z-index: 100; box-shadow: 0 4px 10px rgba(111, 66, 193, 0.1); }
        .bulk-btn { background: #6f42c1; color: white; padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        @media (max-width: 600px) { .container { padding: 15px; margin-top: 60px; } .btn-approve, .btn-reject { width: 100%; text-align: center; margin-bottom: 5px; } .bulk-bar { flex-direction: column; } }
    </style>
</head>
<body>
<?php include "sidebar.php" ;?>

<div class="container">
    <h2>Pending Requests</h2>
    <div class="bulk-bar" id="bulkBar">
        <div><strong id="selectedCount">0</strong> selected</div>
        <div>
            <button type="button" onclick="submitBulkApprove()" class="bulk-btn">Approve Selected</button>
            <button type="button" onclick="submitBulkReject()" class="btn-reject">Reject Selected</button>
        </div>
    </div>

    <form method="POST" id="bulkForm">
        <input type="hidden" name="bulk_reason" id="bulk_reason">
        <div class="table-wrapper">
            <table>
                <tr>
                    <th><input type="checkbox" id="mainCheck"></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
                <?php while($row=mysqli_fetch_assoc($pending_list)){ ?>
                <tr>
                    <td><input type="checkbox" name="student_id[]" value="<?= $row['student_id']?>" class="childCheck"></td>
                    <td><?= $row['name']?></td>
                    <td><?= $row['email']?></td>
                    <td>
                        <a href="?approve_id=<?= $row['student_id']?>" class="btn-approve">Approve</a>
                        <button type="button" onclick="rejectStudent(<?= $row['student_id']?>,'<?= addslashes($row['name'])?>')" class="btn-reject">Reject</button>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </form>

    <h2>Approved Students</h2>
    <div class="table-wrapper">
        <table>
            <tr><th>Roll</th><th>Name</th><th>Email</th></tr>
            <?php while($row=mysqli_fetch_assoc($approved_list)){ ?>
            <tr>
                <td><?= $row['roll_no']?></td>
                <td><?= $row['name']?></td>
                <td><?= $row['email']?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <h2>Rejected Students</h2>
    <div class="table-wrapper">
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th> <th>Reason</th>
            </tr>
            <?php while($row=mysqli_fetch_assoc($rejected_list)){ ?>
            <tr>
                <td><?= $row['name']?></td>
                <td><?= $row['email']?></td> <td style="color: #dc3545; font-style: italic;"><?= $row['rejection_reason']?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>

<form method="POST" id="rejectForm" style="display:none">
    <input type="hidden" name="student_id" id="reject_id">
    <input type="hidden" name="reason" id="reject_reason">
    <input type="hidden" name="reject_student" value="1">
</form>

<script>
/* SweetAlert Notification Handler */
const urlParams = new URLSearchParams(window.location.search);
const msg = urlParams.get('msg');

if (msg) {
    let title = ""; let icon = "success"; let text = "";

    if (msg === "success") { title = "Approved!"; text = "Student has been approved and credentials sent."; }
    else if (msg === "bulk_approved") { title = "Bulk Approval Done!"; text = "Selected students have been approved successfully."; }
    else if (msg === "rejected") { title = "Rejected"; icon = "error"; text = "Student registration has been rejected."; }
    else if (msg === "bulk_rejected") { title = "Bulk Rejection Done"; icon = "error"; text = "Selected students have been rejected."; }

    if (title) {
        Swal.fire({
            title: title, text: text, icon: icon, confirmButtonColor: '#4e73df'
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }
}

/* Checkbox & UI Logic */
const main = document.getElementById("mainCheck");
const bar = document.getElementById("bulkBar");
const count = document.getElementById("selectedCount");

main.addEventListener("change", () => {
    document.querySelectorAll(".childCheck").forEach(c => c.checked = main.checked);
    update();
});

document.addEventListener('change', (e) => {
    if (e.target.classList.contains('childCheck')) update();
});

function update() {
    let checked = document.querySelectorAll(".childCheck:checked").length;
    bar.style.display = checked > 0 ? "flex" : "none";
    count.innerText = checked;
}

function submitBulkApprove() {
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to approve all selected students?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Yes, Approve All'
    }).then((result) => {
        if (result.isConfirmed) {
            let form = document.getElementById("bulkForm");
            let input = document.createElement("input");
            input.type = "hidden"; input.name = "bulk_approve"; input.value = "1";
            form.appendChild(input);
            form.submit();
        }
    });
}

function submitBulkReject() {
    Swal.fire({
        title: "Reject selected students?",
        input: "textarea",
        inputPlaceholder: "Enter rejection reason...",
        showCancelButton: true,
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            document.getElementById("bulk_reason").value = result.value;
            let form = document.getElementById("bulkForm");
            let input = document.createElement("input");
            input.type = "hidden"; input.name = "bulk_reject"; input.value = "1";
            form.appendChild(input);
            form.submit();
        }
    });
}

function rejectStudent(id, name) {
    Swal.fire({
        title: "Reject " + name + " ?",
        input: "textarea",
        inputPlaceholder: "Reason for rejection...",
        showCancelButton: true,
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            document.getElementById("reject_id").value = id;
            document.getElementById("reject_reason").value = result.value;
            document.getElementById("rejectForm").submit();
        }
    });
}
</script>
</body>
</html>