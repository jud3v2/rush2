<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // validation de la requête
        $request->validate([
            'files' => 'required',
            'files.*' => 'required|mimes:pdf,csv,txt,avi,jpg,jpeg,png,gif,mp4,avi,mkv',
        ]);

        // Vérifie qu'il y a bien que des fichier dans la requête
        if ($request->hasFile('files')) {

            // parcours chaque fichier dispo dans la requête
            foreach ($request->file('files') as $key => $file) {
                // Récupère le nom original du fichier
                $fileName = $file->getClientOriginalName();
                // enregistre le fichier dans /public/uploads
                $file->storeAs('uploads', $fileName, 'public');
            }

            if(shell_exec("php my_tar.php ./storage/uploads")) {
                // retourne une réponse json avec toute les info
                return response()->json([
                    'message' => 'data created',
                    'remove_all_files' => shell_exec("rm -rf ./storage/uploads/*")
                ], 201);
            } else {
                return reponse()->json([
                    'message' => "Erreur lors de l'archivage de vos fichiers",
                    'remove_all_files' => shell_exec("rm -rf ./storage/uploads/*")
                ], 500);
            }

        }

        return response()->json([
            "message" => "data not created",
        ], 500);
    }

    public function download(): BinaryFileResponse
    {
        return response()->download(public_path('output.mytar'));
    }

    public function test(): JsonResponse
    {
        return response()->json([
            "message" => "ok"
        ]);
    }
}
