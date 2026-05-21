<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    die("Access Denied");
}

/* ✅ SAVE MATCH HISTORY (SHARED TABLE) */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['match_status'])) {

    $username        = $_SESSION['username'];
    $matched_person  = $_POST['matched_person'] ?? null;
    $match_distance  = $_POST['match_distance'] ?? null;
    $match_status    = $_POST['match_status'];

    $match_source = $_POST['match_source'] ?? 'UPLOAD'; // default

$stmt = $conn->prepare("
    INSERT INTO match_history 
    (username, matched_person, match_distance, match_status, match_source)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "ssdss",
    $username,
    $matched_person,
    $match_distance,
    $match_status,
    $match_source
);

    $stmt->execute();
    exit;
}

/* ✅ FETCH PERSONS */
$persons = [];
$q = mysqli_query($conn, "
    SELECT id, name, age, photo, descriptor 
    FROM persons 
    WHERE descriptor IS NOT NULL
");

while ($row = mysqli_fetch_assoc($q)) {
    $row['descriptor'] = json_decode($row['descriptor'], true);
    if (is_array($row['descriptor'])) {
        $persons[] = $row;
    }
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Face Match</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

<style>
/* ===== Base ===== */
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Montserrat', sans-serif; background: linear-gradient(135deg,#0f172a,#1e293b); color: #f8f8f8; }

/* ===== Sidebar ===== */
.sidebar {
    position: fixed;
    width: 260px;
    height: 100vh;
    background: #111827;
    padding: 30px 20px;
    display: flex;
    flex-direction: column;
}
.sidebar h2 {
    text-align: center;
    color: #fbbf24;
    margin-bottom: 40px;
}
.sidebar a {
    color: #fff;
    text-decoration: none;
    padding: 14px 18px;
    margin: 8px 0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: 0.3s;
}
.sidebar a:hover, .sidebar a.active {
    background: #1f2937;
    color: #fbbf24;
    box-shadow: 0 0 10px rgba(251,191,36,.4);
}

/* ===== Main ===== */
.main {
    margin-left: 280px;
    padding: 40px 50px;
    min-height: 100vh;
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.header h1 { color: #fbbf24; font-size: 32px; }
.header .profile { display: flex; align-items: center; gap: 12px; }
.header .profile img { width: 45px; border-radius: 50%; border: 2px solid #fbbf24; }

/* ===== Card ===== */
.card {
    background: rgba(255,255,255,0.06);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 15px 30px rgba(0,0,0,.35);
    max-width: 720px;
    margin-bottom: 30px;
}
.card h2 { color: #fbbf24; margin-bottom: 20px; }

/* ===== Form Elements ===== */
input[type=file], button {
    padding: 12px 15px;
    border-radius: 10px;
    border: none;
    font-weight: 600;
}
button { background: #2563eb; color: #fff; cursor: pointer; transition: 0.3s; }
button:disabled { background: #9ca3af; }
button:hover:not(:disabled) { background: #1d4ed8; }

/* ===== Preview ===== */
.preview-box {
    margin-top: 15px;
    height: 380px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.05);
    border: 2px dashed #cfd8dc;
}
.preview-box img, .preview-box video { width: 100%; height: 100%; object-fit: contain; border-radius: 12px; }

/* ===== Result ===== */
.result {
    margin-top: 20px;
    padding: 15px;
    background: rgba(255,255,255,0.06);
    border-radius: 12px;
}
.result img { width: 140px; border-radius: 10px; }

/* ===== Responsive ===== */
@media(max-width:1024px){
    .main { margin-left:0; padding:25px; }
    .sidebar{width:100%; height:auto; flex-direction:row; overflow-x:auto;}
    .sidebar a{flex:1;text-align:center;margin:0 5px;}
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>User Panel</h2>
    <a href="user_dashboard.php"><i class="fa fa-house"></i> Home</a>
    <a href="view_persons.php"><i class="fa fa-users"></i> View Records</a>
    <a href="user_match_face.php" class="active"><i class="fa fa-face-smile"></i> Match Face</a>
    <a href="user_match_history.php"><i class="fa fa-clock-rotate-left"></i> Match History</a>
    <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
    <a href="help.php"><i class="fa fa-question-circle"></i> Help</a>
    <a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a>
</div>

<!-- Main -->
<div class="main">
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>
        <div class="profile">
            <img src="https://img.icons8.com/color/96/000000/user-male-circle.png" alt="Profile">
            <span><?php echo htmlspecialchars($username); ?></span>
        </div>
    </div>

    <div class="card">
        <h2>🧠 Face Matching</h2>

        <!-- File Upload -->
        <input type="file" id="imageUpload" accept="image/*">

        <!-- Preview Box -->
        <div class="preview-box">
            <img id="previewImg" style="display:none">
            <video id="videoPreview" autoplay muted style="display:none"></video>
        </div>

        <!-- Live Camera Buttons -->
        <button type="button" id="startLiveBtn">▶ Start Live Matching</button>
        <button type="button" id="stopLiveBtn" disabled>Stop Live Matching</button>
        <button type="button" id="matchBtn" disabled>Find Matching Person</button>

        <!-- Result -->
        <div id="resultBox" class="result"></div>
    </div>
</div>

<script src="js/face-api.min.js"></script>
<script>
const persons = <?php echo json_encode($persons); ?>.map(p => ({...p, descriptor: new Float32Array(p.descriptor)}));

const imageUpload = document.getElementById("imageUpload");
const previewImg = document.getElementById("previewImg");
const videoPreview = document.getElementById("videoPreview");
const resultBox = document.getElementById("resultBox");
const matchBtn = document.getElementById("matchBtn");
const startLiveBtn = document.getElementById("startLiveBtn");
const stopLiveBtn = document.getElementById("stopLiveBtn");

let uploadedDetections = [];
let stream = null;
let liveInterval = null;
let liveBestMatch = null;
let liveBestDistance = 0.6;

/* LOAD MODELS */
(async ()=>{
    await Promise.all([
        faceapi.nets.ssdMobilenetv1.loadFromUri('models'),
        faceapi.nets.faceLandmark68Net.loadFromUri('models'),
        faceapi.nets.faceRecognitionNet.loadFromUri('models')
    ]);
})();

/* IMAGE UPLOAD */
imageUpload.addEventListener("change", async e=>{
    const file = e.target.files[0];
    if(!file) return;

    stopLiveMatching();

    previewImg.src = URL.createObjectURL(file);
    previewImg.style.display = "block";
    videoPreview.style.display = "none";
    resultBox.innerHTML = "Detecting faces...";
    matchBtn.disabled = true;

    previewImg.onload = async ()=>{
        uploadedDetections = await faceapi.detectAllFaces(previewImg).withFaceLandmarks().withFaceDescriptors();
        if(!uploadedDetections.length){
            resultBox.innerHTML = "No face detected";
            return;
        }
        resultBox.innerHTML = `${uploadedDetections.length} face(s) detected. Click Find Matching Person.`;
        matchBtn.disabled = false;
    };
});

/* IMAGE MATCH */
matchBtn.addEventListener("click", ()=>{
    if(!uploadedDetections.length) return;
    resultBox.innerHTML = "";
    let matched = false;

    uploadedDetections.forEach((det, i)=>{
        let best = null;
        let min = 0.6;

        persons.forEach(p=>{
            const d = faceapi.euclideanDistance(det.descriptor, p.descriptor);
            if(d < min){ min = d; best = p; }
        });

        if(best){
            matched = true;
            resultBox.innerHTML += `
                <hr>
                <h3 style="color:green">Match Found (Face ${i+1})</h3>
                <p><b>Name:</b> ${best.name}</p>
                <p><b>Age:</b> ${best.age}</p>
                <p><b>Distance:</b> ${min.toFixed(3)}</p>
                <img src="uploads/persons/${best.photo}">
            `;

            fetch("user_match_face.php", {
                method: "POST",
                headers: {"Content-Type":"application/x-www-form-urlencoded"},
                body: new URLSearchParams({
                    matched_person: best.name,
                    match_distance: min.toFixed(4),
                    match_status: "Matched"
                })
            });
        }
    });

    if(!matched){
        resultBox.innerHTML = `<h3 style="color:red">No Match Found</h3>`;
        fetch("user_match_face.php", {
            method: "POST",
            headers: {"Content-Type":"application/x-www-form-urlencoded"},
            body: new URLSearchParams({
                match_status: "Not Matched"
            })
        });
    }
});

/* LIVE MATCH */
startLiveBtn.addEventListener("click", async ()=>{
    stream = await navigator.mediaDevices.getUserMedia({video:true});
    videoPreview.srcObject = stream;
    videoPreview.style.display = "block";
    previewImg.style.display = "none";

    startLiveBtn.disabled = true;
    stopLiveBtn.disabled = false;

    liveInterval = setInterval(async ()=>{
        const detections = await faceapi.detectAllFaces(videoPreview).withFaceLandmarks().withFaceDescriptors();
        if(!detections.length){ resultBox.innerHTML = "No face detected"; return; }

        detections.forEach(det=>{
            let best = null;
            let min = 0.6;

            persons.forEach(p=>{
                const d = faceapi.euclideanDistance(det.descriptor, p.descriptor);
                if(d < min){ min = d; best = p; }
            });

            if(best && min < liveBestDistance){
                liveBestDistance = min;
                liveBestMatch = best;
            }
        });
    }, 2000);
});

stopLiveBtn.addEventListener("click", stopLiveMatching);

function stopLiveMatching(){
    if(liveInterval) clearInterval(liveInterval);
    if(stream) stream.getTracks().forEach(t=>t.stop());
    if (liveBestMatch) {
        resultBox.innerHTML = `
            <hr>
            <h3 style="color:green">Live Match Found</h3>
            <p><b>Name:</b> ${liveBestMatch.name}</p>
            <p><b>Age:</b> ${liveBestMatch.age}</p>
            <p><b>Distance:</b> ${liveBestDistance.toFixed(3)}</p>
            <img src="uploads/persons/${liveBestMatch.photo}">
        `;
    } else {
        resultBox.innerHTML = "<h3 style='color:red'>No Match Found (Live)</h3>";
    }
    if(liveBestMatch){
        fetch("user_match_face.php", {
            method: "POST",
            headers: {"Content-Type":"application/x-www-form-urlencoded"},
            body: new URLSearchParams({
                matched_person: liveBestMatch.name,
                match_distance: liveBestDistance.toFixed(4),
                match_status: "Matched",
                match_source: "LIVE"
            })
        });
    }

    liveBestMatch = null;
    liveBestDistance = 0.6;
    liveInterval = null;
    stream = null;
    videoPreview.style.display = "none";
    startLiveBtn.disabled = false;
    stopLiveBtn.disabled = true;
}
</script>

</body>
</html>
