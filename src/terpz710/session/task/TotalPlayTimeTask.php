<?php

declare(strict_types=1);

namespace terpz710\session\task;

use pocketmine\scheduler\Task;

use pocketmine\Server;

use terpz710\session\data\Data;

class TotalPlayTimeTask extends Task {

    public function __construct(private Data $data) {
        $this->data = $data;
    }

    public function onRun() : void{
        foreach(Server::getInstance()->getOnlinePlayers() as $player) {
            $this->data->addPlaytime($player, 1);
        }
    }
}