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
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        // Créer le dossier s'il n'existe pas
        $userDir = $this->uploadDirectory . '/avatars/' . $user->getId();
        if (!is_dir($userDir)) {
            mkdir($userDir, 0777, true);
        }

        $file->move($userDir, $newFilename);

        // Retourner le chemin relatif
        return '/uploads/avatars/' . $user->getId() . '/' . $newFilename;
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

