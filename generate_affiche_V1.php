<?php
// ========== AFFICHAGE DU FORMULAIRE ==========
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ?>
   
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>CJP 2026 - Générateur d'affiche "J'Y SERAI"</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
            body { font-family: 'Inter', sans-serif; }
            .gradient-bg {
                background: linear-gradient(135deg, #0a0f2c 0%, #0e1a3a 50%, #1a1f4a 100%);
            }
            
            .hidden-field {
                display: none;
            }
        </style>
    </head>
    <body class="gradient-bg min-h-screen flex items-center justify-center p-6">
        <div class="max-w-2xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-white p-6 text-center border-b">
                <div class="mb-2">
                    <img src="uploads/logos/csguinee-logo.jpeg" alt="" class="w-40 mx-auto">
                </div>
                <h1 class="text-6xl font-black text-black">CJP #FODR 2026</h1>
                <p class="text-gray-500 text-1xl mt-1">Forum du Développement et Réseaux - 4ème Édition</p>
            </div>         
            <div class="text-center text-xs text-gray-500 pt-4">
                    Du 15 au 16 Mai 2026 — Université de Labé, De 09h à 17h<br>
                    Organisé par le Club de Jeunes Programmeurs (CJP)
                </div>
            <!-- Formulaire -->
            <form action="" method="POST" enctype="multipart/form-data" class="p-8 space-y-5" id="participantForm">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nom complet *</label>
                    <input type="text" name="nom" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#008878] focus:outline-none transition">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Statut *</label>
                    <select name="statut" id="statut" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#008878] focus:outline-none transition">
                        <option value="Participant">Participant</option>
                        <option value="Formateur">Formateur</option>
                        <option value="Paneliste">Paneliste</option>
                        <option value="Partenaire">Partenaire</option>
                    </select>
                </div>
                
                <!-- Champ pour le titre de formation (visible uniquement pour Formateur) -->
                <div id="formationField" class="hidden-field">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Titre de la formation *</label>
                    <input type="text" name="titre_formation" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#008878] focus:outline-none transition" placeholder="Ex: Introduction à l'IA">
                </div>
                
                <!-- Champ pour le thème du panel (visible uniquement pour Paneliste) -->
                <div id="themeField" class="hidden-field">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Thème du panel *</label>
                    <select name="theme_panel" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#008878] focus:outline-none transition">
                        <option value="">Sélectionnez un thème</option>
                        <option value="Cloud souverain et hébergement local enjeux et opportunités"> 15 MAI - Cloud souverain et hébergement local enjeux et opportunités</option>
                        <option value="Genre et inclusion numérique impact de l'IA dans la transformation de l'emploi"> 16 MAI - Genre et inclusion numérique impact de l'IA dans la transformation de l'emploi</option>
                        <option value="Rôle du numérique dans la construction des alumnis au développement et l'innovation dans les institutions">16 MAI - Rôle du numérique dans la construction des alumnis au développement et l'innovation dans les institutions</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Photo de profil *</label>
                    <input type="file" name="photo" accept="image/jpeg,image/png" required class="w-full px-4 py-2 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1">Format JPG/PNG - 500x500px recommandé</p>
                </div>
                
                <button type="submit" class="w-full bg-yellow-600 text-white font-bold py-4 rounded-xl hover:bg-black transition">
                    ✨ GÉNÉRER MON AFFICHE "J'Y SERAI A LABE"
                </button>

                <button type="button" class="w-full bg-black text-white font-bold py-4 rounded-xl hover:bg-yellow-600 transition">
                    <a href="https://www.club-jp.com/" class="text-white no-underline">FERMER</a>
                </button>
            </form>
        </div>
    </body>
    
    <script>
        // Gestion de l'affichage conditionnel des champs
        const statutSelect = document.getElementById('statut');
        const formationField = document.getElementById('formationField');
        const themeField = document.getElementById('themeField');
        
        function updateFields() {
            const statut = statutSelect.value;
            
            // Cacher tous les champs d'abord
            formationField.classList.add('hidden-field');
            themeField.classList.add('hidden-field');
            
            // Désactiver les champs cachés
            document.querySelector('input[name="titre_formation"]').disabled = true;
            document.querySelector('select[name="theme_panel"]').disabled = true;
            
            // Afficher le champ approprié selon le statut
            if (statut === 'Formateur') {
                formationField.classList.remove('hidden-field');
                document.querySelector('input[name="titre_formation"]').disabled = false;
                document.querySelector('input[name="titre_formation"]').required = true;
            } else if (statut === 'Paneliste') {
                themeField.classList.remove('hidden-field');
                document.querySelector('select[name="theme_panel"]').disabled = false;
                document.querySelector('select[name="theme_panel"]').required = true;
            } else {
                // Participant ou Partenaire - pas de champ supplémentaire
                document.querySelector('input[name="titre_formation"]').required = false;
                document.querySelector('select[name="theme_panel"]').required = false;
            }
        }
        
        statutSelect.addEventListener('change', updateFields);
        
        // Initialiser au chargement
        updateFields();
    </script>
    
    <style>
        .hidden-field {
            display: none;
        }
    </style>
    </html>
    <?php
    exit;
}

