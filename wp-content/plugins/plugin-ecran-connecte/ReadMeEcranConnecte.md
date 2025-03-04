# Ecran connecté

Voici un guide expliquant le fonctionnement de l'écran connecté.  

Si vous souhaitez mieux comprendre les fonctions les plus importantes, veuillez lire le ReadMe dédié à ces dernières.  

## Principe

Ce projet permet d'afficher l'emploi du temps de la personne connectée.  
En plus de voir son emploi du temps, l'utilisateur pourra aussi recevoir des informations venant de son département et des alertes le concernant.  

Ce projet a aussi pour but d'être affiché sur des télévisions afin d'afficher l'emploi du temps des différentes promotions.  

Ce projet est composé de deux parties :  
    - Le plugin qui permet d'avoir nos fonctionnalités ;  
    - Le thème pour avoir l'apparence / la structure que l'on désire.  

## Plugin

Il y a plusieurs plugins utilisés pour ce projet, voici une liste décrivant l'utilité de chaque plugin :  
    - Members // Permet de limiter les pages d'un site à certain membre ; 
    - Ecran connecté // Plugin principal du site, nous allons en parler plus en détails en dessous ; 
    - GithubMachin // Permet de faire la synchronisation entre notre plugin et son code sur GitHub ;
    - WPS Hide Login // Change l'URL de connexion ;  
    - WP Crontrol // Permet de faire appel au cron de WordPress.

Nous allons traiter plus en détails le plugin que nous développons, le plugin "Ecran connecté".  

Ce plugin permet plusieurs fonctionnalités:  
    - Création de plusieurs types de compte (Directeur d'études, Secretaire, technicien, Télévision) ; 
    - Affichage de l'emploi du temps de la personne connectée ;
    - Envoie et affichage d'informations ;
    - Envoie et affichage d'alertes.

### Utilisateurs

Il y a sept rôles différents avec chacun leur droit :  

| Utilisateur | Voir son emploi du temps | Poster des informations | Poster des alertes | Inscrire des utilisateurs |
|:-----------:|:------------------------:|:-----------------------:|:------------------:|:-------------------------:|
|  AdminDept  |           Non            |           Oui           |        Oui         |            Oui            |
| Technicien  |           Oui            |           Non           |        Non         |            Non            |
| Télévision  |           Oui            |           Non           |        Non         |            Non            |
|  Tablette   |           Oui            |           Non           |        Non         |            Non            |
| Secretaire  |           Non            |           Oui           |        Oui         |            Oui            |

Dans ce tableau, on peut voir que technicien et télévisions ont les mêmes droits.  
Mais quelle est la différence ? L'affichage de l'emploi du temps.  

Le technicien va avoir un mix de toutes les promotions afin de savoir quelle salle est / sera utilisé.  
Quant à la télévision, la télévision affiche autant d'emploi du temps désiré.  

### Emploi du temps

L'emploi du temps provient de l'ADE.  
Pour le récupérer rendez-vous sur le read me d'installation du projet.  

Il est téléchargé tous les matins via "WP Control" et de la fonction "", en cas de problème de téléchargement, le plugin prend l'emploi du temps téléchargé la veille.  
L'emploi du temps télécharge une période d'une semaine en cas de problème venant de l'ADE permettant de continuer à fonctionner.  
L'affichage de l'emploi du temps est sur la journée pour les étudiants et les techniciens.  
Les enseignants et directeur d'études ont quant à eux accès aux dix prochains cours.  

Les emplois du temps des différentes promotions sont disponibles pour toutes les personnes connectées.  


### Informations

Les informations sont visibles par tous les utilisateurs.
Elles sont affichées dans un diaporama sur le côté de l'écran.

Il y a plusieurs types d'informations possibles à poster (image, texte, PDF, évènement, vidéo, flux RSS).

Les PDF sont affichés grâce à la librairie "PDF.js" qui permet de créer son propre lecteur de PDF. Voir "slideshow.js"

Les vidéos peuvent être de deux formats : short et classique.<br>
Les vidéos shorts sont les vidéos verticales et les vidéos classiques sont les vidéos horizontales.<br>
Les vidéos peuvent être importées via un fichier MP4 ou un lien YouTube.

Les événements sont des informations spéciales. Lorsqu'une information événement est posté, les télévisions n'affichent que les informations en plein écran.  
Ces informations sont donc destinés pour les journées sans cours du style "journée porte ouverte".  


### Alerte

Les alertes sont visibles par les personnes concernées.
Avant de poster une alerte, la personne doit indiquer les personnes concernées. Elle peut envoyer l'alerte à tout le monde ou seulement à un groupe voir plusieurs groupes.

Normalement, les personnes qui se sont abonnées aux alertes du site reçoivent l'alerte en notification.
Les alertes défilent les une après les autres en bas de l'écran dans un bandeau rouge.
Les alertes ne sont que du texte.

### Météo

La météo vient d'une API qui est appelé pour nous donner la météo en fonction de notre position GPS.
Voir "weather.js", searchLocationTV.js et ajax-location-methods.php.

## Thème

Le thème permet de créer la structure du site. Cela nous permet de modeler le site à notre convenance.
Le site est dans les couleurs de l'AMU. Nous avons le site séparé en quatre parties principales :
    - Le Header où se trouve le logo du site et le menu ;
    - Le Main où se trouve l'emploi du temps ;
    - La sidebar avec les informations ;
    - Le footer avec les alertes, la date et la météo.


### Customisation

Ce thème peut être modifiable directement en allant dans la catégorie "Customize" disponible sur la barre WordPress.  
Dans l'onglet "Theme écran connecté", vous pourriez modifier :  
    - L'affichage des informations (positionner les infos à droite, à gauche ou ne pas les afficher) ;
    - L'affichage des alertes (activer/désactiver les alertes) ;
    - L'affichage de la météo (activer/désactiver, positionner à gauche ou à droite) ; 
    - L'affichage de l'emploi du temps (Défiler les emplois du temps un par un ou en continu). 

Vous pouvez aussi modifier les couleurs du site, changer le logo du site.  
