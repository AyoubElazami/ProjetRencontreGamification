<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\MatchRequestRepository;
use App\Repository\UserMatchRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecommendationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MatchRequestRepository $matchRequestRepo,
        private UserMatchRepository $matchRepo
    ) {}

    public function getRecommendations(User $user, int $limit = 20): array
    {
        $preferences = $user->getPreferences() ?? [];
        $ageRange = $preferences['age_range'] ?? [18, 99];
        $distanceKm = $preferences['distance_km'] ?? 50;
        $genderPref = $preferences['gender_pref'] ?? null;

        $qb = $this->em->createQueryBuilder();
        $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.id != :currentUser')
            ->setParameter('currentUser', $user->getId());

        // Filtrer par préférences de genre
        if ($genderPref) {
            $qb->andWhere('u.gender = :gender')
               ->setParameter('gender', $genderPref);
        }

        // Filtrer par âge
        if ($user->getAge() && isset($ageRange[0], $ageRange[1])) {
            $qb->andWhere('u.age >= :minAge')
               ->andWhere('u.age <= :maxAge')
               ->setParameter('minAge', $ageRange[0])
               ->setParameter('maxAge', $ageRange[1]);
        }

        // Exclure les utilisateurs déjà matchés ou avec requête en cours
        $existingMatches = $this->matchRepo->findUserMatches($user);
        $excludedIds = [$user->getId()];
        foreach ($existingMatches as $match) {
            $otherUser = $match->getUserA()->getId() === $user->getId() 
                ? $match->getUserB() 
                : $match->getUserA();
            $excludedIds[] = $otherUser->getId();
        }

        $qb->andWhere('u.id NOT IN (:excluded)')
           ->setParameter('excluded', $excludedIds);

        // Trier par score (leaderboard)
        $qb->orderBy('u.score', 'DESC')
           ->addOrderBy('u.createdAt', 'DESC')
           ->setMaxResults($limit);

        $candidates = $qb->getQuery()->getResult();

        // Scoring basé sur les tags en commun
        $userTags = $user->getTags();
        $scoredCandidates = [];

        foreach ($candidates as $candidate) {
            $score = $this->calculateCompatibilityScore($user, $candidate, $userTags);
            $scoredCandidates[] = [
                'user' => $candidate,
                'score' => $score
            ];
        }

        // Trier par score de compatibilité
        usort($scoredCandidates, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_map(fn($item) => $item['user'], $scoredCandidates);
    }

    private function calculateCompatibilityScore(User $user1, User $user2, $user1Tags): float
    {
        $score = 0.0;

        // Tags en commun
        $user2Tags = $user2->getTags();
        $commonTags = 0;
        foreach ($user1Tags as $tag) {
            if ($user2Tags->contains($tag)) {
                $commonTags++;
            }
        }
        $score += $commonTags * 10;

        // Proximité d'âge
        if ($user1->getAge() && $user2->getAge()) {
            $ageDiff = abs($user1->getAge() - $user2->getAge());
            $score += max(0, 20 - $ageDiff);
        }

        // Distance géographique (si disponible)
        if ($user1->getLatitude() && $user1->getLongitude() 
            && $user2->getLatitude() && $user2->getLongitude()) {
            $distance = $this->calculateDistance(
                (float)$user1->getLatitude(),
                (float)$user1->getLongitude(),
                (float)$user2->getLatitude(),
                (float)$user2->getLongitude()
            );
            $score += max(0, 30 - ($distance / 10));
        }

        return $score;
    }

    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}

