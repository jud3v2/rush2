#!/usr/bin/env php
<?php

if ($argc < 2) {
    fwrite(STDERR, "Usage: my_untar.php [archivefile1] ...\n");
    exit(1);
}

$overwrite_all = false;
$skip_all = false;

function sanitizeFileName($name) {
    echo "Nettoyage du nom de fichier: $name\n";
    return preg_replace('/[^\x20-\x7e]/', '', $name);
}

function extractArchive($archivePath, &$overwrite_all, &$skip_all) {
    echo "Ouverture de l'archive: $archivePath\n";
    $handle = fopen($archivePath, "rb");
    if (!$handle) {
        fwrite(STDERR, "Impossible d'ouvrir le fichier: $archivePath\n");
        return;
    }

    while (!feof($handle)) {
        $header = fread($handle, 512);
        if ($header === false || strlen($header) === 0 || trim($header) === str_repeat("\0", 512)) {
            echo "Fin de l'archive ou en-tête vide détecté.\n";
            break;
        }

        $fileData = unpack("a100name/a8mode/a8uid/a8gid/a12size/a12mtime/a8chksum/a1typeflag/a100linkname/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor", $header);
        if (!$fileData) {
            fwrite(STDERR, "En-tête illisible ou fichier corrompu.\n");
            break;
        }

        $fileName = sanitizeFileName(trim($fileData['name']));
        $fileSize = octdec(trim($fileData['size']));
        $fileType = $fileData['typeflag'];

        echo "Traitement du fichier: $fileName (taille: $fileSize, type: $fileType)\n";

        if ($fileType == '5') {
            if (!is_dir($fileName) && $fileName !== '') {
                mkdir($fileName, 0777, true);
                echo "Dossier créé: $fileName\n";
            }
        } elseif ($fileType == '0') {
            if (file_exists($fileName)) {
                if ($skip_all) {
                    echo "Le fichier $fileName existe déjà, option 'Ne pas écraser pour tous' activée, fichier ignoré.\n";
                    continue;
                } else if (!$overwrite_all) {
                    echo "Le fichier $fileName existe déjà. Choisissez une option:\n";
                    echo "1. Écraser\n2. Ne pas écraser\n3. Écraser pour tous\n4. Ne pas écraser pour tous\n5. Arrêter et quitter\n";
                    $choice = trim(fgets(STDIN));

                    switch ($choice) {
                        case 1: // Écraser
                            break;
                        case 2: // Ne pas écraser
                            continue 2;
                        case 3: // Écraser pour tous
                            $overwrite_all = true;
                            break;
                        case 4: // Ne pas écraser pour tous
                            $skip_all = true;
                            continue 2;
                        case 5: // Arrêter et quitter
                            fclose($handle);
                            exit;
                    }
                }
            }

            if (!$skip_all) {
                $content = ($fileSize > 0) ? fread($handle, $fileSize) : '';
                file_put_contents($fileName, $content);
                echo "Fichier extrait: $fileName\n";
            }
            fseek($handle, (512 - ($fileSize % 512)) % 512, SEEK_CUR);
        }
    }

    fclose($handle);
    echo "Extraction terminée pour: $archivePath\n";
}

foreach (array_slice($argv, 1) as $archivePath) {
    if (file_exists($archivePath) && is_readable($archivePath)) {
        extractArchive($archivePath, $overwrite_all, $skip_all);
    } else {
        fwrite(STDERR, "Le fichier '$archivePath' n'existe pas ou ne peut pas être lu.\n");
    }
}

?>
