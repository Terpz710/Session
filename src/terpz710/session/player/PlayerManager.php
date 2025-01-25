<?php

declare(strict_types=1);

namespace terpz710\session\player;

use pocketmine\player\Player;

final class PlayerManager {

    protected static self $instance;

    public function __construct(protected Player $player) {
        $this->player = $player;

        self::$instance = $this;
    }

    public function getPlayer() : Player{
        return $this->player;
    }

    public static function getInstance() : self{
        return self::$instance;
    }
}