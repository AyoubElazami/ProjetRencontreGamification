<?php
namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private NotificationRepository $notificationRepo
    ) {}

    public function createNotification(
        User $user,
        string $type,
        ?array $payload = null
    ): Notification {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType($type);
        $notification->setPayload($payload);

        $this->em->persist($notification);
        $this->em->flush();

        // TODO: Envoyer une notification push (APNs/Firebase)
        // $this->pushNotificationService->send($user, $notification);

        return $notification;
    }

    public function markAsRead(Notification $notification): void
    {
        if (!$notification->isRead()) {
            $notification->setReadAt(new \DateTimeImmutable());
            $this->em->flush();
        }
    }

    public function markAllAsRead(User $user): void
    {
        $unread = $this->notificationRepo->findUnreadByUser($user);
        foreach ($unread as $notification) {
            $notification->setReadAt(new \DateTimeImmutable());
        }
        $this->em->flush();
    }

    public function getUserNotifications(User $user, int $limit = 50, int $offset = 0): array
    {
        return $this->notificationRepo->findByUser($user, $limit, $offset);
    }

    public function getUnreadCount(User $user): int
    {
        return count($this->notificationRepo->findUnreadByUser($user));
    }
}

