// faceid_script.js

// Elementos del DOM
const video = document.getElementById('videoElement');
const canvas = document.getElementById('canvas');
const captureButton = document.getElementById('captureButton');
const switchButton = document.getElementById('switchCameraButton');
const submitButton = document.getElementById('submitButton');
const stepMessage = document.getElementById('stepMessage');
const idImage = document.getElementById('idImage');
const selfieImage = document.getElementById('selfieImage'); 
const idDataInput = document.getElementById('idDataInput');
const selfieDataInput = document.getElementById('selfieDataInput');
const startVerificationButton = document.getElementById('startVerificationButton');
const cameraSection = document.querySelector('.camera-section'); 
const cameraContainer = document.querySelector('.camera-container'); // Definición del contenedor

let currentStream = null;
let currentStep = 'ID'; // Estados: 'ID', 'ID_CONFIRM', 'PAPER_PHOTO', 'PAPER_CONFIRM', 'ID_SELFIE'
let isFrontCamera = false;
let videoDevices = [];
let currentDeviceId = null;

// --- CORRECCIÓN DEL ERROR DE NODO (LÍNEAS 30-40) ---

// 1. Campo oculto para la data del papel y añadido al formulario.
const paperDataInput = document.createElement('input');
paperDataInput.type = 'hidden';
paperDataInput.name = 'paper_data';
document.getElementById('faceIdForm').appendChild(paperDataInput);

// 2. Elemento <img> para la previsualización del papel.
const paperImage = document.createElement('img');
paperImage.id = 'paperImage';
paperImage.alt = 'Captured Paper Photo';
paperImage.style.display = 'none';

// CORRECCIÓN: Usamos appendChild en el contenedor. Esto es más robusto y evita el error NotFoundError.
cameraContainer.appendChild(paperImage); 

// --- FIN CORRECCIÓN ---


// Helper function for video constraints
function getVideoConstraints(deviceId, preferFront) {
    let facingMode = preferFront ? 'user' : 'environment'; 
    let constraints = {
        video: {
            deviceId: deviceId ? { exact: deviceId } : undefined,
            facingMode: facingMode
        },
        audio: false
    };
    return constraints;
}


// 1. Initialize Camera (Main Function)
async function startCamera(deviceId = null, preferFront = false) {
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
    }
    
    let constraints = getVideoConstraints(deviceId, preferFront);

    try {
        currentStream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = currentStream;
        video.play();
        
        const videoTrack = currentStream.getVideoTracks()[0];
        const settings = videoTrack.getSettings();
        
        const isUserFacing = settings.facingMode === 'user' || (videoTrack.label && videoTrack.label.toLowerCase().includes('front'));

        // Efecto espejo para cámara frontal
        video.style.transform = (isUserFacing) ? 'scaleX(-1)' : 'scaleX(1)'; 
        isFrontCamera = isUserFacing;
        video.style.display = 'block'; 
        currentDeviceId = settings.deviceId; 

    } catch (err) {
        // Mensaje de error para el usuario (Error de permisos)
        stepMessage.innerHTML = '<span style="color:red">Error: No se puede acceder a la cámara. Asegúrate de dar permiso.</span>';
        console.error("Error accessing camera: ", err);
    }
}

// 2. Get and set up camera list
async function getCameras() {
    try {
        // Pedir permisos brevemente para obtener la lista de dispositivos
        currentStream = await navigator.mediaDevices.getUserMedia({video: true});
        currentStream.getTracks().forEach(track => track.stop()); 
    } catch(e) {
        // Si hay error en la solicitud de permisos, mostramos el mensaje de error de permisos
        stepMessage.innerHTML = '<span style="color:red">Error: No se puede acceder a la cámara. Asegúrate de dar permiso.</span>';
        return;
    }
    
    videoDevices = (await navigator.mediaDevices.enumerateDevices()).filter(device => device.kind === 'videoinput');
    
    if (videoDevices.length > 0) {
        // Intentar iniciar con la cámara trasera (ambiente) para el ID
        const rearCamera = videoDevices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('environment'));
        
        if (rearCamera) {
            currentDeviceId = rearCamera.deviceId;
            startCamera(currentDeviceId, false); 
        } else {
            // Si no hay trasera, usar la primera disponible
            currentDeviceId = videoDevices[0].deviceId;
            startCamera(currentDeviceId, false); 
        }
    }
    
    // Ocultar botón de switch si solo hay 1 cámara
    if (videoDevices.length <= 1) {
        switchButton.style.display = 'none'; 
    }
}

