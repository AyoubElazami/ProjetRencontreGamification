<?php
namespace App\Controller\User;

use App\Entity\ProfileTag;
use App\Entity\User;
use App\Service\MediaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private MediaService $mediaService
    ) {}

    #[Route('/api/user/welcome', name: 'api_user_welcome', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function welcome(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $username = $user->getUsername() ?? $user->getEmail();

        return $this->json([
            'message' => "Bienvenue connecté !",
            'welcome' => "Bienvenue " . $username . " !",
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'username' => $user->getUsername(),
            ]
        ]);
    }

    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->json($this->serializeUserDetailed($user));
    }

    #[Route('/api/me', name: 'api_me_update', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_USER')]
    public function update(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }
        if (isset($data['bio'])) {
            $user->setBio($data['bio']);
        }
        if (isset($data['gender'])) {
            $user->setGender($data['gender']);
        }
        if (isset($data['age'])) {
            $user->setAge($data['age']);
        }
        if (isset($data['location'])) {
            $user->setLocation($data['location']);
        }
        if (isset($data['latitude'])) {
            $user->setLatitude($data['latitude']);
        }
        if (isset($data['longitude'])) {
            $user->setLongitude($data['longitude']);
        }
        if (isset($data['preferences'])) {
            $user->setPreferences($data['preferences']);
        }
        if (isset($data['tags'])) {
            $tagNames = $data['tags'];
            $user->getTags()->clear();
            foreach ($tagNames as $tagName) {
                $tag = $this->em->getRepository(ProfileTag::class)->findOneBy(['name' => $tagName]);
                if (!$tag) {
                    $tag = new ProfileTag();
                    $tag->setName($tagName);
                    $this->em->persist($tag);
                }
                $user->addTag($tag);
            }
        }

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return $this->json(['errors' => $messages], 400);
        }

        $this->em->flush();

        return $this->json($this->serializeUserDetailed($user));
    }

    #[Route('/api/me/avatar', name: 'api_me_avatar', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function uploadAvatar(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        // Vérifier que la requête contient des fichiers
        if (!$request->files->has('avatar')) {
            return $this->json(['error' => 'No file uploaded. Please select a file.'], 400);
        }

        $file = $request->files->get('avatar');
        if (!$file) {
            return $this->json(['error' => 'No file uploaded'], 400);
        }

        // Vérifier que c'est bien un fichier uploadé
        if (!$file->isValid()) {
            return $this->json(['error' => 'Invalid file upload: ' . $file->getErrorMessage()], 400);
        }

        // Vérifier le type de fichier (essayer getMimeType, sinon utiliser l'extension)
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        $isValid = false;
        try {
            $mimeType = $file->getMimeType();
            if (in_array($mimeType, $allowedMimeTypes)) {
                $isValid = true;
            }
        } catch (\Exception $e) {
            // Si getMimeType() échoue, vérifier l'extension
            $extension = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
            if (in_array($extension, $allowedExtensions)) {
                $isValid = true;
            }
        }
        
        if (!$isValid) {
            return $this->json(['error' => 'Invalid file type. Allowed types: JPEG, PNG, GIF, WebP'], 400);
        }

        // Vérifier la taille (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file->getSize() > $maxSize) {
            return $this->json(['error' => 'File too large. Maximum size: 5MB'], 400);
        }

        try {
            // Supprimer l'ancien avatar si existe
            if ($user->getAvatarUrl()) {
                $this->mediaService->deleteAvatar($user->getAvatarUrl());
            }

            $avatarUrl = $this->mediaService->uploadAvatar($file, $user);
            $user->setAvatarUrl($avatarUrl);
            $this->em->flush();

            return $this->json([
                'avatarUrl' => $avatarUrl
            ]);
        } catch (\Exception $e) {
            // Logger l'erreur pour le débogage
            error_log('Avatar upload error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            // Retourner un message d'erreur clair
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'Impossible de créer') !== false || 
                strpos($errorMessage, 'n\'est pas accessible') !== false) {
                $errorMessage = 'Erreur de permissions. Vérifiez que le serveur peut écrire dans le dossier uploads.';
            }
            
            return $this->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    private function serializeUserDetailed(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'bio' => $user->getBio(),
            'gender' => $user->getGender(),
            'age' => $user->getAge(),
            'location' => $user->getLocation(),
            'latitude' => $user->getLatitude(),
            'longitude' => $user->getLongitude(),
            'preferences' => $user->getPreferences(),
            'avatarUrl' => $user->getAvatarUrl(),
            'level' => $user->getLevel(),
            'xp' => $user->getXp(),
            'score' => $user->getScore(),
            'tags' => array_map(fn(ProfileTag $tag) => $tag->getName(), $user->getTags()->toArray()),
            'roles' => $user->getRoles(),
            'createdAt' => $user->getCreatedAt()?->format('c'),
            'updatedAt' => $user->getUpdatedAt()?->format('c')
        ];
    }
}

