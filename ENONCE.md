# Contexte et objectifs pédagogiques
## Contexte
L’association "Littoral Propre" collecte des déchets sur les plages et souhaite mieux organiser ses actions grâce à un back-office permettant de :

- Gérer les bénévoles
- Enregistrer les collectes
- Suivre les types et quantités de déchets récupérés
- Générer des statistiques sur l'impact des actions menées
- Objectifs pédagogiques
- Comprendre les bases du développement back-end en PHP 8.3
- Manipuler une base de données MySQL via MAMP / XAMP / LAMP
- Mettre en place un CRUD (Create, Read, Update, Delete)
- Utiliser PHP PDO pour les interactions avec la base de données
- Sécuriser les accès avec un système d'authentification simple (email / mot de passe)

## Objectifs pédagogiques  
- Comprendre les bases du développement back-end en PHP 8.3
- Manipuler une base de données MySQL via MAMP / XAMP / LAMP
- Mettre en place un CRUD (Create, Read, Update, Delete)
- Utiliser PHP PDO pour les interactions avec la base de données
- Sécuriser les accès avec un système d'authentification simple (email / mot de passe)

# Cahier des charges et stack technique
## Cahier des charges
Nous allons prioriser le développement des 3 premiers points pour le MVP afin d’avoir une application fonctionnelle : gestion des bénévoles, gestion des collectes de déchet et gestion des déchets.

Les étapes 4 et 5 seront à faire pour aller plus loin et le reste pour aller encore plus loin !

1. Gestion des bénévoles  
📌 Fonctionnalités :

Ajouter / modifier / supprimer un bénévole
Lister tous les bénévoles  
Attribuer un rôle (ex : admin, participant)  

2. Gestion des collectes de déchets  
📌 Fonctionnalités :

Enregistrer une collecte (date, lieu, bénévole responsable)  
Associer plusieurs types de déchets et leurs quantités

3. Gestion des déchets collectés  
📌 Fonctionnalités :

Enregistrer les types et quantités de déchets collectés pour chaque collecte

4. Accessibilité de l’application et éco-conception  
📌 Fonctionnalités :

Dans chaque page web, le contraste entre la couleur du texte et la couleur de son arrière-plan doit être suffisamment élevé  
Chaque liste de lien doit être correctement structuré   
Chaque lien doit être explicite  
Identifier et réparer les problèmes d’éco-conception  

5. Système d’authentification  
📌 Fonctionnalités :

Page de connexion sécurisée
Gestion des sessions  
Accès restreint au back-office pour les bénévoles authentifiés

6. Tableau de bord et statistiques  
📌 Fonctionnalités :

Voir le total de déchets collectés  
Filtrer par type de déchet et période  
Graphiques simples avec PHP et une librairie JS (ex : Chart.js)  

## Stack technique
PHP 8.3 (programmation procédurale pour débuter, puis introduction à la programmation orientée objet)  
MySQL (gestion de la base de données)  
MAMP WAMP ou LAMP (serveur local)  
PHP PDO (pour les requêtes SQL sécurisées)  
HTML / CSS (interface utilisateur simple)  
