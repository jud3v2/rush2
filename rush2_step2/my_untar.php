<?php

function logMessage($message) {
    echo $message . PHP_EOL;
}

function askUserChoice() {
    fwrite(STDOUT, "Choisissez une option :\n1. Écraser\n2. Ne pas écraser\n3. Écraser pour tous\n4. Ne pas écraser pour tous\n5. Arrêter et quitter\nVotre choix : ");
    return trim(fgets(STDIN));
}

function extractTar($filePath) {
    $overwriteAll = false;
    $skipAll = false;

    if (!file_exists($filePath)) {
        return logMessage("Erreur : Le fichier $filePath n'existe pas.");
    }

    // Créer un répertoire d'extraction basé sur le nom du fichier
    $destination = dirname($filePath) . '/' . basename($filePath, '.mytar');
    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }

    $file = fopen($filePath, 'rb');

    while (!feof($file)) {
        $header = fread($file, 512);
        if (!$header || strlen($header) < 512) {
            break;
        }

        $fileInfo = unpack("a100name/a8mode/a8uid/a8gid/a12size/a12mtime/a2chksum/a1typeflag/a100linkname/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor", $header);

        $name = trim($fileInfo['name']);
        if (!$name) {
            break;
        }

        $fileSize = octdec(trim($fileInfo['size']));
        $typeFlag = $fileInfo['typeflag'];

        if (file_exists($destination . '/' . $name)) {
            if (!$overwriteAll && !$skipAll) {
                logMessage("Conflit avec le fichier/dossier : " . $name);
                $userChoice = askUserChoice();

                switch ($userChoice) {
                    case 1: // Écraser
                        break;
                    case 2: // Ne pas écraser
                        fseek($file, ceil($fileSize / 512) * 512, SEEK_CUR);
                        continue 2;
                    case 3: // Écraser pour tous
                        $overwriteAll = true;
                        break;
                    case 4: // Ne pas écraser pour tous
                        $skipAll = true;
                        fseek($file, ceil($fileSize / 512) * 512, SEEK_CUR);
                        continue 2;
                    case 5: // Arrêter et quitter
                        fclose($file);
                        return logMessage("Décompression annulée par l'utilisateur.");
                    default:
                        fclose($file);
                        return logMessage("Choix non valide. Décompression annulée.");
                }
            } elseif ($skipAll) {
                fseek($file, ceil($fileSize / 512) * 512, SEEK_CUR);
                continue;
            }
        }

        $extractPath = $destination . '/' . $name;

        switch ($typeFlag) {
            case '0': // Fichier normal
            case '':  // Fichier normal (GNU tar)
            if ($fileSize > 0) {
                $fileContent = fread($file, $fileSize);
                file_put_contents($extractPath, $fileContent);
            }
            break;
            case '5': // Répertoire
                if (!is_dir($extractPath)) {
                    mkdir($extractPath, 0777, true);
                }
                break;
            // Ajouter d'autres cas pour différents types de fichiers si nécessaire
        }

        // Padding pour s'assurer que la taille de l'enregistrement est un multiple de 512 octets
        $padding = 512 - ($fileSize % 512);
        if ($padding < 512) {
            fseek($file, $padding, SEEK_CUR);
        }
    }

    fclose($file);
    return logMessage("Décompression réussie dans le dossier : $destination");
}

fwrite(STDOUT, "Entrez le chemin du fichier .mytar à décompresser : ");
$filePath = trim(fgets(STDIN));

extractTar($filePath);
