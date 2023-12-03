<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

#[AllowDynamicProperties] class my_tar {

    /**
     * @description récupère les arguments passé en paramètre.
     * @param $argv
     */
    public function __construct($argv)
    {
        $this->arguments = $argv;
        $this->tree = [];
        $this->authorizedFiles = ['txt', 'csv', 'pdf', 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mkv'];
        $this->message = [
            "success" => 0,
            "error" => -1,
        ];
    }

    /**
     * @description point d'entré pour découvrir tous les fichiers et sous dossier envoyé en arguments.
     * @return void
     */
    public function start(): void
    {
        if(count($this->getArguments()) <= 1) {
            $this->showHelp();
            exit($this->message['error']);
        }
        // parcours tous les arguments en tant que fichiers.
        foreach ($this->getArguments() as $file) {
            if($file == 'my_tar.php') { // skip my_tar.php
                continue;
            }

            echo "Processing file: " . $file . PHP_EOL;
            if(is_dir($file)) {
                // si c'est un dossier on l'envoie dans une autre fonction qui vas gerer les dossiers et les sous-dossiers
                $this->scan_my_dir_rec($file);
            } elseif($this->my_own_check_file($file)){
                // sinon si c'est un fichier on l'ajoute dans l'arborescence
                $this->setTree($file);
            }
        }
    }

    /**
     * @description fonction qui nous permet de créé notre tarball personnaliser
     * @return void
     */
    public function makeTarball(): void
    {
        // récupère tous les chemins d'accès au fichier découvert précedemment.
        $files = $this->getTree();

        // création d'un array pour conversion en json.
        $array_files = [];

        // création de la tarball avec l'option w si il existe déjà on réduit la taille du fichier à 0
        $tarFile = fopen("output.mytar", 'w');

        // si l'opération échoue on retounrne une erreur
        if (!$tarFile) {
            echo "Impossible d'ouvrir le fichier tar pour écriture.";
            exit($this->message["error"]);
        }

        // parcours chaque fichier afin de lire son contenue et l'insérer dans le tableau.
        foreach ($files as $file) {
            // ajout dans l'array $array_files, opération similaire à un array_push(...)
            $array_files[] = [
                "name" => basename($file), // nom du fichier
                "path" => str_replace(getcwd(), './', $file), // récupération du chemin relatif
                "content" => $this->getFileContent($file) // contenu du fichier
            ];

        }

        // écriture des donné dans la tarball et encodage en json pour pouvoir les décoder plus tard.
        fwrite($tarFile, json_encode($array_files));

        // fermeture du fichier sinon problème !!
        fclose($tarFile);

        // Retour utilisateur afin de l'informer que tous c'est bien passé
        echo "tarball created";
        exit($this->message['success']);
    }

    /**
     * @description Obtient le contenu du fichier en fonction de son type.
     * @param string $file
     * @return string
     */
    private function getFileContent(string $file): string
    {
        $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        // Ajouter ici des conditions pour traiter différents types de fichiers
        if (in_array($fileExtension, ['txt', 'csv', 'pdf'])) {
            // Utiliser file_get_contents pour les types de fichiers texte et PDF
            return file_get_contents($file);
        } elseif (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
            // Utiliser une méthode spécifique pour les images
            return base64_encode(file_get_contents($file)); // Vous pouvez également utiliser des bibliothèques pour convertir l'image en base64
        } elseif (in_array($fileExtension, ['mp4', 'avi', 'mkv'])) {
            // Utiliser une méthode spécifique pour les vidéos
            return base64_encode(file_get_contents($file)); // Vous pouvez également utiliser des bibliothèques pour convertir la vidéo en base64
        } else {
            // Ajouter d'autres conditions pour d'autres types de fichiers si nécessaire
            return ''; // Retourner une chaîne vide pour les types de fichiers non pris en charge
        }
    }

    /**
     * @description Fonction récursive qui permet de découvrir les fichiers et sous dossiers.
     * @param $dir
     * @return void
     */
    private function scan_my_dir_rec($dir): void {
        $scanned_directories = scandir($dir);
        $this->remove_point_and_double($scanned_directories);

        foreach ($scanned_directories as $file) {
            $fullPath = $dir . '/' . $file;

            if (is_dir($fullPath)) {
                $this->scan_my_dir_rec($fullPath);
            } elseif ($this->my_own_check_file($fullPath)) {
                $this->setTree($fullPath);
            }
        }
    }

    private function my_own_check_file(string $file): bool
    {
        $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return in_array($fileExtension, $this->authorizedFiles);
    }


    /**
     * @description permet de retirer par références à un tableau le . et le .. qui correspond au dossier courant et précédent.
     * @param array $directories
     * @return void
     */
    private function remove_point_and_double(array &$directories): void
    {
        foreach ($directories as $k => $v) {
            if($v == '.' || $v == "..") {
                unset($directories[$k]);
            }
        }
    }

    /**
     * @description Récupère les argument donné en paramètre de $argv
     * @return string[]
     */
    private function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @description retourne l'arborescence des fichiers, dossier et sous-dossiers décourvert
     * @return array
     */
    private function getTree(): array
    {
        return $this->tree;
    }

    /**
     * @description permet d'assigner un nouveau champs de fichier dans l'arborescence.
     * @param array|string $tree
     */
    private function setTree(array|string $tree): void
    {
        $this->tree[] = $tree;
    }

    private function showHelp(): void
    {
        $str = <<<EOF
BONJOUR ET BIENVENUE DANS VOTRE OUTILS DE COMPRESSION

Pour une utilisation optimal du script merci de l'utiliser comme suit:

php my_tar.php [dossier1|fichier1] [dossier2|fichier2]

Ce script vous permettras d'optenir une tarball.
EOF;
        echo $str;
    }
}

// Création d'un objet php avec la class my_tar
$foo = new my_tar($argv);

// commence à découvrir les fichiers et sous-dossier
$foo->start();

// Fabrique la tarball.
$foo->makeTarball();