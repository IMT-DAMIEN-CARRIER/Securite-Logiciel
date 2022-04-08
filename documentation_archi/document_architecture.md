# Document d'architecture

[TOC]

## Composants

### Base de données

![](https://i.imgur.com/BjHFWXi.png)



### Client

Côté client, nous allons utiliser une CLI (Command Line Interface) pour communiquer avec le serveur. Les échanges seront réalisés en utilisant TLS pour sécuriser les données en transit.

### Serveur

Côté serveur, nous aurons une API REST sous Symfony qui fera l'interface entre le client et la base de données. 

### Routes

Symfony est un framework basée sur l'utilisation des routes : chaque action possède sa propre route.

#### GET

**Lister les fichiers présents**
La route de l'API sera ***[URL de mon site]/api/files*** pour récupérer la liste complète des fichiers uploadés sur le serveur.

**Afficher le contenu d'un fichier**
La route pour récupérer un fichier spécifique sera ***[URL de mon site]/api/document/{id}*** pour récupérer le fichier correspondant à l'*id* donné en paramètre.

Ces deux routes seront appelées à l'aide d'une requête **HTTP/GET**.

#### POST

La route pour uploader un fichier sera ***[URL de mon site]/api/document***.

Elle est identique à celle du GET mais sera effectuée à l'aide d'une requête **HTTP/POST** et sera accompagneé des paramètres necessaires pour la création d'un object File au format json.

> Alfred : détail du body ?

#### DELETE

La route pour supprimer un fichier spécifique sera ***[URL de mon site]/api/document/{id}*** pour le fichier correspondant à l'*id* donné en paramètre.

Cette route sera appelée à l'aide d'une requête **HTTP/DELETE**.

#### PUT

La route pour modifier un fichier spécifique sera ***[URL de mon site]/api/document/{id}*** pour le fichier correspondant à l'*id* donné en paramètre.

Cette route sera appelée à l'aide d'une requête **HTTP/PUT**.



Nous utiliserons le même principe pour les permissions avec une route de base : **[URL de mon site]/api/permission**.

## Objets

### Authentification

Pour des raisons de simplicité nous utiliserons des identifiants simples pour ce TP. En effet, il serait plus sécurisé de générer des mots de passe aléatoires pour plus de sécurité.

**Administrateur :**
- Login : admin
- Mot de passe : admin

**Alice :**
- Login : alice
- Mot de passe : alice

**Bob :**
- Login : bob
- Mot de passe : bob

**Roger :**
- Login : roger
- Mot de passe : roger

### Accès base de données

La base de données sera une base de données mysql 5.7 avec comme identifiant *root* et mot de passe *root* pour simplifer notre utilisation

## Permissions

Il y aura 4 types de permission : 

- **Créer** : Droit de créer un fichier dans le dossier de partage. Le propriétaire du fichier reste le propriétaire du partage. Le créateur du fichier détient les droits suivants sur le fichier : lister, télécharger, supprimer et écraser.
- **Lister** : Droit de lister le(s) fichier(s).
- **Télécharger** : Droit de télécharger un fichier.
- **Supprimer** : Droit de supprimer un fichier.
- **Écraser** : Droit de modifier un fichier.

## Rôle

- **ADMIN** : Donne le droit de lister le contenu du répertoire de tous les utilisateurs.
- **USER** : Il sera le rôle de base de chaque utilisateur qui sera créé dans la base de données.

## Interfaces

- Une interface de connexion
- Une interface de gestion des droits des utilisateurs
- Une interface de création de fichier (upload) : 
    - Gestion des droits des utilisateurs sur le fichier : de base *aucun droit*.
- Une interface de liste de tous les fichiers
- Une interface d'affichage d'un fichier contenant la possibilité de : 
    - Écraser le fichier
    - Supprimer le fichier
    - Télécharger le fichier
- Une dernière interface permettant de :
    - Ajouter une permission sur un fichier que l'on détient
    - Supprimer une permission précédemment attribué

> Alfred : Il aurait été intéressant de rentrer dans le détail ici : comment ces interfaces sont implémentées ? Qui interagit avec ?

## Sécurité

### Authentification

![](/home/clement/Téléchargements/sequence_authentification.png)

> Alfred : Vous avez oublié d'adjoindre l'image...

### Création de fichier

![](/home/clement/Téléchargements/sequence_creation_fichier.png)

![(https://i.imgur.com/CpV6N7P.png)

### Téléchargement de fichier

![](/home/clement/Téléchargements/sequence_recuperation_fichier.png)

![(https://i.imgur.com/79EhNH5.png)



## Librairies

Nous utiliserons le framework PHP : Symfony pour la création de l'API et des différentes interfaces.

Les différents composants inclus dans Symfony vont nous permettre de gérer la base de données et les différents upload/download.

Le package Symfony API Platform va nous permettre de mettre en place l'API REST qui permettra à l'utilisateur d'intéragir avec le serveur.

Côté serveur, nous hasherons les données avec **bcrypt**. Nous utiliserons **AES 256** pour chiffrer le contenu des fichiers dans la base de données.