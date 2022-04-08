# Document d'installation et d'utilisation

[TOC]

## Installation du client

### Installation de PHP 7.4

Afin de faire fonctionner le client, il est nécessaire d'installation **php 7.4** (une version de php 7.0 devrait suffir
mais par sécurité nous vous conseillons d'utiliser la version de PHP avec laquelle nous avons développé le projet).

Pour cela, vous pouvez taper ces commandes dans une console sous Linux :

```bash
sudo apt-get update
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt install php7.4
php -v # Affiche bien la version 7.4
```

### Récupération du Client

Pour récuperer le client, vous pouvez cloner notre projet **GitLab** :

```bash
git clone https://gitlab.mines-ales.fr/Toxiqh/securite_logiciel.git
```

Vous retrouverez dans ce projet 3 répertoires différents :

- **server :** Code du serveur Symfony
- **documentation_archi :** Contient les documents d'architectures
- **client :** Contient le code du client. On y retrouve notamment le fichier **client.php** qui est le launcher de
  notre **CLI**.

## Utilisation du serveur

Vous n'avez rien à faire on déjà mis en production le serveur. Vous pourrez le retrouver
via [ce lien](https://securite-app.damscrr.fr).

Vous pourrez vous "connecter" au site via les utilisateurs du projet mais vous n'aurez accès a rien.

Nous avons pré générer des données aléatoire à l'aide d'un outil de Symfony afin de remplir de qq fichier la base de
données. Ces fichiers sont donc fictifs, malgré les différentes extensions de fichier que vous verrez, ils ne
contiennent que du texte aléatoire généré à l'aide de lorem ipsum.

## Utiliser la CLI

Afin d'utiliser la CLI, il suffit d'exécuter les commandes suivante et de suivres les instructions :

```bash
cd client/
php client.php
```

La base de données comporte 4 utilisateurs :

* Utilisateur Alice :
    * login : alice
    * Mot de passe : alice
* Utilisateur Bob :
    * login : bob
    * Mot de passe : bob
* Utilisateur Roger :
    * login : roger
    * Mot de passe : roger
* Utilisateur Admin :
    * login : admin
    * Mot de passe : admin

*A noter que les mots de passe ne sont pas des mots de passe "compliqué" afin de simplifier les tests pour ce TP. De
toute évidence dans la réalité nous aurions généré des mot de passe aléatoire à l'aide d'un outils prévu à cet effet
pour augmenter la sécurité.*