// 3. Switch Camera
switchButton.addEventListener('click', async () => {
    const otherCamera = videoDevices.find(d => d.deviceId !== currentDeviceId);
    
    if (otherCamera) {
         currentDeviceId = otherCamera.deviceId;
         isFrontCamera = !isFrontCamera; 
         startCamera(currentDeviceId, isFrontCamera);
    }
});


// 4. Capture Photo and manage flow
captureButton.addEventListener('click', () => {
    if (!currentStream) return;
    
    // Configuración de captura
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const context = canvas.getContext('2d');
    
    // Aplicar transformación espejo si es frontal
    if (isFrontCamera) { 
        context.translate(canvas.width, 0); 
        context.scale(-1, 1); 
    }
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    const photoData = canvas.toDataURL('image/jpeg', 0.9);
    
    // Resetear transformación
    context.setTransform(1, 0, 0, 1, 0, 0);
    
    
    // PROCESAR SEGÚN EL PASO
    if (currentStep === 'ID') {
        // --- Paso 1: Foto del ID ---
        idDataInput.value = photoData;
        idImage.src = photoData;
        
        currentStream.getTracks().forEach(track => track.stop());
        video.style.display = 'none';
        idImage.style.display = 'block';
        
        captureButton.textContent = 'Continue to Paper Photo';
        switchButton.style.display = 'none';
        currentStep = 'ID_CONFIRM';
        stepMessage.textContent = 'Step 1: ID Photo captured. Click to continue.';
        
    } else if (currentStep === 'ID_CONFIRM') {
        // Confirmación ID -> Iniciar Paso 2 (Papel)
        
        stepMessage.textContent = 'Step 2: Capture your Paper Photo (Email and Date)'; 
        idImage.style.display = 'none';
        
        captureButton.textContent = 'Take Paper Photo'; 
        switchButton.style.display = 'block';
        currentStep = 'PAPER_PHOTO';
        
        // Re-iniciar la cámara (trasera si es posible, para capturar un documento)
        const rearCamera = videoDevices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('environment'));
        const rearDeviceId = rearCamera ? rearCamera.deviceId : videoDevices[0].deviceId;
        startCamera(rearDeviceId, false);

    } else if (currentStep === 'PAPER_PHOTO') {
        // --- Paso 2: Foto del Papel ---
        paperDataInput.value = photoData; 
        paperImage.src = photoData;
        
        currentStream.getTracks().forEach(track => track.stop());
        video.style.display = 'none';
        paperImage.style.display = 'block';
        
        captureButton.textContent = 'Continue to Selfie with ID';
        switchButton.style.display = 'none';
        currentStep = 'PAPER_CONFIRM';
        stepMessage.textContent = 'Step 2: Paper Photo captured. Click to continue.';

    } else if (currentStep === 'PAPER_CONFIRM') {
        // Confirmación Papel -> Iniciar Paso 3 (Selfie con ID)

        stepMessage.textContent = 'Step 3: Capture your Selfie with ID (Face and ID visible)'; 
        paperImage.style.display = 'none';
        
        captureButton.textContent = 'Take Selfie with ID'; 
        switchButton.style.display = 'block';
        currentStep = 'ID_SELFIE';
        
        // Re-iniciar la cámara con la configuración frontal (selfie)
        const frontCamera = videoDevices.find(d => d.label.toLowerCase().includes('front') || d.label.toLowerCase().includes('user'));
        const frontDeviceId = frontCamera ? frontCamera.deviceId : videoDevices[0].deviceId;
        startCamera(frontDeviceId, true);


    } else if (currentStep === 'ID_SELFIE') {
        // --- Paso 3: Foto Selfie con ID ---
        selfieDataInput.value = photoData;
        selfieImage.src = photoData;
        
        currentStream.getTracks().forEach(track => track.stop());
        video.style.display = 'none';
        selfieImage.style.display = 'block';
        
        captureButton.style.display = 'none';
        switchButton.style.display = 'none';
        submitButton.style.display = 'block'; 
        stepMessage.textContent = 'Process completed. Click the button below to submit.';
    }
});

// KEY: Lógica de inicio de verificación
startVerificationButton.addEventListener('click', () => {
    // 1. Ocultar el botón de inicio de verificación
    startVerificationButton.style.display = 'none'; 
    
    // 2. Mostrar la sección principal de la cámara
    cameraSection.style.display = 'block';
    
    // 3. Mostrar los botones de acción que estaban ocultos por CSS
    captureButton.style.display = 'block';
    // switchButton.style.display se manejará dentro de getCameras.

    // 4. Inicializar la cámara
    getCameras();                                   
});