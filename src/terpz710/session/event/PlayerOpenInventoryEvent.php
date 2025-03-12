<?php

declare(strict_types=1);

namespace terpz710\session\event;

use pocketmine\event\Event;

use pocketmine\player\Player;

class PlayerOpenInventoryEvent extends Event {

    public function __construct(protected Player $player) {
        $this->player = $player;
    }

    public function getPlayer() : Player{
        return $this->player;
    }
}
