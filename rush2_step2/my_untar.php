<?php

function logMessage($message) {
    echo $message . PHP_EOL;
}

function askUserChoice() {
    fwrite(STDOUT, "Choisissez une option :\n1. Écraser\n2. Ne pas écraser\n3. Écraser pour tous\n4. Ne pas écraser pour tous\n5. Arrêter et quitter\nVotre choix : ");
    return trim(fgets(STDIN));
}

function extractMyTar($filePath) {
    $overwriteAll = false;
    $skipAll = false;

    if (!file_exists($filePath)) {
        return logMessage("Erreur : Le fichier $filePath n'existe pas.");
    }

    $fileContent = file_get_contents($filePath);
    $filesData = json_decode($fileContent, true);

    if ($filesData === null) {
        return logMessage("Erreur : Le contenu du fichier $filePath n'est pas un JSON valide.");
    }

    foreach ($filesData as $fileData) {
        $fileName = $fileData['name'];
        $fileRelativePath = $fileData['path'];
        $fileContent = $fileData['content'];

        $fullPath = dirname($filePath) . '/' . $fileRelativePath;

        if (file_exists($fullPath)) {
            if (!$overwriteAll && !$skipAll) {
                logMessage("Conflit avec le fichier/dossier : $fullPath");
                $userChoice = askUserChoice();

                switch ($userChoice) {
                    case 1: // Écraser
                        break;
                    case 2: // Ne pas écraser
                        continue 2;
                    case 3: // Écraser pour tous
                        $overwriteAll = true;
                        break;
                    case 4: // Ne pas écraser pour tous
                        $skipAll = true;
                        continue 2;
                    case 5: // Arrêter et quitter
                        return logMessage("Décompression annulée par l'utilisateur.");
                    default:
                        return logMessage("Choix non valide. Décompression annulée.");
                }
            } elseif ($skipAll) {
                continue;
            }
        }

        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
            logMessage("Répertoire créé : ".basename($directory)."/");
        }

        file_put_contents($fullPath, $fileContent);
        logMessage("Fichier extrait : ".basename($fullPath));
    }

    return logMessage("Extraction terminée avec succès.");
}

fwrite(STDOUT, "Entrez le chemin du fichier .mytar à décompresser : ");
$filePath = trim(fgets(STDIN));

extractMyTar($filePath);