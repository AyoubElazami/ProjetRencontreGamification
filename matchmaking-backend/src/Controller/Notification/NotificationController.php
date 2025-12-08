<?php
namespace App\Controller\Notification;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class NotificationController extends AbstractController
{
    public function __construct(
        private NotificationService $notificationService,
        private NotificationRepository $notificationRepo
    ) {}

    #[Route('/api/notifications', name: 'api_notifications_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(Request $request): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $limit = min(100, max(1, (int)$request->query->get('limit', 50)));
        $offset = max(0, (int)$request->query->get('offset', 0));

        $notifications = $this->notificationService->getUserNotifications($user, $limit, $offset);
        $unreadCount = $this->notificationService->getUnreadCount($user);

        return $this->json([
            'notifications' => array_map(fn(Notification $notif) => $this->serializeNotification($notif), $notifications),
            'unreadCount' => $unreadCount
        ]);
    }

    #[Route('/api/notifications/{id}/read', name: 'api_notification_read', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function markAsRead(int $id): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $notification = $this->notificationRepo->find($id);
        if (!$notification) {
            throw new NotFoundHttpException('Notification not found');
        }

        if ($notification->getUser()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Unauthorized'], 403);
        }

        $this->notificationService->markAsRead($notification);

        return $this->json(['success' => true]);
    }

    #[Route('/api/notifications/read-all', name: 'api_notifications_read_all', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function markAllAsRead(): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->notificationService->markAllAsRead($user);

        return $this->json(['success' => true]);
    }

    private function serializeNotification(Notification $notification): array
    {
        return [
            'id' => $notification->getId(),
            'type' => $notification->getType(),
            'payload' => $notification->getPayload(),
            'readAt' => $notification->getReadAt()?->format('c'),
            'createdAt' => $notification->getCreatedAt()?->format('c'),
            'isRead' => $notification->isRead()
        ];
    }
}

