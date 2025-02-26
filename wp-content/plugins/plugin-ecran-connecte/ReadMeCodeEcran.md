# Ecran connecté code

Ce guide va expliquer les parties les plus importantes du plugin et du thème "Theme écran connecté".

## Plugins

Un plugin se créé avec un fichier PHP contenant :  
    - Un dossier "src" contenant le MVC du projet ;  
    - Un dossier "blocks" avec tous les blocks, les blocks permettent de placer notre code dans une page WordPress ;  
    - Un dossier "public" contenant tout le contenu multimédia (CSS / JS / Img / Fichier) ; 
    - Un dossier "vendor" du code qu'on utilise mais qui ne nous appartient pas (Contient composer, phpoffice (lecture de fichier excel), R34ICS (permet de lire les fichiers ICS / l'emploi du temps)) ; 
    - Un dossier "widgets" contient les widgets générés pour WordPress, devenue obsolète vue notre utilisation actuelle. Il pourrait être utile dans le futur.  

Toutes les fonctionnalités sont générées via le dossier "src".  

### Utilisateurs

Il y a cinq classes pour les utilisateurs :  

User qui est la classe principale puis les classes qui héritent de cette dernière (Television, Secretary, Technician, AdminDept, Tablet).  

Ils sont tous liés à la même entité (model) : User  

### Emploi du temps

Les emplois du temps sont téléchargés en format ICS.  

Les classes utilisées sont : R34ICS et UserController  

Lorsqu'un utilisateur est connecté, il appelle R34ICS pour pouvoir afficher son emploi du temps, R34ICS permet de lire les fichiers ICS.  

### Informations

Les classes utilisées sont : InformationController, Information & InformationView.  
Les librairies "PhpOffice" et "PDF.js" sont aussi utilisées.  
Fichier javascript : slideshow.js  

Les informations sont affichées comme dans un diaporama les une après les autres.  

### Alertes

Les classes utilisées sont : AlertController, Alert & AlertView.  
La librairie "JQuery Ticker" est aussi utilisée.  
Fichier javascript : alertTicker.js 
