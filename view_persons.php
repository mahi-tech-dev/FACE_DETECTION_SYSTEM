<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    echo "Access denied!";
    exit();
}

$admin_name = $_SESSION['username'];
$result = $conn->query("SELECT * FROM persons ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Persons</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
*{box-sizing:border-box;margin:0;padding:0}
body{
    font-family:'Montserrat',sans-serif;
    background:linear-gradient(135deg,#0f172a,#1e293b);
    color:#f8f8f8;
}

/* ===== Sidebar ===== */
.sidebar{
    position:fixed;
    left:0;top:0;
    width:260px;
    height:100vh;
    background:#111827;
    padding:30px 20px;
    box-shadow:5px 0 15px rgba(0,0,0,0.35);
}
.sidebar h2{
    text-align:center;
    color:#fbbf24;
    font-size:26px;
    margin-bottom:40px;
    font-weight:700;
}
.sidebar a{
    display:flex;
    align-items:center;
    gap:12px;
    color:#f8f8f8;
    text-decoration:none;
    padding:14px 18px;
    margin:8px 0;
    border-radius:12px;
    transition:.3s;
}
.sidebar a:hover,
.sidebar a.active{
    background:#1f2937;
    box-shadow:0 0 10px rgba(251,191,36,.6);
    color:#fbbf24;
}

/* ===== Main ===== */
.main{
    margin-left:280px;
    padding:40px 50px;
    min-height:100vh;
}
.main h2{
    font-size:30px;
    color:#fbbf24;
    margin-bottom:25px;
}

/* ===== Glass Table Card ===== */
.table-container{
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(12px);
    border-radius:20px;
    padding:30px;
    box-shadow:0 15px 35px rgba(0,0,0,.35);
}

/* ===== Search ===== */
.search-box{
    width:320px;
    padding:12px 15px;
    border-radius:12px;
    border:none;
    outline:none;
    margin-bottom:20px;
    background:rgba(255,255,255,0.15);
    color:#fff;
}
.search-box::placeholder{color:#cbd5e1}

/* ===== Table ===== */
table{
    width:100%;
    border-collapse:collapse;
}
th,td{
    padding:14px;
    text-align:left;
}
th{
    background:rgba(251,191,36,.2);
    color:#fbbf24;
    font-weight:600;
}
tr{
    border-bottom:1px solid rgba(255,255,255,.1);
}
tr:hover{
    background:rgba(255,255,255,.05);
}

img.person-photo{
    width:80px;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,.4);
}

/* ===== Buttons ===== */
.action-btn{
    padding:8px 14px;
    border-radius:10px;
    background:rgba(251,191,36,.2);
    color:#fbbf24;
    text-decoration:none;
    font-weight:600;
    margin-right:6px;
    transition:.3s;
}
.action-btn:hover{
    background:rgba(251,191,36,.45);
    color:#111827;
}

/* ===== Pagination ===== */
.pagination{
    margin-top:20px;
    text-align:center;
}
.pagination button{
    padding:8px 14px;
    margin:4px;
    border:none;
    border-radius:10px;
    background:rgba(251,191,36,.2);
    color:#fbbf24;
    cursor:pointer;
    font-weight:600;
}
.pagination button.active,
.pagination button:hover{
    background:#fbbf24;
    color:#111827;
}

/* ===== Responsive ===== */
@media(max-width:1024px){
    .main{margin-left:0;padding:25px}
    .sidebar{width:100%;height:auto;position:relative}
}
</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php"><i class="fa fa-house"></i> Home</a>
    <a href="add_person.php"><i class="fa fa-user-plus"></i> Add Person</a>
    <a href="view_persons.php" class="active"><i class="fa fa-users"></i> View All Records</a>
    <a href="match_face.php"><i class="fa fa-face-smile"></i> Match Face</a>
    <a href="admin_match_history.php"><i class="fa fa-history"></i> Match History</a>
    <a href="manage_users.php"><i class="fa fa-user-cog"></i> Manage Users</a>
    <a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a>
</div>

<!-- Main -->
<div class="main">
    <h2>Welcome, <?php echo htmlspecialchars($admin_name); ?></h2>

    <div class="table-container">

        <input type="text" id="searchInput" class="search-box" placeholder="Search by ID, Name or Age">

        <table id="personsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Photo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo $row['age']; ?></td>
                    <td>
                        <?php if($row['photo']): ?>
                            <img src="uploads/persons/<?php echo $row['photo']; ?>" class="person-photo">
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_person.php?id=<?php echo $row['id']; ?>" class="action-btn">Edit</a>
                        <a href="#" class="action-btn" onclick="confirmDelete(<?php echo $row['id']; ?>)">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <div class="pagination" id="pagination"></div>
    </div>
</div>

<script>
function confirmDelete(id){
    Swal.fire({
        title:"Are you sure?",
        text:"This record will be permanently deleted!",
        icon:"warning",
        showCancelButton:true,
        confirmButtonColor:"#d33",
        confirmButtonText:"Yes, delete"
    }).then((res)=>{
        if(res.isConfirmed){
            window.location.href="delete_person.php?id="+id;
        }
    });
}

/* SEARCH + PAGINATION */
const rowsPerPage=5;
let currentPage=1;
const table=document.getElementById("personsTable");
const rows=Array.from(table.querySelectorAll("tbody tr"));
const pagination=document.getElementById("pagination");

function displayTable(){
    let start=(currentPage-1)*rowsPerPage;
    let end=start+rowsPerPage;
    rows.forEach((row,i)=>{
        row.style.display=(i>=start&&i<end)?"":"none";
    });
}
function setupPagination(){
    pagination.innerHTML="";
    let pageCount=Math.ceil(rows.length/rowsPerPage);
    for(let i=1;i<=pageCount;i++){
        let btn=document.createElement("button");
        btn.innerText=i;
        btn.onclick=()=>{currentPage=i;displayTable();setupPagination();}
        if(i===currentPage)btn.classList.add("active");
        pagination.appendChild(btn);
    }
}
document.getElementById("searchInput").addEventListener("keyup",function(){
    let val=this.value.toLowerCase();
    rows.forEach(row=>{
        row.style.display=row.innerText.toLowerCase().includes(val)?"":"none";
    });
});
displayTable();
setupPagination();
</script>

</body>
</html>
