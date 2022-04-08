# Coding rules

[TOC]

## Information sur le projet

- Version de PHP : 7.4
- Version de Symfony : 4.4
- Version de MySQL : 5.7 ou supérieure

## Règle concernant les commits

Le message de commit doit être écrit en français.
Il doit contenir un récapitulatif succint des modifications apportées.

Il aura la forme suivante :
```
Modifications apportées : 
* Modification du README.md
* Ajout d'une méthode de GET pour récupérer les fichiers.
```

## Règle de développement

Nous suivrons les règles [PSR-2 : Coding style Guide](https://www.php-fig.org/psr/psr-2/) et les règles [PS3: Logger Interface](https://www.php-fig.org/psr/psr-3/).

Par exemple :

- Utilisation du typage fort sur les tests
```php
if ($a === $b) {
    bar();
}
```

- Utilisation du typage fort pour la création de variable ou de méthode.
```php
private string $foo;

public function maFonction(string $foo): string
{
    return $foo;
}
```

**Attention** : Le type de retour ne sera pas renseigné pour les methodes ne retournant rien.

- Utilisation de yoda condition pour plus de sécurité dans les comparaisons.
```php
$a = 12;

if(12 === $a) {
    //do something.
} else {
    //do something else.
}
```

De plus comme vu sur l'exemple du dessus un if sera toujours précédé et suivi d'une ligne vide pour plus de lisibilité.

Concernant le nommage des variables et des fonctions nous utiliserons le camel case pour des raisons de lisibilité. Nous éviterons aussi d'ajouter des chiffres et numéros dans les noms de variable ou de fonction.

Par exemple :
```php
private int $maNouvelleVariableDeTypeInt;

public function maNouvelleFonction()
{
    //do something.
}
```

Les instructions ne devront pas dépasser  120 caractères (signalé par une ligne verticale dans PHPStorm), si c'est le cas il faut revenir à la ligne.