// ========== TRAITEMENT POST (SANS BDD) ==========
$nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
$statut = htmlspecialchars($_POST['statut'] ?? 'Participant');
$titre_formation = htmlspecialchars(trim($_POST['titre_formation'] ?? ''));
$theme_panel = htmlspecialchars(trim($_POST['theme_panel'] ?? ''));

// Validation du nom
if (empty($nom)) {
    die("<div class='bg-red-100 text-red-700 p-4 rounded-lg text-center'>❌ Erreur: Le nom est obligatoire. <a href='javascript:history.back()'>Retour</a></div>");
}

// Validation selon le statut
if ($statut === 'Formateur' && empty($titre_formation)) {
    die("<div class='bg-red-100 text-red-700 p-4 rounded-lg text-center'>❌ Erreur: Veuillez saisir le titre de la formation. <a href='javascript:history.back()'>Retour</a></div>");
}

if ($statut === 'Paneliste' && empty($theme_panel)) {
    die("<div class='bg-red-100 text-red-700 p-4 rounded-lg text-center'>❌ Erreur: Veuillez sélectionner un thème pour le panel. <a href='javascript:history.back()'>Retour</a></div>");
}

// Déterminer le texte à afficher pour la fonction
$fonction_affiche = $statut;
if ($statut === 'Formateur' && !empty($titre_formation)) {
    $fonction_affiche = 'Formateur - Formation : ' . $titre_formation;
} elseif ($statut === 'Paneliste' && !empty($theme_panel)) {
    // Extraire le thème sans la date
    $theme_clean = preg_replace('/^\d{1,2} MAI - /', '', $theme_panel);
    $fonction_affiche = 'Paneliste - Thème : ' . $theme_clean;
} elseif ($statut === 'Partenaire') {
    $fonction_affiche = 'Partenaire';
}

// Génération d'un code unique sans BDD
$code_unique = 'CJP-FODR-2026-' . strtoupper(substr(uniqid(), -6));

