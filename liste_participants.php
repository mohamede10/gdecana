<?php
require_once 'config.php';

// Récupérer tous les participants
$stmt = $pdo->query("SELECT * FROM participants ORDER BY date_inscription DESC");
$participants = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des participants - CJP FODR 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg {
            background: linear-gradient(135deg, #0a0f2c 0%, #0e1a3a 50%, #1a1f4a 100%);
        }
        
        /* Style pour l'avatar dans le tableau */
        .avatar-cell {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0 auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* Modal pour agrandir la photo */
        .modal-photo {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(8px);
            z-index: 9999;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }
        
        .modal-photo img {
            max-width: 90%;
            max-height: 90%;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        }
        
        .modal-photo.active {
            display: flex;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen p-8">
    <div class="max-w-7xl mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden">
        <div class="p-6 bg-white border-b">
            <h1 class="text-3xl font-black text-black">Liste des inscrits - CJP FODR 2026</h1>
            <p class="text-gray-500">Total : <?php echo count($participants); ?> participants</p>
        </div>
        
        <div class="overflow-x-auto p-6">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Détail</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date d'inscription</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach($participants as $p): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="avatar-cell" style="background-image: url('<?php echo $p['photo_base64']; ?>'); cursor: pointer;" onclick="showPhoto('<?php echo $p['photo_base64']; ?>', '<?php echo htmlspecialchars($p['nom']); ?>')"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-yellow-600"><?php echo htmlspecialchars($p['code_id']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $p['id']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($p['nom']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 py-1 rounded-full text-xs <?php echo $p['statut'] == 'Participant' ? 'bg-blue-100 text-blue-800' : ($p['statut'] == 'Formateur' ? 'bg-green-100 text-green-800' : ($p['statut'] == 'Paneliste' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800')); ?>">
                                <?php echo $p['statut']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?php 
                            if($p['statut'] == 'Formateur') echo htmlspecialchars($p['titre_formation']);
                            elseif($p['statut'] == 'Paneliste') echo htmlspecialchars($p['theme_panel']);
                            else echo '-';
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('d/m/Y H:i', strtotime($p['date_inscription'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="p-6 bg-gray-50 text-center flex gap-4 justify-center">
            <a href="generer_affiche_cjp_fodr_2026.php" class="bg-yellow-500 text-white font-bold px-6 py-2 rounded-xl hover:bg-black transition">Retour au formulaire</a>
            <button onclick="window.print()" class="bg-gray-600 text-white font-bold px-6 py-2 rounded-xl hover:bg-gray-700 transition">🖨️ Imprimer</button>
            <button onclick="exportToCSV()" class="bg-green-600 text-white font-bold px-6 py-2 rounded-xl hover:bg-green-700 transition">📊 Exporter CSV</button>
        </div>
    </div>
    
    <!-- Modal pour agrandir la photo -->
    <div id="photoModal" class="modal-photo" onclick="closeModal()">
        <div style="position: relative;">
            <img id="modalImage" src="" alt="">
            <div id="modalCaption" style="position: absolute; bottom: -40px; left: 0; right: 0; text-align: center; color: white; font-weight: 500;"></div>
            <button onclick="closeModal()" style="position: absolute; top: -40px; right: -40px; background: rgba(255,255,255,0.2); border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; color: white; font-size: 20px;">✕</button>
        </div>
    </div>
    
    <script>
        // Fonction pour afficher la photo en grand
        function showPhoto(photoUrl, nom) {
            const modal = document.getElementById('photoModal');
            const modalImage = document.getElementById('modalImage');
            const modalCaption = document.getElementById('modalCaption');
            
            modalImage.src = photoUrl;
            modalCaption.innerHTML = nom;
            modal.classList.add('active');
        }
        
        // Fonction pour fermer la modal
        function closeModal() {
            const modal = document.getElementById('photoModal');
            modal.classList.remove('active');
        }
        
        // Fermer avec la touche Echap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
        
        // Fonction pour exporter en CSV
        function exportToCSV() {
            const rows = [];
            // En-têtes
            rows.push(['Code', 'ID', 'Nom', 'Statut', 'Détail', 'Date d\'inscription']);
            
            <?php foreach($participants as $p): ?>
            rows.push([
                '<?php echo addslashes($p['code_id']); ?>',
                '<?php echo $p['id']; ?>',
                '<?php echo addslashes($p['nom']); ?>',
                '<?php echo $p['statut']; ?>',
                '<?php echo addslashes($p['statut'] == 'Formateur' ? $p['titre_formation'] : ($p['statut'] == 'Paneliste' ? $p['theme_panel'] : '-')); ?>',
                '<?php echo date('d/m/Y H:i', strtotime($p['date_inscription'])); ?>'
            ]);
            <?php endforeach; ?>
            
            const csvContent = rows.map(row => row.join(';')).join('\n');
            const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.href = url;
            link.setAttribute('download', 'liste_participants_cjp_2026.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
    </script>
    
    <style>
        /* Animation pour la modal */
        .modal-photo {
            transition: opacity 0.3s ease;
        }
        
        .modal-photo img {
            animation: zoomIn 0.3s ease-out;
        }
        
        @keyframes zoomIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        /* Style pour l'impression */
        @media print {
            .modal-photo, .bg-gray-50, button {
                display: none;
            }
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .max-w-7xl {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</body>
</html>