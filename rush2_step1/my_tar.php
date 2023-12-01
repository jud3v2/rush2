<?php

#[AllowDynamicProperties] class my_tar {

    /**
     * @param $argv
     */
    public function __construct($argv)
    {
        $this->arguments = $argv;
        $this->tree = [];
    }

    /**
     * @return void
     */
    public function start(): void
    {
        foreach ($this->getArguments() as $file) {
            if(is_dir($file)) {
                $this->scan_my_dir_rec($file);
            } elseif(is_file($file)) {
                if($file != 'my_tar.php') { // supprime le nom du script car on ne veux pas l'inclure dans notre tree
                    $this->setTree(realpath($file));
                }
            }
        }
    }

    /**
     * @return void
     */
    public function makeTarball(): void
    {
        $tarFile = fopen("output.mytar", 'w');

        if (!$tarFile) {
            die("Impossible d'ouvrir le fichier tar pour écriture.");
        }

        $files = $this->getTree();

        unset($files[0]); // remove my_tar.php

        foreach ($files as $file) {
            $file = realpath($file);

            // Ignorer les répertoires
            if (is_dir($file)) {
                continue;
            }

            // Lire le contenu du fichier
            $content = is_file($file) ? file_get_contents($file) : '';
            $header = $this->makeHeader($file);

            // Calculer le checksum
            $checksum = array_sum(unpack('C*', $header));

            // Ajouter le contenu du fichier après l'en-tête
            $header .= pack("a8", sprintf("%06o", $checksum));

            // Écrire l'en-tête et le contenu dans le fichier tar
            fwrite($tarFile, $header . $content);

            // Padding pour s'assurer que la taille de l'enregistrement est un multiple de 512 octets
            $padding = 512 - (strlen($header . $content) % 512);
            if ($padding < 512) {
                fwrite($tarFile, str_repeat("\0", $padding));
            }
        }

        fclose($tarFile);
        echo "tarball created";
    }

    /**
     * @param $file
     * @return string
     */
    private function getFileType($file): string
    {
        if (is_link($file)) {
            return '2'; // lien symbolique
        } elseif (is_dir($file)) {
            return '5'; // répertoire
        } elseif (is_file($file)) {
            return '0'; // fichier ordinaire
        } else {
            return '0'; // type par défaut pour les cas non gérés
        }
    }

    private function headerFormat(): string
    {
        // En-tête du fichier dans le format tar
        return "a100" .      // Nom du fichier
            "a8" .        // Permissions
            "a8" .        // UID
            "a8" .        // GID
            "a12" .       // Taille du fichier
            "a12" .       // Timestamp de modification
            "a8" .        // Checksum (à remplir plus tard)
            "a1" .        // Type de fichier
            "a100" .      // Linkname
            "a6" .        // Magic
            "a2";         // Version
    }

    private function makeHeader($file) {
        // Obtenir les informations sur le fichier
        $stat = stat($file);

        return pack($this->headerFormat(),
            basename($file),               // Nom du fichier
            is_file($file) ? sprintf("%06o ", fileperms($file)) : '0000000 ', // Permissions
            sprintf("%06o ", $stat['uid']), // UID
            sprintf("%06o ", $stat['gid']), // GID
            sprintf("%011o ", is_file($file) ? $stat['size'] : 0), // Taille du fichier (0 pour les fichiers spéciaux)
            sprintf("%011o ", $stat['mtime']), // Timestamp de modification
            '        ',                     // Checksum (à remplir plus tard)
            $this->getFileType($file),             // Type de fichier
            is_link($file) ? readlink($file) : '', // Linkname
            'ustar',                        // Magic
            '00'                            // Version
        );
    }

    /**
     * @param $dir
     * @return void
     */
    private function scan_my_dir_rec($dir): void {
        $scanned_directories = scandir(realpath('./' . $dir));
        $this->remove_point_and_double($scanned_directories);

        foreach ($scanned_directories as $file) {
            $fullPath = realpath('./' . $dir . '/' . $file);

            if (is_dir($fullPath)) {
                echo "scanning directory: " . $fullPath . PHP_EOL;
                $this->scan_my_dir_rec($dir . '/' . $file);
            } elseif (is_file($fullPath)) {
                if(!str_contains($fullPath, 'my_tar.php')) {
                    $this->setTree($fullPath);
                    echo "adding file to tree: " . $fullPath . PHP_EOL;
                }
            }
        }
    }


    /**
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
     * @return array
     */
    private function getTree(): array
    {
        return $this->tree;
    }

    /**
     * @param array|string $tree
     */
    private function setTree(array|string $tree): void
    {
        $this->tree[] = $tree;
    }
}

$foo = new my_tar($argv);
$foo->start();
$foo->makeTarball();