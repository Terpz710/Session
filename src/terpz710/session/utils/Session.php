<?php

declare(strict_types=1);

namespace terpz710\session\utils;

use pocketmine\player\Player;

final class Session {

    private $player;
    private $userData;

    public function __construct(Player $player) {
        $this->player = $player;
        $this->userData = new User($player);
    }

    public function getPlayer() : Player{
        return $this->player;
    }

    public function getUserData() : User{
        return $this->userData;
    }
}