<?php 
include "../config.php"; 
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-blue: #2c52a1;
        --accent-blue: #3c64b1;
        --bg-gray: #f4f7fa;
        --success-green: #2ecc71;
        --danger-red: #e74c3c;
        --text-main: #2d3436;
        --text-muted: #636e72;
        --card-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }

    body { 
        font-family: 'Inter', sans-serif; 
        background-color: var(--bg-gray); 
        margin: 0; 
        display: flex; 
        color: var(--text-main);
        flex-direction: row;
    }

    .main-wrapper { 
        flex-grow: 1; 
        padding: 110px 40px 40px 40px; 
        min-height: 100vh; 
        max-width: 1400px; 
        margin: 0 auto;
        width: 100%;
        box-sizing: border-box;
    }
    
    .page-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 30px; 
        flex-wrap: wrap; 
        gap: 15px;
    }
    .page-title { font-size: 24px; font-weight: 700; color: var(--primary-blue); margin: 0; }

    .btn-add { 
        background: var(--primary-blue); color: white; padding: 12px 24px; text-decoration: none; 
        border-radius: 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;
        box-shadow: 0 4px 12px rgba(44, 82, 161, 0.2); transition: transform 0.2s;
    }

    .content-card { 
        background: #ffffff; border-radius: 16px; padding: 0; overflow: hidden;
        box-shadow: var(--card-shadow); border: 1px solid rgba(0,0,0,0.03);
    }
    
    .ho { background: #281fa7; }
    .card-header { padding: 25px 30px; font-size: 18px; font-weight: 700; color:white; border-bottom: 1px solid #1c1ea0; }

    /* Responsive Table CSS */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    table { width: 100%; border-collapse: collapse; min-width: 800px; }
    th { 
        background: #fcfcfc; color: var(--text-muted); font-weight: 600; font-size: 12px; 
        padding: 18px 25px; text-align: left; border-bottom: 1px solid #f0f0f0; text-transform: uppercase;
    }
    td { padding: 20px 25px; border-bottom: 1px solid #f8f9fa; font-size: 14px; vertical-align: middle; }
    tr:hover { background-color: #f9fbff; }

    .code-chip { background: #f1f3f9; color: var(--primary-blue); padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 13px; }

    .badge { padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; display: inline-block; }
    .badge-active { background: rgba(46, 204, 113, 0.1); color: var(--success-green); }
    .badge-inactive { background: rgba(231, 76, 60, 0.1); color: var(--danger-red); }

    .action-links { display: flex; gap: 10px; }
    .btn-action { height: 35px; width: 35px; display: flex; align-items: center; justify-content: center; border-radius: 8px; text-decoration: none; transition: all 0.2s; cursor: pointer; border: none; }
    .btn-edit { background: rgba(52, 152, 219, 0.1); color: #3498db; }
    .btn-delete { background: rgba(231, 76, 60, 0.1); color: var(--danger-red); }

    /* Media Queries for Responsiveness */
    @media screen and (max-width: 992px) {
        body { flex-direction: column; }
        .main-wrapper { padding: 80px 20px 20px 20px; }
    }

    @media screen and (max-width: 600px) {
        .page-header { flex-direction: column; align-items: flex-start; }
        .btn-add { width: 100%; justify-content: center; }
        .page-title { font-size: 20px; }
        .card-header { padding: 20px; }
    }
</style>

<?php include "sidebar.php"; ?>

<div class="main-wrapper">
    <div class="page-header">
        <h2 class="page-title">Exam Centres Management</h2>
        <a href="add-centre.php" class="btn-add"><i class="fa-solid fa-plus"></i> Add New Centre</a>
    </div>

    <div class="content-card">
        <div class="card-header ho">Existing Centres List</div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="8%">ID</th>
                        <th width="12%">Centre Code</th>
                        <th width="35%">Centre Details</th>
                        <th width="15%">Location</th>
                        <th width="15%">Status</th>
                        <th width="15%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM exam_centres ORDER BY id DESC");
                    if(mysqli_num_rows($res) > 0) {
                        while($row = mysqli_fetch_assoc($res)){
                            $statusClass = ($row['status'] == 'Active') ? 'badge-active' : 'badge-inactive';
                            echo "<tr>
                                <td style='color: #b2bec3; font-weight: 600;'>#{$row['id']}</td>
                                <td><span class='code-chip'>{$row['centre_code']}</span></td>
                                <td>
                                    <div style='font-weight:700; color:#2d3436; margin-bottom:4px;'>{$row['centre_name']}</div>
                                    <div style='font-size:12px; color:#636e72;'><i class='fa-solid fa-location-dot'></i> {$row['address']}</div>
                                </td>
                                <td style='font-weight: 500;'>{$row['city']}</td>
                                <td><span class='badge {$statusClass}'>{$row['status']}</span></td>
                                <td>
                                    <div class='action-links'>
                                        <a href='edit-centre.php?id={$row['id']}' class='btn-action btn-edit' title='Edit'><i class='fa-solid fa-pen-to-square'></i></a>
                                        <button onclick='confirmDelete({$row['id']})' class='btn-action btn-delete' title='Delete'><i class='fa-solid fa-trash'></i></button>
                                    </div>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; padding: 60px;'>No records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Delete Confirmation Function
function confirmDelete(id) {
    Swal.fire({
        title: 'Kya aap sure hain?',
        text: "Delete hone ke baad ye data wapas nahi aayega!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2c52a1',
        cancelButtonColor: '#e74c3c',
        confirmButtonText: 'Haan, delete karein!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'delete-centre.php?id=' + id;
        }
    })
}

// Success Alert check (URL parameter se)
const urlParams = new URLSearchParams(window.location.search);

if (urlParams.get('msg') === 'updated') {
    Swal.fire({
        icon: 'success',
        title: 'Update Successful 🎉',
        text: 'Centre details successfully update ho gayi hain.',
        confirmButtonText: 'OK',
        confirmButtonColor: '#2c52a1',
        timer: 2500,
        timerProgressBar: true
    }).then(() => {
        window.history.replaceState({}, document.title, window.location.pathname);
    });
}
</script>