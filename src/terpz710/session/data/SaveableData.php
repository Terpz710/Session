<?php

declare(strict_types=1);

namespace terpz710\session\data;

use pocketmine\player\Player;

interface SaveableData {

    public function getId(Player|string $player) : ?string;

    public function savePlayerData();

    public function deletePlayerData(Player|string $player);

    public function getName(string $player) : string;

}