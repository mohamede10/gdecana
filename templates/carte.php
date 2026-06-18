<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }
        .page {
        width: 21cm;
        height: 29.7cm;
        padding: 20px;
        margin-left: -2cm; /* décale la page un peu à gauche */
        }
        table.carte-table {
        border-collapse: collapse;
        margin: 0 auto;       /* centre le tableau dans la page */
        width: 18cm;          /* largeur totale recto+verso */
        }
        td.carte-cell {
            vertical-align: top;
            padding: 0 15px;      /* espace horizontal entre les deux cartes */
            text-align: center;
        }
        .carte {
            width: 8.5cm;
            height: 5.5cm;
            border: 1px solid black;
            border-radius: 6px;
            box-sizing: border-box;
            page-break-inside: avoid;
            padding: 8px;
            margin: 0 auto; /* centre la carte dans la cellule */
            position: relative;
        }
        .header {
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
        }
        .drapeau {
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, red, yellow, green);
            margin: 4px 0;
        }
        .photo {
            width: 3cm;
            height: 3cm;
          /*  border: 1px solid black;*/
            float: left;
            margin: 5px 8px 5px 0;
            object-fit: cover;
        }
        .infos {
            font-size: 8.5pt;
            line-height: 1.3;
            text-align: left;
        }
        .footer {
            position: absolute;
            bottom: 5px;
            left: 5px;
            right: 5px;
            font-size: 8pt;
        }
    </style>
</head>
<body>
<div class="page">
    <table class="carte-table">
        <tr>
            <!-- Recto -->
            <td class="carte-cell">
                <div class="carte">
                   <table style="width:100%; border:none;">
                      <tr>
                        <!-- Texte centré -->
                        <td style="text-align:center; border:none; font-weight:bold;">
                          RÉPUBLIQUE DE GUINÉE<br>
                          Travail – Justice – Solidarité
                        </td>
                        <!-- Logo à droite -->
                        <td style="text-align:right; border:none;">
                           <img src="assets/Logo_univ_labe.png" alt="Logo Université de Labé" style="max-height: 40px;">
                        </td>
                      </tr>
                   </table>
                    <div style="display:flex; align-items:flex-left;">
                        <img class="photo" src="uploads/photos/<?= htmlspecialchars($etudiant['photo'] ?? 'default.png') ?>" alt="Photo">
                        <div class="infos" style="margin-right:40px;">
                            <b>CARTE D'ÉTUDIANT</b><br>
                            Matricule : <?= htmlspecialchars($etudiant['MatEtu']) ?><br>
                            Nom : <?= htmlspecialchars($etudiant['Nom']) ?><br>
                            Prénoms : <?= htmlspecialchars($etudiant['Prenom']) ?><br>
                            Sexe : <?= htmlspecialchars($etudiant['Sexe'] ?? '') ?><br>
                            Né(e) le : <?= htmlspecialchars($etudiant['DateNais'] ?? '') ?>  
                            à : <?= htmlspecialchars($etudiant['LieuNais'] ?? '') ?><br>
                            Nationalité : <?= htmlspecialchars($etudiant['Nationalite'] ?? '') ?><br>
                        </div>
                    </div>                                                                      
                    <div class="footer">
                        <b>Le Doyen</b><br>
                         <img src="assets/Logo_univ_labe.png" alt="Logo Université de Labé" style="max-height: 30px;">
                    </div>
                </div>
            </td>
            <!-- Verso -->
            <td class="carte-cell">
                <div class="carte">
                    <div class="header">
                        FACULTÉ : <?= htmlspecialchars($etudiant['Faculte']) ?><br>
                        DÉPARTEMENT : <?= htmlspecialchars($etudiant['Departement']) ?><br>
                        LICENCE : <?= htmlspecialchars($etudiant['Licence']) ?><br>
                        ANNÉE UNIVERSITAIRE : <?= htmlspecialchars($etudiant['AnneeUniv']) ?>
                    </div>
                    <div class="drapeau"></div>
                    <div class="infos">
                        Tuteur : <br>
                        Téléphone :<br><br>
                        Fait le : <?= date("d/m/Y") ?><br><br>
                        Étudiant(e) : _____________ 
                        Chef Service Scolarité <br>  
                        <img src="assets/Logo_univ_labe.png" alt="Logo Université de Labé" style="max-height: 30px;">
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
