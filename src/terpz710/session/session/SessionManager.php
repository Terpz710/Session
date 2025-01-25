<?php

declare(strict_types=1);

namespace terpz710\session\session;

use pocketmine\player\Player;

final class SessionManager {

    protected array $sessions = [];

    public function __construct() {
        //NADA
    }

    public function openSession(Player $player) : Session{
        $session = new Session($player);
        $this->sessions[$player->getUniqueId()->getBytes()] = $session;
        return $session;
    }

    public function getSession(Player $player) : ?Session{
        return $this->sessions[$player->getUniqueId()->getBytes()] ?? null;
    }

    public function inSession(Player $player) : bool{
        return isset($this->sessions[$player->getUniqueId()->getBytes()]);
    }

    public function closeSession(Player $player) : void{
        $session = $this->getSession($player);

        if ($session !== null) {
            $session->getData()->addQuit($player);
            $session->getData()->saveLogoutPosition($player);
        }

        unset($this->sessions[$player->getUniqueId()->getBytes()]);
    }

    public function getAllSessions() : array{
        return $this->sessions;
    }
}
