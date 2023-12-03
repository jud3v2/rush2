<?php

// Affiche un message dans la console
function logMessage($message) {
    echo $message . PHP_EOL;
}

// Affiche un menu de choix pour l'utilisateur et récupère la sélection
function askUserChoice() {
    fwrite(STDOUT, "Choisissez une option :\n1. Écraser\n2. Ne pas écraser\n3. Écraser pour tous\n4. Ne pas écraser pour tous\n5. Arrêter et quitter\nVotre choix : ");
    return trim(fgets(STDIN));
}

// Vérifie si une chaîne est encodée en base64
function isBase64Encoded($data) {
    if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
        return base64_decode($data, true) !== false;
    }
    return false;
}

// Extrait les fichiers d'une archive .mytar
function extractMyTar($filePath) {
    $overwriteAll = false;
    $skipAll = false;

    // Vérifier si le fichier .mytar existe
    if (!file_exists($filePath)) {
        return logMessage("Erreur : Le fichier $filePath n'existe pas.");
    }

    // Lire le contenu du fichier et le décoder du JSON
    $fileContent = file_get_contents($filePath);
    $filesData = json_decode($fileContent, true);

    // Vérifier si le contenu JSON est valide
    if ($filesData === null) {
        return logMessage("Erreur : Le contenu du fichier $filePath n'est pas un JSON valide.");
    }

    // Traitement de chaque fichier dans les données JSON
    foreach ($filesData as $fileData) {
        $fileRelativePath = $fileData['path'];
        $fileContent = $fileData['content'];

        // Construction du chemin complet du fichier
        $fullPath = dirname($filePath) . '/' . $fileRelativePath;

        // Gestion des conflits de fichiers
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

        // Création des répertoires si nécessaire
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
            logMessage("Répertoire créé : $directory");
        }

        // Décodage du contenu si nécessaire et écriture dans le fichier
        if (isBase64Encoded($fileContent)) {
            $fileContent = base64_decode($fileContent);
        }

        file_put_contents($fullPath, $fileContent);
        logMessage("Fichier extrait : $fullPath");
    }

    return logMessage("Extraction terminée avec succès.");
}

// Récupération du chemin du fichier depuis la ligne de commande ou entrée utilisateur
$filePath = $argc > 1 ? $argv[1] : null;

if (!$filePath) {
    fwrite(STDOUT, "Entrez le chemin du fichier .mytar à décompresser : ");
    $filePath = trim(fgets(STDIN));
}

// Appel de la fonction d'extraction avec le chemin du fichier
extractMyTar($filePath);