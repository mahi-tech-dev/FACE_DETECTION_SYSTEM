<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'user'){
    echo "Access denied!";
    exit();
}
?>
<h2>Live Face Recognition</h2>
<video id="video" width="720" height="560" autoplay muted></video>
<p id="status">Loading models...</p>

<script defer src="https://cdn.jsdelivr.net/npm/face-api.js"></script>
<script>
const video = document.getElementById('video');

Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
    faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('/models')
]).then(startVideo);

function startVideo(){
    navigator.getUserMedia(
        { video:{} },
        stream => video.srcObject = stream,
        err => console.error(err)
    );
    document.getElementById('status').innerText = "Camera opened. Models loaded.";
}

// You can add face recognition matching logic here
</script>
