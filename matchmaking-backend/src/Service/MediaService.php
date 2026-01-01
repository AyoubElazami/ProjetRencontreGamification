<?php
namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class MediaService
{
    public function __construct(
        private string $uploadDirectory,
        private SluggerInterface $slugger
    ) {}

    public function uploadAvatar(UploadedFile $file, User $user): string
    {
        try {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            
            // Obtenir l'extension depuis le nom de fichier original (ne nécessite pas fileinfo)
            $extension = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
            
            // Valider et normaliser l'extension
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($extension, $allowedExtensions)) {
                // Si l'extension n'est pas valide, essayer de la deviner depuis le type MIME (si disponible)
                try {
                    $mimeType = $file->getMimeType();
                    $extensionMap = [
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/gif' => 'gif',
                        'image/webp' => 'webp',
                    ];
                    $extension = $extensionMap[$mimeType] ?? 'jpg';
                } catch (\Exception $e) {
                    // Si getMimeType() échoue (fileinfo non disponible), utiliser jpg par défaut
                    $extension = 'jpg';
                }
            }
            
            // Normaliser jpeg -> jpg
            if ($extension === 'jpeg') {
                $extension = 'jpg';
            }
            
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;

            // Créer le dossier avatars s'il n'existe pas
            $avatarsDir = $this->uploadDirectory . '/avatars';
            if (!is_dir($avatarsDir)) {
                if (!mkdir($avatarsDir, 0777, true)) {
                    throw new \RuntimeException('Impossible de créer le dossier avatars: ' . $avatarsDir);
                }
            }

            // Créer le dossier utilisateur s'il n'existe pas
            $userDir = $avatarsDir . '/' . $user->getId();
            if (!is_dir($userDir)) {
                if (!mkdir($userDir, 0777, true)) {
                    throw new \RuntimeException('Impossible de créer le dossier utilisateur: ' . $userDir);
                }
            }

            // Vérifier que le dossier est accessible en écriture
            if (!is_writable($userDir)) {
                throw new \RuntimeException('Le dossier n\'est pas accessible en écriture: ' . $userDir);
            }

            $file->move($userDir, $newFilename);

            // Vérifier que le fichier a bien été déplacé
            $fullPath = $userDir . '/' . $newFilename;
            if (!file_exists($fullPath)) {
                throw new \RuntimeException('Le fichier n\'a pas pu être sauvegardé: ' . $fullPath);
            }

            // Retourner le chemin relatif
            return '/uploads/avatars/' . $user->getId() . '/' . $newFilename;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de l\'upload de l\'avatar: ' . $e->getMessage(), 0, $e);
        }
    }

    public function deleteAvatar(string $avatarPath): bool
    {
        $fullPath = $this->uploadDirectory . str_replace('/uploads', '', $avatarPath);
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}