// Gestion de la photo
$photo_base64 = '';
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $photo_tmp = $_FILES['photo']['tmp_name'];
    $photo_type = $_FILES['photo']['type'];
    
    // Vérifier le type de fichier
    if (strpos($photo_type, 'jpeg') !== false || strpos($photo_type, 'png') !== false) {
        $photo_data = file_get_contents($photo_tmp);
        $photo_base64 = 'data:' . $photo_type . ';base64,' . base64_encode($photo_data);
    } else {
        die("<div class='bg-red-100 text-red-700 p-4 rounded-lg text-center'>❌ Format non supporté. Utilisez JPG ou PNG. <a href='javascript:history.back()'>Retour</a></div>");
    }
} else {
    die("<div class='bg-red-100 text-red-700 p-4 rounded-lg text-center'>❌ Veuillez sélectionner une photo. <a href='javascript:history.back()'>Retour</a></div>");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affiche CJP FODR-2026 - <?php echo $nom; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-6">
    
    <!-- AFFICHE À CAPTURER -->
    <div id="affiche" class="w-[800px] shadow-2xl overflow-hidden" style="background: linear-gradient(45deg, #92d7ff 0%, #fffcfc 50%, #92d7ff 100%);">
        
        <!-- En-tête avec logo -->
        <div class="p-4">
            <div class="flex items-center justify-center">
                <div class="mt-4">
                    <img src="img/logo.png" alt="cjp" class="w-40 rounded-xl">
                </div>
                <div class="text-center">
                    <h1 class="text-5xl font-black text-black">CJP #FODR 2026</h1> <br>
                    <p class="text-black text-xl mt-1">FORUM DU DÉVELOPPEMENT ET RÉSEAUX</p> <br>
                    <span class="text-yellow-500 text-2xl">4ÈME ÉDITION</span>
                </div>  
            </div>
        </div>
        <hr class="border-1xl">
        
        <!-- Contenu principal -->
        <div class="p-8">
            <div class="flex flex-col items-center">
                <div class="flex items-end justify-center gap-6">
                    <!-- Photo -->
                    <div class="ml-10">
                        <div class="w-80 h-80 overflow-hidden border-1 border-blue-200 shadow-2xl relative">
                            <div class="w-full h-full bg-center bg-cover" style="background-image: url('<?php echo $photo_base64; ?>');"></div>
                            <div class="absolute bottom-0 left-0 w-full" style="height: 70px; background: linear-gradient(to top, rgb(255, 255, 255) 0%, rgba(255, 255, 255, 0.05) 40%, transparent 100%); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(6px); pointer-events: none;"></div>
                        </div>
                    </div>          
                    
                    <!-- Badge J'Y SERAI -->
                    <div class="relative px-8 -top-20 py-6 text-center min-w-[180px] transform rotate-1 transition ml-20 mb-20">
                        <div class="absolute top-12 -left-4 text-4xl animate-pulse">✨</div>
                        <div class="text-black font-black text-5xl leading-tight tracking-tighter">J'Y<br>SERAI !</div>
                    </div>
                </div>
                
                <!-- Nom et Fonction -->
                <div class="flex items-center justify-between gap-6 mt-8 ml-10">
                    <div class="text-left relative -ml-15 w-64 flex-shrink-0">
                        <div class="w-full">
                            <h6 class="text-2xl text-center font-black text-gray-800 break-words whitespace-normal"><?php echo strtoupper($nom); ?></h6>
                            <div class="flex justify-center">
                                <p class="font-black mt-2 text-black p-2 inline-block text-center"><?php echo $fonction_affiche; ?></p>
                            </div>
                            <svg class="absolute -bottom-6 left-0 w-full h-10" viewBox="0 0 400 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M 20 10 Q 200 35, 380 10" stroke="#000000e0" stroke-width="3" stroke-linecap="round" fill="none"/>
                            </svg>
                        </div>
                    </div>

                     <!-- Ligne courbe verticale entre J'Y SERAI et la date -->
                    <div class="absolute left-1/2 transform -translate-x-1/2" style="margin-left: 350px; margin-bottom: 250px;">
                        <svg width="60" height="180" viewBox="0 0 60 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M 10 10 C 40 60, 40 120, 10 170" 
                                stroke="#000000" 
                                stroke-width="6" 
                                stroke-linecap="round"
                                fill="none"/>
                            <circle cx="10" cy="10" r="10" fill="#000000"/>
                        </svg>
                    </div>
                    
                    <!-- Date -->
                    <div class="-mt-40 ml-20 w-full flex justify-center">
                        <div class="px-6 py-3 rounded-xl inline-block">
                            <div class="text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="font-semibold text-black text-1xl">Vendredi et Samedi</span>
                                    <span class="w-20 h-20 rounded-full bg-black flex items-center justify-center shadow-md"> 
                                        <span class="text-2xl font-black text-white" style="margin-top: -20px;">15<br>16</span>
                                    </span>
                                    <span class="font-semibold text-black text-1xl">MAI 2026</span>
                                </div>
                                <span class="font-semibold text-black mt-3 flex items-center justify-center gap-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>Université de Labé
                                </span>
                            </div>
                        </div>
                    </div>
                </div>        
            </div>
            
            <!-- Partenaires -->
            <br>
            <div class="flex justify-between items-center border-b border-yellow-400 pb-1 mb-3">
                <p>Panel, Formations, Hackathon et Présentation de projets</p>
                <p class="text-xs font-bold text-black uppercase tracking-wider">Nos partenaires</p>
            </div>
            
            <!-- Logos partenaires -->
            <div class="flex items-center justify-center gap-2 mt-4 flex-wrap">
                <img src="img/ansuten.jpeg" alt="" class="w-20 rounded-xl p-2">
                <img src="img/sabutech.jpeg" alt="" class="w-20 rounded-xl p-2">
                <img src="img/guineedev.jpeg" alt="" class="w-20 rounded-xl p-2">
                <img src="img/novtec.jpeg" alt="" class="w-20 rounded-xl p-2">
                <img src="img/anssi.jpg" alt="" class="w-20 rounded-xl p-2">
                <img src="img/kumy.jpeg" alt="" class="w-20 rounded-xl p-2">
                <img src="img/eti.jpeg" alt="" class="w-20 rounded-xl p-2">
                <img src="img/santoulab.jpeg" alt="" class="w-20 rounded-xl p-2">
                <img src="img/ebooster.jpeg" alt="" class="w-20 rounded-xl p-2">
                <img src="img/technologiehouse.jpeg" alt="" class="w-20 rounded-xl p-2">
                <img src="img/teelaf.jpeg" alt="" class="w-20 rounded-xl p-2">  
                <img src="img/senditoo.jpeg" alt="" class="w-20 rounded-xl p-2">
                <img src="img/paycard.jpeg" alt="" class="w-20 rounded-xl p-2">   
                <img src="img/fata.jpeg" alt="" class="w-20 rounded-xl p-2">
                <img src="img/elnex.jpeg" alt="" class="w-20 rounded-xl p-2">   
                <img src="img/digitalis.jpeg" alt="" class="w-20 rounded-xl p-2">   
            </div>
        </div>
        
        <!-- Footer -->
        <div class="p-6 text-center" style="background: linear-gradient(45deg, #92d7ff 0%, #92d7ff 50%, #ffffff 100%);">
            <div class="flex justify-between items-center">
                <div class="text-left">
                    <p class="text-black text-1xl">@CJP-FODR-2026</p>
                    <p class="text-black text-1xl">www.club-jp.com</p>
                </div>
                <div class="text-right">
                    <div class="mt-4">
                        <img src="img/Logo_univ_labe.png" alt="Logo-CSGUINEE" class="w-40 rounded-xl">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Boutons -->
    <div class="mt-8 flex gap-4">
        <button onclick="downloadAffiche(event)" class="bg-yellow-500 text-white font-bold px-8 py-3 rounded-xl">
            ⬇️ TÉLÉCHARGER L'AFFICHE
        </button>
        <button class="bg-black text-white font-bold px-8 py-3 rounded-xl">
            <a href="https://www.club-jp.com/fodr/" target="_blank">Voir les détails de l'événement</a>
        </button>
    </div>
    
    <script>
        async function downloadAffiche(event) {
            const element = document.getElementById('affiche');
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '⏳ GÉNÉRATION...';
            btn.disabled = true;
            
            try {
                const canvas = await html2canvas(element, {
                    scale: 2.5,
                    backgroundColor: '#ffffff',
                    logging: false,
                    useCORS: true,
                    allowTaint: false
                });
                
                const link = document.createElement('a');
                link.download = 'affiche_cjp_<?php echo $nom; ?>.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de la génération: ' + error.message);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>