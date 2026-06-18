-- BASE DE DONNEES : etablissement
CREATE DATABASE IF NOT EXISTS decana;
USE decana;

-- TABLES DE BASE
CREATE TABLE signataires (
    MatSigna INT PRIMARY KEY,
    NomSigna VARCHAR(100),
    PrenomSigna VARCHAR(100)
);

CREATE TABLE cohortes (
    CodeCoh VARCHAR(10) PRIMARY KEY,
    Cohorte VARCHAR(100)
);

CREATE TABLE professeurs (
    MatProf INT PRIMARY KEY,
    NomProf VARCHAR(100),
    PrenomProf VARCHAR(100),
    TelProf VARCHAR(20),
    MailProf VARCHAR(100)
);

CREATE TABLE facultes (
    CodeFac VARCHAR(10) PRIMARY KEY,
    NomFac VARCHAR(100)
);

CREATE TABLE departements (
    CodeDep VARCHAR(10) PRIMARY KEY,
    CodeFac VARCHAR(10),
    NomDep VARCHAR(100),
    FOREIGN KEY (CodeFac) REFERENCES facultes(CodeFac)
);

CREATE TABLE programmes (
    CodeProg VARCHAR(10) PRIMARY KEY,
    CodeDep VARCHAR(10),
    NomProg VARCHAR(100),
    FOREIGN KEY (CodeDep) REFERENCES departements(CodeDep)
);

CREATE TABLE concentrations (
    CodeConc VARCHAR(10) PRIMARY KEY,
    CodeProg VARCHAR(10),
    NomCons VARCHAR(100),
    FOREIGN KEY (CodeProg) REFERENCES programmes(CodeProg)
);

CREATE TABLE annees_univ (
    id INT AUTO_INCREMENT PRIMARY KEY,
    annee VARCHAR(20) UNIQUE
);

CREATE TABLE niveaux (
    CodeNiv VARCHAR(10) PRIMARY KEY,
    Niveau VARCHAR(50),
    AnneeUniv VARCHAR(20),
    FOREIGN KEY (AnneeUniv) REFERENCES annees_univ(annee)
);

CREATE TABLE semestres (
    CodeSemes VARCHAR(10) PRIMARY KEY,
    CodeNiv VARCHAR(10),
    NivauSemes VARCHAR(50),
    FOREIGN KEY (CodeNiv) REFERENCES niveaux(CodeNiv)
);

CREATE TABLE matieres (
    CodeMat VARCHAR(10) PRIMARY KEY,
    CodeSemes VARCHAR(10),
    NomMat VARCHAR(100),
    CrediMat INT,
    Statut VARCHAR(50),
    FOREIGN KEY (CodeSemes) REFERENCES semestres(CodeSemes)
);

CREATE TABLE etudiants (
    MatEtu VARCHAR(15) PRIMARY KEY,
    INE VARCHAR(15),
    NomEtu VARCHAR(100),
    PrenomEtu VARCHAR(100),
    DateNais DATE,
    LieuNais VARCHAR(100),
    Sexe ENUM('M', 'F'),
    PVBAC VARCHAR(20),
    OptionBAC VARCHAR(50),
    SessionBAC VARCHAR(20),
    NomPere VARCHAR(100),
    NomMere VARCHAR(100),
    Nationalite VARCHAR(50),
    CentreExamen VARCHAR(100),
    Lycee VARCHAR(100),
    Moyenne DECIMAL(5,2),
    Prefecture VARCHAR(100),
    SessionOrient VARCHAR(50),
    TelephoneEtu VARCHAR(20),
    Mail VARCHAR(100),
    SituaMatriEtu VARCHAR(50),
    CodeCoh VARCHAR(10),
    FOREIGN KEY (CodeCoh) REFERENCES cohortes(CodeCoh)
);

