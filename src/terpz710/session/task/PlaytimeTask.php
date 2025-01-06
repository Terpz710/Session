<?php

declare(strict_types=1);

namespace terpz710\session\task;

use pocketmine\scheduler\Task;

use terpz710\session\utils\User;

class PlaytimeTask extends Task {

    public function __construct(private User $userData) {
        $this->userData = $userData;
    }

    public function onRun() : void{
        $this->userData->addPlaytime(1);
    }
}
