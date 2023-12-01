# Rush #2 my_tar

## Description du rush
> COMPÉTENCES à ACQUÉRIR
- PHP
- Bash
- Systèmes de compression

Bienvenue dans ce second Rush de piscine ! :)
Ce rush va vous apprendre comment sont générées les archives “zip”, “tar”, “tgz”, “7z”, etc.

## Rush Effectuer par Enzo, Med et Judikaël.
### Étape 1 (Création d'une tarball)
Rendu : rush2_step1/

Écrire un programme exécutable my_tar.php qui prend en ligne de commande, un ou plusieurs arguments,
qui sont des noms de fichiers ou dossiers à archiver. Le programme génère en sortie une tarball nommée
output.mytar (et l’écrase si elle existe déjà).

```shell
∼/W-WEB-024> ls
file1 file2 file3 folder1 my_tar.php
∼/W-WEB-024> php my_tar.php file1 file2 folder1
∼/W-WEB-024> ls
file1 file2 folder1 my_tar.php output.mytar
```

Il faudra bien entendu gérer le maximum de formats de fichiers (txt, avi, png, mp3, etc.).

> Vous devez gérer la récursivité. Il n’est pas impossible qu’un dossier contienne lui-même
d’autres dossiers et ainsi de suite. La commande tree peut rapidement vous apporter un
aperçu.

> Il est strictement interdit de reprendre une fonction qui génère une archive. Tout l’intérêt
de ce sujet étant de recréer soit même les fonctionnalités. Si vous avez un doute sur
l’autorisation ou l’interdiction des fonctions que vous utilisez, demandez aux assistants.

### Étape 2 (Création du untar)
Écrire un programme exécutable my_untar.php qui prend en ligne de commande, un ou plusieurs arguments,
qui sont des tarball dont il faut extraire les données. Le programme génère en sortie un ou plusieurs fichiers
ou dossiers. En cas de conflit, lorsqu’un fichier ou dossier existe déjà, un prompt doit proposer plusieurs
choix :
1. Écraser
2. Ne pas écraser
3. Écraser pour tous (ne plus redemander)
4. Ne pas écraser pour tous (ne plus redemander)
5. Arrêter et quitter

```shell
∼/W-WEB-024> ls
my_untar.php output.mytar
∼/W-WEB-024> php my_untar.php output.mytar
∼/W-WEB-024> ls
file1 fil2 folder1 my_tar.php output.mytar
```

### Étape 3 (INTERFACE WEB)

Rendu : rush2_step3/
Dans cette partie, vous réaliserez une interface web qui aura pour but de créer une archive depuis votre
navigateur.

Cette interface web doit contenir :
- Un champ pour définir le nom de l’archive.
- Une fonction pour ajouter des fichiers (drag and drop, formulaire, etc.).
- Une liste de tous les fichiers ajoutés.
- Un bouton pour générer l’archive.
- Un bouton pour télécharger l’archive.

> Inspirez-vous de l’interface ci-dessous. Vous êtes libre d’ajouter des éléments de style
ou des animations.

> Nous vous conseillons fortement de passer par un serveur apache avec PHP pour utiliser
votre algorithme de compression.

### Bonus

rush2_bonus

Meilleur sera le taux de compression de votre archive, plus votre Rush sera réussi ! Vous devez à présent
commencer à réfléchir aux différentes façons d’optimiser la taille de votre archive et la vitesse de compression.

Pour chaque amélioration apportée à ce processus et présentée en soutenance, des points bonus vous
seront accordés.

> Définition de la “compression” : https://fr.wikipedia.org/wiki/Compression_de_donn%C3%A9es
Comparaison des compressions : http://rlwpx.free.fr/WPFF/comploc.htm