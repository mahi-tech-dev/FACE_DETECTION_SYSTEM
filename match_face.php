<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied");
}

/* Load persons */
$persons = [];
$q = mysqli_query($conn, "SELECT id, name, age, photo, descriptor FROM persons WHERE descriptor IS NOT NULL");
while ($row = mysqli_fetch_assoc($q)) {
    $row['descriptor'] = json_decode($row['descriptor'], true);
    if (is_array($row['descriptor'])) {
        $persons[] = $row;
    }
}

$admin_name = $_SESSION['username'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Match Face</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

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
    box-shadow:5px 0 15px rgba(0,0,0,.35);
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
.main h1{
    font-size:30px;
    color:#fbbf24;
    margin-bottom:25px;
}

/* ===== Glass Card ===== */
.card{
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(14px);
    border-radius:22px;
    padding:35px;
    max-width:780px;
    box-shadow:0 20px 40px rgba(0,0,0,.35);
}
.card h2{
    margin-bottom:20px;
    color:#fbbf24;
}

/* ===== Upload ===== */
input[type=file]{
    background:rgba(255,255,255,.15);
    border:none;
    padding:12px;
    border-radius:12px;
    color:#fff;
    margin-bottom:20px;
}

/* ===== Preview ===== */
.preview-box{
    height:380px;
    border:2px dashed rgba(251,191,36,.5);
    border-radius:18px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(0,0,0,.25);
}
.preview-box img,
.preview-box video{
    width:100%;
    height:100%;
    object-fit:contain;
    border-radius:16px;
}

/* ===== Buttons ===== */
.btn{
    padding:12px 26px;
    border:none;
    border-radius:14px;
    font-weight:600;
    cursor:pointer;
    margin-right:10px;
    transition:.3s;
}
.btn-blue{
    background:rgba(59,130,246,.25);
    color:#93c5fd;
}
.btn-green{
    background:rgba(34,197,94,.25);
    color:#86efac;
}
.btn-red{
    background:rgba(239,68,68,.25);
    color:#fca5a5;
}
.btn:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(0,0,0,.4);
}

/* ===== Result ===== */
.result{
    margin-top:25px;
    padding:20px;
    border-radius:16px;
    background:rgba(0,0,0,.25);
}
.match{
    color:#22c55e;
    font-weight:700;
    font-size:18px;
}
.nomatch{
    color:#ef4444;
    font-weight:700;
    font-size:18px;
}
.result img{
    margin-top:10px;
    border-radius:14px;
    box-shadow:0 10px 20px rgba(0,0,0,.4);
}

/* ===== Responsive ===== */
@media(max-width:1024px){
    .main{margin-left:0;padding:25px}
    .sidebar{width:100%;height:auto;position:relative}
}
</style>
</head>

<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php"><i class="fa fa-house"></i> Home</a>
    <a href="add_person.php"><i class="fa fa-user-plus"></i> Add Person</a>
    <a href="view_persons.php"><i class="fa fa-users"></i> View Records</a>
    <a href="match_face.php" class="active"><i class="fa fa-face-smile"></i> Match Face</a>
    <a href="admin_match_history.php"><i class="fa fa-clock-rotate-left"></i> Match History</a>
    <a href="manage_users.php"><i class="fa fa-user-cog"></i> Manage Users</a>
    <a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a>
</div>

<div class="main">
    <h1>Welcome, <?=htmlspecialchars($admin_name)?></h1>

    <div class="card">
        <h2>Face Matching</h2>

        <input type="file" id="imageUpload" accept="image/*">

        <div class="preview-box">
            <img id="previewImg" style="display:none">
            <video id="video" autoplay muted style="display:none"></video>
        </div>

        <br>
        <button class="btn btn-blue" id="findBtn" disabled>Find Match</button>
        <button class="btn btn-green" id="liveBtn">Start Live Matching</button>
        <button class="btn btn-red" id="stopBtn" style="display:none">Stop Live Matching</button>

        <div id="result" class="result"></div>
    </div>
</div>

<script src="js/face-api.min.js"></script>
<script>
const persons = <?=json_encode($persons)?>.map(p=>({...p,descriptor:new Float32Array(p.descriptor)}));
let uploadedDesc=null, stream=null, lastFrame=null;

Promise.all([
faceapi.nets.ssdMobilenetv1.loadFromUri('models'),
faceapi.nets.faceLandmark68Net.loadFromUri('models'),
faceapi.nets.faceRecognitionNet.loadFromUri('models')
]);

imageUpload.onchange=async e=>{
const img=previewImg;
img.src=URL.createObjectURL(e.target.files[0]);
img.style.display='block';
video.style.display='none';

img.onload=async()=>{
const det=await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
if(!det){result.innerHTML="<span class='nomatch'>No face detected</span>";return;}
uploadedDesc=det.descriptor;
findBtn.disabled=false;
};
};

findBtn.onclick=()=>matchFace(uploadedDesc,false);

function matchFace(desc,isLive){
let best=null,min=0.6;
persons.forEach(p=>{
const d=faceapi.euclideanDistance(desc,p.descriptor);
if(d<min){min=d;best=p;}
});

let status=best?"MATCH":"NO MATCH";
fetch("save_match.php", {
    method: "POST",
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        username: "<?= htmlspecialchars($_SESSION['username']) ?>",
        matched_person: best ? best.name : null,
        distance: min,
        status: status,
        match_source: isLive ? 'LIVE' : 'UPLOAD' 
    })
});

if(best){
result.innerHTML=`
<div class="match">MATCH FOUND</div>
<p><b>Name:</b> ${best.name}</p>
<p><b>Age:</b> ${best.age}</p>
<p><b>Distance:</b> ${min.toFixed(3)}</p>
<img src="uploads/persons/${best.photo}" width="150">`;
}else{
result.innerHTML=`<div class="nomatch">NO MATCH FOUND</div>`;
}
}

liveBtn.onclick=async()=>{
stream=await navigator.mediaDevices.getUserMedia({video:true});
video.srcObject=stream;
video.style.display='block';
previewImg.style.display='none';
liveBtn.style.display='none';
stopBtn.style.display='inline-block';

const interval=setInterval(async()=>{
const det=await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();
if(det) lastFrame=det.descriptor;
},1500);

stopBtn.onclick=()=>{
clearInterval(interval);
stream.getTracks().forEach(t=>t.stop());
video.style.display='none';
stopBtn.style.display='none';
liveBtn.style.display='inline-block';
if(lastFrame) matchFace(lastFrame,true);
};
};
</script>

</body>
</html>
