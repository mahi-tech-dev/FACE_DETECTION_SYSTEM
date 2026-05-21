<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    echo "Access denied!";
    exit();
}

$message = "";

/* ===== Existing Logic (UNCHANGED) ===== */
$persons = [];
$q = mysqli_query($conn, "SELECT id, name, descriptor FROM persons WHERE descriptor IS NOT NULL");
while ($row = mysqli_fetch_assoc($q)) {
    $row['descriptor'] = json_decode($row['descriptor'], true);
    if (is_array($row['descriptor'])) $persons[] = $row;
}

if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $age  = $_POST['age'];
    $photoName = "";
    $descriptorJson = $_POST['descriptor'];

    if (!empty($_FILES['photo']['name'])) {
        $photoName = time() . "_" . preg_replace("/[^a-zA-Z0-9\.\-_]/", "", $_FILES['photo']['name']);
        if(!is_dir("uploads/persons/")) mkdir("uploads/persons/", 0777, true);
        move_uploaded_file($_FILES['photo']['tmp_name'], "uploads/persons/" . $photoName);
    } elseif (!empty($_POST['camera_image'])) {
        $data = base64_decode(str_replace('data:image/png;base64,', '', $_POST['camera_image']));
        if(!is_dir("uploads/persons/")) mkdir("uploads/persons/", 0777, true);
        $photoName = time() . "_camera.png";
        file_put_contents("uploads/persons/" . $photoName, $data);
    }

    if ($photoName != "" && !empty($descriptorJson)) {
        $stmt = $conn->prepare("INSERT INTO persons (name, age, photo, descriptor) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $name, $age, $photoName, $descriptorJson);
        $stmt->execute();
        $message = "✅ Person added successfully!";
    } else {
        $message = "Please upload/capture an image AND generate descriptor.";
    }
}

$admin_name = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Person</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box;margin:0;padding:0}
body{
    font-family:'Montserrat',sans-serif;
    background:linear-gradient(135deg,#0f172a,#1e293b);
    color:#f8fafc;
}

/* ===== Sidebar (MATCH VIEW_PERSONS) ===== */
.sidebar{
    position:fixed;
    width:260px;
    height:100vh;
    background:#111827;
    padding:30px 20px;
}
.sidebar h2{
    color:#fbbf24;
    text-align:center;
    margin-bottom:35px;
}
.sidebar a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:14px 18px;
    margin:8px 0;
    color:#e5e7eb;
    text-decoration:none;
    border-radius:12px;
    font-size:15px;
    transition:.3s;
}
.sidebar a:hover,
.sidebar a.active{
    background:#1f2937;
    color:#fbbf24;
    box-shadow:0 0 12px rgba(251,191,36,.6);
}

/* ===== Main ===== */
.main{
    margin-left:280px;
    padding:40px 50px;
}
.main h1{
    color:#fbbf24;
    margin-bottom:25px;
}

/* ===== Card ===== */
.card{
    max-width:620px;
    background:rgba(255,255,255,.05);
    padding:30px;
    border-radius:20px;
    box-shadow:0 15px 35px rgba(0,0,0,.35);
}
.card h2{
    color:#fbbf24;
    margin-bottom:20px;
}

/* ===== Inputs ===== */
input[type=text],
input[type=number],
input[type=file]{
    width:100%;
    padding:14px;
    border-radius:10px;
    border:none;
    margin-bottom:15px;
}

/* ===== Buttons (FIXED SIZE) ===== */
.btn{
    background:linear-gradient(135deg,#fbbf24,#f59e0b);
    color:#111827;
    border:none;
    padding:10px 18px;
    border-radius:10px;
    font-weight:600;
    cursor:pointer;
    margin:6px 6px 6px 0;
    transition:.3s;
}
.btn:hover{
    box-shadow:0 0 12px rgba(251,191,36,.7);
    transform:translateY(-2px);
}

/* ===== Preview ===== */
video,img{
    border-radius:12px;
    margin-top:10px;
}

/* ===== Message ===== */
.message{
    margin-top:15px;
    color:#22c55e;
    font-weight:600;
}
</style>
</head>

<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php"><i class="fa fa-house"></i> Home</a>
    <a href="add_person.php" class="active"><i class="fa fa-user-plus"></i> Add Person</a>
    <a href="view_persons.php"><i class="fa fa-users"></i> View All Records</a>
    <a href="match_face.php"><i class="fa fa-face-smile"></i> Match Face</a>
    <a href="admin_match_history.php"><i class="fa fa-history"></i> Match History</a>
    <a href="manage_users.php"><i class="fa fa-user-cog"></i> Manage Users</a>
    <a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a>
</div>

<div class="main">
    <h1>Welcome, <?php echo htmlspecialchars($admin_name); ?></h1>

    <div class="card">
        <h2>Add Person</h2>

        <form method="post" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Name" required>
            <input type="number" name="age" placeholder="Age" required>
            <input type="file" name="photo" accept="image/*" onchange="loadPreview(event)">

            <button type="button" class="btn" onclick="startCamera()">Start Camera</button>
            <button type="button" class="btn" onclick="captureImage()">Capture</button>
            <button type="button" class="btn" onclick="generateDescriptor()">Generate Descriptor</button>
            <br><br>
            <button type="submit" name="save" class="btn">Save Person</button>

            <video id="video" width="320" style="display:none;"></video>
            <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
            <input type="hidden" name="camera_image" id="camera_image">
            <img id="preview" width="320" style="display:none;">
            <input type="hidden" name="descriptor" id="descriptor">
        </form>

        <?php if($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>
</div>

<script src="js/face-api.min.js"></script>
<script>
let video=document.getElementById("video");
let preview=document.getElementById("preview");

async function loadModels(){
    await Promise.all([
        faceapi.nets.ssdMobilenetv1.loadFromUri('models'),
        faceapi.nets.faceLandmark68Net.loadFromUri('models'),
        faceapi.nets.faceRecognitionNet.loadFromUri('models')
    ]);
}
loadModels();
function loadPreview(event){
    let preview = document.getElementById("preview");
    preview.src = URL.createObjectURL(event.target.files[0]);
    preview.style.display = "block";
}
function startCamera(){
    navigator.mediaDevices.getUserMedia({video:true}).then(s=>{
        video.srcObject=s;
        video.style.display="block";
        video.play();
    });
}
function captureImage(){
    let c=document.getElementById("canvas");
    c.getContext("2d").drawImage(video,0,0,c.width,c.height);
    let d=c.toDataURL("image/png");
    document.getElementById("camera_image").value=d;
    preview.src=d;
    preview.style.display="block";
}
async function generateDescriptor(){
    if(preview.style.display!=="block") return alert("Select or capture image first.");
    const det=await faceapi.detectSingleFace(preview)
        .withFaceLandmarks()
        .withFaceDescriptor();
    if(!det) return alert("No face detected!");
    document.getElementById("descriptor").value=
        JSON.stringify(Array.from(det.descriptor));
    alert("Descriptor generated.");
}
</script>

</body>
</html>
