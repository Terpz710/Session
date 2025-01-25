<?php

declare(strict_types=1);

namespace terpz710\session\data;

use pocketmine\player\Player;

interface IData {

    public function getId(Player|string $player) : ?string;

    public function hasPlayerData(Player|string $player) : bool;

    public function getName(string $player) : string;

}