<?php

declare(strict_types=1);

namespace terpz710\session\session;

use pocketmine\player\Player;

use terpz710\session\data\Data;
use terpz710\session\data\SavedData;

final class Session {

    protected static self $instance;

    protected Player $player;

    protected Data $data;

    protected SavedData $savedData;

    public function __construct(Player $player) {
        $this->player = $player;
        
        $this->data = new Data($player);
        
        $this->savedData = SavedData::getInstance();

        self::$instance = $this;
    }

    public function getPlayer() : Player{
        return $this->player;
    }

    public function getData() : Data{
        return $this->data;
    }

    public function getSavedData() : SavedData{
        return $this->savedData;
    }

    public static function getInstance() : self{
        return self::$instance;
    }
}