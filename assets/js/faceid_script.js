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

// 1. Inicializar la cámara (Función Principal)
async function startCamera(deviceId = null, preferFront = false) {
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
    }

    const constraints = {
        video: {
            // Si se especifica un dispositivo, úsalo
            ...(deviceId && {deviceId: {exact: deviceId}}),
            // Preferencia de cámara (frontal/trasera)
            facingMode: preferFront ? 'user' : { exact: "environment" }
        },
        audio: false
    };

    try {
        currentStream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = currentStream;
        video.play();
        // Reflejar la imagen para selfies (cámara frontal)
        video.style.transform = (preferFront) ? 'scaleX(-1)' : 'scaleX(1)'; 
        isFrontCamera = preferFront;
        video.style.display = 'block'; // Asegurar que el video se muestre
    } catch (err) {
        stepMessage.textContent = 'Error: No se puede acceder a la cámara. Asegúrate de dar permiso.';
        console.error("Error al acceder a la cámara: ", err);
    }
}

// 2. Obtener y configurar la lista de cámaras
async function getCameras() {
    // Pedir permisos inicialmente para asegurar que enumerateDevices devuelva etiquetas útiles
    try {
        currentStream = await navigator.mediaDevices.getUserMedia({video: true});
        currentStream.getTracks().forEach(track => track.stop()); // Detener la cámara inicial
    } catch(e) {
        console.warn("Permiso de cámara no concedido inicialmente.");
    }
    
    videoDevices = (await navigator.mediaDevices.enumerateDevices()).filter(device => device.kind === 'videoinput');
    
    if (videoDevices.length > 0) {
        // Encontrar la cámara trasera para empezar (ID)
        const rearCamera = videoDevices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('environment'));
        
        if (rearCamera) {
            currentDeviceId = rearCamera.deviceId;
            startCamera(currentDeviceId, false); // Iniciar con la trasera (false = no frontal)
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
    // Busca el dispositivo opuesto al actual
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
    
    // Configurar canvas para la captura
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const context = canvas.getContext('2d');
    
    // Si es selfie, dibujar reflejado
    if (isFrontCamera) {
        context.translate(canvas.width, 0);
        context.scale(-1, 1);
    }
    
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Obtener la data de la foto
    const photoData = canvas.toDataURL('image/jpeg', 0.9);
    
    // Limpiar las transformaciones del contexto
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