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

let currentStream = null;
let currentStep = 'ID'; // 'ID', 'ID_CONFIRM', o 'SELFIE'
let isFrontCamera = false;
let videoDevices = [];
let currentDeviceId = null;

// Función auxiliar para obtener las restricciones de video
function getVideoConstraints(deviceId, preferFront) {
    // Si la cámara frontal está fallando, evitamos el 'exact'
    let facingMode = preferFront ? 'user' : 'environment'; 
    
    // Si tenemos un DeviceId específico, lo usamos
    let constraints = {
        video: {
            deviceId: deviceId ? { exact: deviceId } : undefined,
            facingMode: facingMode
        },
        audio: false
    };

    return constraints;
}


// 1. Inicializar la cámara (Función Principal)
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
        
        // Determinar si reflejar la imagen (si es 'user' o si la etiqueta indica frontal)
        const isUserFacing = settings.facingMode === 'user' || (videoTrack.label && videoTrack.label.toLowerCase().includes('front'));

        video.style.transform = (isUserFacing) ? 'scaleX(-1)' : 'scaleX(1)'; 
        isFrontCamera = isUserFacing;
        video.style.display = 'block'; 
        currentDeviceId = settings.deviceId; // Guardar el DeviceId actual

    } catch (err) {
        // En caso de error, mostrar el mensaje específico
        stepMessage.innerHTML = '<span style="color:red">Error: No se puede acceder a la cámara. Asegúrate de dar permiso.</span>';
        console.error("Error al acceder a la cámara: ", err);
    }
}

// 2. Obtener y configurar la lista de cámaras
async function getCameras() {
    // Pedir permisos inicialmente para asegurar que enumerateDevices devuelva etiquetas útiles
    try {
        currentStream = await navigator.mediaDevices.getUserMedia({video: true});
        currentStream.getTracks().forEach(track => track.stop()); 
    } catch(e) {
        // Si no hay permisos, startCamera manejará el error al intentar activarla
    }
    
    videoDevices = (await navigator.mediaDevices.enumerateDevices()).filter(device => device.kind === 'videoinput');
    
    if (videoDevices.length > 0) {
        // Encontrar la cámara trasera para empezar (ID)
        const rearCamera = videoDevices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('environment'));
        
        if (rearCamera) {
            currentDeviceId = rearCamera.deviceId;
            startCamera(currentDeviceId, false); // Iniciar con la trasera
        } else {
            currentDeviceId = videoDevices[0].deviceId;
            startCamera(currentDeviceId, false); // Iniciar con la primera disponible
        }
    }
    
    if (videoDevices.length <= 1) {
        switchButton.style.display = 'none'; 
    }
}

// 3. Cambiar de cámara (frontal/trasera)
switchButton.addEventListener('click', async () => {
    // Buscar el dispositivo opuesto al actual
    const otherCamera = videoDevices.find(d => d.deviceId !== currentDeviceId);
    
    if (otherCamera) {
         currentDeviceId = otherCamera.deviceId;
         isFrontCamera = !isFrontCamera; // Invertir el estado
         startCamera(currentDeviceId, isFrontCamera);
    }
});


// 4. Capturar la foto y gestionar el flujo
captureButton.addEventListener('click', () => {
    if (!currentStream) return;
    
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const context = canvas.getContext('2d');
    
    // Si es selfie, dibujar reflejado
    if (isFrontCamera) {
        context.translate(canvas.width, 0);
        context.scale(-1, 1);
    }
    
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    const photoData = canvas.toDataURL('image/jpeg', 0.9);
    context.setTransform(1, 0, 0, 1, 0, 0);
    
    
    // PROCESAR SEGÚN EL PASO
    if (currentStep === 'ID') {
        // Guardar ID y pedir confirmación
        idDataInput.value = photoData;
        idImage.src = photoData;
        
        // Esconder video y mostrar preview
        currentStream.getTracks().forEach(track => track.stop());
        video.style.display = 'none';
        idImage.style.display = 'block';
        
        captureButton.textContent = 'Continuar a Selfie';
        switchButton.style.display = 'none';
        currentStep = 'ID_CONFIRM';
        stepMessage.textContent = 'Paso 1: Foto del ID capturada. Haz clic para continuar.';
        
    } else if (currentStep === 'ID_CONFIRM') {
         // Iniciar el paso de Selfie
        stepMessage.textContent = 'Paso 2: Captura de tu Selfie (Rostro centrado)';
        idImage.style.display = 'none';
        
        captureButton.textContent = 'Tomar Selfie';
        switchButton.style.display = 'block';
        currentStep = 'SELFIE';
        
        // Re-iniciar la cámara con la configuración frontal (selfie)
        const frontCamera = videoDevices.find(d => d.label.toLowerCase().includes('front') || d.label.toLowerCase().includes('user'));
        const frontDeviceId = frontCamera ? frontCamera.deviceId : videoDevices[0].deviceId;
        startCamera(frontDeviceId, true);

    } else if (currentStep === 'SELFIE') {
        // Guardar Selfie y finalizar
        selfieDataInput.value = photoData;
        selfieImage.src = photoData;
        
        // Esconder video y mostrar preview
        currentStream.getTracks().forEach(track => track.stop());
        video.style.display = 'none';
        selfieImage.style.display = 'block';
        
        captureButton.style.display = 'none';
        switchButton.style.display = 'none';
        submitButton.style.display = 'block'; // Mostrar botón de envío final
        stepMessage.textContent = 'Proceso completado. Haz clic en el botón de abajo para enviar.';
    }
});

// Iniciar al cargar la página
window.addEventListener('load', getCameras);