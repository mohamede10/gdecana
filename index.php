<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Bienvenue</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      font-family: 'Courier New', Courier, monospace;
      overflow: hidden;
    }

    .background {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('img/unive_labe.jpg') no-repeat center center;
      background-size: cover;
      filter: blur(0px);
      z-index: -1;
    }

    .content {
      position: relative;
      height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: #fff;
      background-color: rgba(0, 0, 0, 0.4);
      padding: 20px;
    }

    .logo {
      width: 400px;
      animation: pulse 2s ease-in-out infinite;
      margin-bottom: 20px;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    .typing-text {
      font-size: 2em;
      border-right: 2px solid #fff;
      white-space: nowrap;
      overflow: hidden;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
    }

    @keyframes clignoter {
      0%, 100% { opacity: 1; }
      50% { opacity: 0; }
    }

    .clignotant {
      animation: clignoter 2s infinite;
    }

    @media screen and (max-width: 768px) {
      .typing-text {
        font-size: 1.5em;
      }
      .logo {
        width: 80px;
      }
    }
  </style>
</head>
<body>

  <div class="background"></div>

  <div class="content">
    <img src="img/Logo_univ_labe.png" alt="Logo" class="logo">
    <div class="typing-text" id="typedText"></div>
  </div>

  <audio id="typeSound" src="typewriter.mp3" preload="auto" muted></audio>

  <script>
    const textLines = [
      "Université de Labé",
      "Services Scolarité"
    ];

    const typedText = document.getElementById("typedText");
    const typeSound = document.getElementById("typeSound");

    let lineIndex = 0;
    let charIndex = 0;

    function typeLine() {
      if (lineIndex < textLines.length) {
        if (charIndex < textLines[lineIndex].length) {
          typedText.innerHTML += textLines[lineIndex].charAt(charIndex);
          try {
            typeSound.currentTime = 0;
            typeSound.play();
          } catch (e) {
            console.warn("Lecture son bloquée : ", e);
          }
          charIndex++;
          setTimeout(typeLine, 70);
        } else {
          typedText.innerHTML += "<br>";
          charIndex = 0;
          lineIndex++;
          setTimeout(typeLine, 500);
        }
      } else {
        // Redirection automatique avec transition
        setTimeout(() => {
          document.body.style.transition = "opacity 1s ease";
          document.body.style.opacity = 0;
          setTimeout(() => {
            window.location.href = "generer_affiche_cjp_fodr_2026.php";
          }, 1000);
        }, 1000); // petite pause avant redirection
      }
    }

    // Démarrer l’écriture et débloquer l’autoplay si possible
    window.onload = () => {
      typeSound.play().then(() => {
        typeSound.pause();
        typeSound.currentTime = 0;
        typeSound.muted = false;
        typeLine();
      }).catch((e) => {
        console.warn("Autoplay bloqué, on continue sans le son : ", e);
        typeLine();
      });
    };
  </script>

</body>
</html>
