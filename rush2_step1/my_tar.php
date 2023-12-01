<?php
function createTar($sourceDir, $outputFile) {
    $tarFile = fopen($outputFile, 'w');

    if (!$tarFile) {
        die("Impossible d'ouvrir le fichier tar pour écriture.");
    }

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceDir), RecursiveIteratorIterator::SELF_FIRST);

    foreach ($files as $file) {
        $file = realpath($file);

        // Ignorer les répertoires
        if (is_dir($file)) {
            continue;
        }

        // Lire le contenu du fichier
        $content = is_file($file) ? file_get_contents($file) : '';

        // Obtenir les informations sur le fichier
        $stat = stat($file);

        // En-tête du fichier dans le format tar
        $headerFormat = "a100" .      // Nom du fichier
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

        $header = pack($headerFormat,
            basename($file),               // Nom du fichier
            is_file($file) ? sprintf("%06o ", fileperms($file)) : '0000000 ', // Permissions
            sprintf("%06o ", $stat['uid']), // UID
            sprintf("%06o ", $stat['gid']), // GID
            sprintf("%011o ", is_file($file) ? $stat['size'] : 0), // Taille du fichier (0 pour les fichiers spéciaux)
            sprintf("%011o ", $stat['mtime']), // Timestamp de modification
            '        ',                     // Checksum (à remplir plus tard)
            getFileType($file),             // Type de fichier
            is_link($file) ? readlink($file) : '', // Linkname
            'ustar',                        // Magic
            '00'                            // Version
        );

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
}

function getFileType($file) {
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

// Récupérer le répertoire source à partir des paramètres du terminal
$sourceDir = realpath($argv[1]);
$outputFile = "output.mytar";

// Ajouter tous les arguments (à l'exception du premier) passés dans le terminal
for ($i = 2; $i < count($argv); $i++) {
    $arg = realpath($argv[$i]);
    if (is_dir($arg)) {
        createTar($arg, $outputFile);
    } elseif (is_file($arg)) {
        // Handle files here
    } else {
        die("Le fichier ou répertoire $arg n'existe pas.\n");
    }
}

if (is_dir($sourceDir)) {
    createTar($sourceDir, $outputFile);
} else {
    die("Le répertoire source n'est pas valide.\n");
}

createTar($sourceDir, $outputFile);