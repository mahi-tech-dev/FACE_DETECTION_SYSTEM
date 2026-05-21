<!DOCTYPE html>
<html>
<head>
    <title>Live Face Detection (CPU Mode)</title>

    <!-- TensorFlow CPU backend -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.21.0/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-wasm@3.21.0/dist/tf-backend-wasm.min.js"></script>

    <!-- Face API -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <style>
        body { text-align: center; }
        #wrap {
            position: relative;
            width: 640px;
            height: 480px;
            margin: auto;
            border: 2px solid black;
        }
        video, canvas {
            position: absolute;
            top: 0;
            left: 0;
        }
    </style>
</head>

<body>

<h2>Live Face Detection (CPU / WASM Mode)</h2>

<div id="wrap">
    <video id="video" autoplay muted playsinline></video>
</div>

<script>
const video = document.getElementById("video");
const wrap = document.getElementById("wrap");

async function init() {
    // Force CPU backend
    await tf.setBackend('wasm');
    await tf.ready();

    console.log("CPU backend enabled");

    await faceapi.nets.ssdMobilenetv1.loadFromUri("models");
    await faceapi.nets.faceLandmark68Net.loadFromUri("models");
    await faceapi.nets.faceRecognitionNet.loadFromUri("models");

    startCamera();
}

async function startCamera() {
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.srcObject = stream;
    video.play();

    waitForVideo();
}

function waitForVideo() {
    if (video.videoWidth === 0) {
        requestAnimationFrame(waitForVideo);
        return;
    }
    startDetection();
}

function startDetection() {
    const canvas = faceapi.createCanvasFromMedia(video);
    wrap.appendChild(canvas);

    const size = {
        width: video.videoWidth,
        height: video.videoHeight
    };

    video.width = size.width;
    video.height = size.height;
    canvas.width = size.width;
    canvas.height = size.height;

    faceapi.matchDimensions(canvas, size);

    async function detect() {
        const detections = await faceapi
    .detectAllFaces(
        video,
        new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 })
    )
    .withFaceLandmarks()
    .withFaceDescriptors();

        const resized = faceapi.resizeResults(detections, size);
        const ctx = canvas.getContext("2d"); 
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        faceapi.draw.drawDetections(canvas, resized);
        faceapi.draw.drawFaceLandmarks(canvas, resized);

        requestAnimationFrame(detect);
    }

    detect();
}

init();
</script>

</body>
</html>
