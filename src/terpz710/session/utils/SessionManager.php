<?php

declare(strict_types=1);

namespace terpz710\session\utils;

use pocketmine\player\Player;

final class SessionManager {

    private array $sessions = [];

    public function openSession(Player $player) : Session{
        $session = new Session($player);
        $this->sessions[$player->getName()] = $session;
        return $session;
    }

    public function getSession(Player $player) : ?Session{
        return $this->sessions[$player->getName()] ?? null;
    }

    public function inSession(Player $player) : bool{
        return isset($this->sessions[$player->getName()]);
    }

    public function closeSession(Player $player) : void{
        $session = $this->getSession($player);

        if ($session !== null) {
            $session->getUserData()->setLastJoin();
            $session->getUserData()->saveLogoutCoordinates($player);
        }

        unset($this->sessions[$player->getName()]);
    }

    public function getAllSessions() : array{
        return $this->sessions;
    }
}