<?php
namespace App\Event;

use App\Entity\UserMatch;
use Symfony\Contracts\EventDispatcher\Event;

class MatchCreatedEvent extends Event
{
    public const NAME = 'match.created';

    public function __construct(
        private UserMatch $match
    ) {}

    public function getMatch(): UserMatch
    {
        return $this->match;
    }
}