CREATE TABLE inscriptions (
    MatEtu VARCHAR(15),
    MatSigna INT,
    CodeConc VARCHAR(10),
    CodeNiv VARCHAR(10),
    DateIns DATE,
    PRIMARY KEY (MatEtu, CodeConc, CodeNiv),
    FOREIGN KEY (MatEtu) REFERENCES etudiants(MatEtu),
    FOREIGN KEY (MatSigna) REFERENCES signataires(MatSigna),
    FOREIGN KEY (CodeConc) REFERENCES concentrations(CodeConc),
    FOREIGN KEY (CodeNiv) REFERENCES niveaux(CodeNiv)
);

CREATE TABLE notes (
    MatEtu VARCHAR(15),
    MatSigna INT,
    CodeSemes VARCHAR(10),
    CodeMat VARCHAR(10),
    NoteIndiv DECIMAL(5,2),
    NoteGroup DECIMAL(5,2),
    NoteExa DECIMAL(5,2),
    DateNote DATE,
    PRIMARY KEY (MatEtu, CodeSemes, CodeMat),
    FOREIGN KEY (MatEtu) REFERENCES etudiants(MatEtu),
    FOREIGN KEY (MatSigna) REFERENCES signataires(MatSigna),
    FOREIGN KEY (CodeSemes) REFERENCES semestres(CodeSemes),
    FOREIGN KEY (CodeMat) REFERENCES matieres(CodeMat)
);

-- UTILISATEURS & COMPTES
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    mot_de_passe VARCHAR(255),
    role ENUM('admin', 'scolarite') DEFAULT 'scolarite',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- COMPTES ETUDIANTS
CREATE TABLE comptes_etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    MatEtu VARCHAR(15),
    email VARCHAR(100) UNIQUE,
    mot_de_passe VARCHAR(255),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MatEtu) REFERENCES etudiants(MatEtu)
);
ALTER TABLE etudiants ADD photo VARCHAR(255) DEFAULT NULL;

-- DOCUMENTS, MESSAGERIE, JOURNAL, IMPORTS
CREATE TABLE documents_imprimes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    MatEtu VARCHAR(15),
    type_document ENUM('attestation_inscription','releve_notes','fiche_resultats','fin_cycle','carte_etudiant'),
    date_generation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    utilisateur_id INT,
    FOREIGN KEY (MatEtu) REFERENCES etudiants(MatEtu),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE historiques_imports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    fichier_nom VARCHAR(255),
    date_import TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    type_import ENUM('notes', 'etudiants', 'inscriptions'),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    MatEtu VARCHAR(15),
    message TEXT,
    date_notif TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lu BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (MatEtu) REFERENCES etudiants(MatEtu)
);

CREATE TABLE paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    MatEtu VARCHAR(15),
    montant DECIMAL(10,2),
    motif VARCHAR(100),
    date_paiement DATE,
    recu VARCHAR(100),
    FOREIGN KEY (MatEtu) REFERENCES etudiants(MatEtu)
);

CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('admin', 'scolarite'),
    action VARCHAR(50),
    autorise BOOLEAN DEFAULT TRUE
);

CREATE TABLE demandes_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    MatEtu VARCHAR(15),
    type_document ENUM('attestation_inscription', 'releve_notes', 'fiche_resultats', 'fin_cycle', 'carte_etudiant'),
    statut ENUM('en_attente', 'valide', 'refuse') DEFAULT 'en_attente',
    date_demande DATE,
    FOREIGN KEY (MatEtu) REFERENCES etudiants(MatEtu)
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expediteur_id INT,
    MatEtu VARCHAR(15),
    sujet VARCHAR(100),
    message TEXT,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lu BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (MatEtu) REFERENCES etudiants(MatEtu),
    FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE fichiers_etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    MatEtu VARCHAR(15),
    nom_fichier VARCHAR(255),
    type_fichier VARCHAR(50),
    chemin VARCHAR(255),
    date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MatEtu) REFERENCES etudiants(MatEtu)
);

CREATE TABLE journal_actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    action VARCHAR(100),
    description TEXT,
    date_action TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);