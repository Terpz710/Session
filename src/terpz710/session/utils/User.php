<?php

declare(strict_types=1);

namespace terpz710\session\utils;

use pocketmine\player\Player;

use pocketmine\utils\Config;

use function date;

use DateTime;
use DateTimeZone;

use terpz710\session\Loader;

final class User {

    private $player;
    private $config;

    public function __construct(Player $player) {
        $this->player = $player;
        $this->config = new Config(Loader::getInstance()->getDataFolder() . "session.json", Config::JSON);

        $uuid = $this->player->getUniqueId()->toString();

        $date = date("m-d-Y");
        $dateTime = new DateTime("now", new DateTimeZone("America/Los_Angeles"));
        $time = (string) $dateTime->format("h:i A");

        if (!$this->config->exists($uuid)) {
            $this->config->set($uuid, [
                "username" => $this->player->getName(),
                "playtime" => 0,
                "kills" => 0,
                "deaths" => 0,
                "respawns" => 0,
                "total_block_mined" => 0,
                "total_block_placed" => 0,
                "total_item_smelt" => 0,
                "total_item_craft" => 0,
                "logout_coordinates" => null,
                "first_join" => $date . " " . $time,
                "last_join" => null,
                "last_block_mined" => null,
                "last_block_placed" => null,
                "last_item_picked_up" => null
            ]);
            $this->config->save();
        }
    }

    private function getUUID() : string{
        return $this->player->getUniqueId()->toString();
    }

    public function getLogoutCoordinates() : string{
        return $this->config->get($this->getUUID())["logout_coordinates"];
    }

    public function saveLogoutCoordinates(Player $player) : void{
        $uuid = $this->getUUID();
        $data = $this->config->get($uuid);
        $data["logout_coordinates"] = "World: " . $player->getPosition()->getWorld()->getFolderName() . ", X: " . $player->getPosition()->getX() . ", Y: " . $player->getPosition()->getY() . ", Z: " . $player->getPosition()->getZ();
        $this->config->set($uuid, $data);
        $this->config->save();
    }

    public function getPlaytime() : string{
        $seconds = $this->config->get($this->getUUID())["playtime"];

        $years = floor($seconds / (365 * 24 * 60 * 60));
        $seconds %= (365 * 24 * 60 * 60);

        $months = floor($seconds / (30 * 24 * 60 * 60));
        $seconds %= (30 * 24 * 60 * 60);

        $days = floor($seconds / (24 * 60 * 60));
        $seconds %= (24 * 60 * 60);

        $hours = floor($seconds / (60 * 60));
        $seconds %= (60 * 60);

        $minutes = floor($seconds / 60);
        $seconds %= 60;

        $formatted = [];
        if ($years > 0) $formatted[] = "{$years}y";
        if ($months > 0) $formatted[] = "{$months}mo";
        if ($days > 0) $formatted[] = "{$days}d";
        if ($hours > 0) $formatted[] = "{$hours}h";
        if ($minutes > 0) $formatted[] = "{$minutes}m";
        if ($seconds > 0 || empty($formatted)) $formatted[] = "{$seconds}s";

        return implode("", $formatted);
    }

    public function addPlaytime(int $seconds) : void{
        $uuid = $this->getUUID();
        $data = $this->config->get($uuid);
        $data["playtime"] += $seconds;
        $this->config->set($uuid, $data);
        $this->config->save();
    }

    public function getFirstJoin() : string{
        return $this->config->get($this->getUUID())["first_join"];
    }

    public function getLastJoin() : string{
        return $this->config->get($this->getUUID())["last_join"];
    }

    public function setLastJoin() : void{
        $uuid = $this->getUUID();
        $data = $this->config->get($uuid);
        $date = date("m-d-Y");
        $dateTime = new DateTime("now", new DateTimeZone("America/Los_Angeles"));
        $time = (string) $dateTime->format("h:i A");
        $data["last_join"] = $date . " " . $time;
        $this->config->set($uuid, $data);
        $this->config->save();
    }

    public function getLastBlockMined() : ?string{
        return $this->config->get($this->getUUID())["last_block_mined"];
    }

    public function setLastBlockMined(string $block) : void{
        $uuid = $this->getUUID();
        $data = $this->config->get($uuid);
        $data["last_block_mined"] = $block;
        $this->config->set($uuid, $data);
        $this->config->save();
    }

    public function getLastBlockPlaced() : ?string{
        return $this->config->get($this->getUUID())["last_block_placed"];
    }

    public function setLastBlockPlaced(string $block) : void{
        $uuid = $this->getUUID();
        $data = $this->config->get($uuid);
        $data["last_block_placed"] = $block;
        $this->config->set($uuid, $data);
        $this->config->save();
    }

    public function getLastItemPickedUp() : ?string{
        return $this->config->get($this->getUUID())["last_item_picked_up"];
    }

    public function setLastItemPickedUp(string $item) : void{
        $uuid = $this->getUUID();
        $data = $this->config->get($uuid);
        $data["last_item_picked_up"] = $item;
        $this->config->set($uuid, $data);
        $this->config->save();
    }

    public function getUsername() : ?string{
        return $this->config->get($this->getUUID())["username"];
    }

    public function updateUsername(string $name) : void{
        $uuid = $this->getUUID();
        $data = $this->config->get($uuid);
        $data["username"] = $name;
        $this->config->set($uuid, $data);
        $this->config->save();
    }

    public function getKills() : ?string{
        return $this->config->get($this->getUUID())["kills"];
    }

    public function addKill(int $kills) : void{
        $uuid = $this->getUUID();
        $data = $this->config->get($uuid);
        $data["kills"] += $kills;
        $this->config->set($uuid, $data);
        $this->config->save();
    }

    public function getDeaths() : ?string{
        return $this->config->get($this->getUUID())["deaths"];
    }

    public function addDeath(int $death) : void{
        $uuid = $this->getUUID();
        $data = $this->config->get($uuid);
        $data["deaths"] += $death;
        $this->config->set($uuid, $data);
        $this->config->save();
    }

    public function getTotalBlockMined() : ?string{
        return $this->config->get($this->getUUID())["total_block_mined"];
    }

    public function setTotalBlockMined(int $block) : void{
        $uuid = $this->getUUID();
        $data = $this->config->get($uuid);
        $data["total_block_mined"] += $block;
        $this->config->set($uuid, $data);
        $this->config->save();
    }

    public function getTotalBlockPlaced() : ?string{
        return $this->config->get($this->getUUID())["total_block_placed"];
    }

    public function setTotalBlockPlaced(int $block) : void{
        $uuid = $this->getUUID();
        $data = $this->config->get($uuid);
        $data["total_block_placed"] += $block;
        $this->config->set($uuid, $data);
        $this->config->save();
    }

    public function getRespawns() : ?string{
        return $this->config->get($this->getUUID())["respawn"];
    }

    public function addRespawn(int $respawn) : void{
        $uuid = $this->getUUID();
        $data = $this->config->get($uuid);
        $data["respawn"] += $respawn;
        $this->config->set($uuid, $data);
        $this->config->save();
    }

    public function getTotalItemSmelted() : ?string{
        return $this->config->get($this->getUUID())["total_item_smelt"];
    }

    public function setTotalItemSmelted(int $smelt) : void{
        $uuid = $this->getUUID();
        $data = $this->config->get($uuid);
        $data["total_item_smelt"] += $smelt;
        $this->config->set($uuid, $data);
        $this->config->save();
    }

    public function getTotalItemCraft() : ?string{
        return $this->config->get($this->getUUID())["total_item_craft"];
    }

    public function setTotalItemCraft(int $craft) : void{
        $uuid = $this->getUUID();
        $data = $this->config->get($uuid);
        $data["total_item_craft"] += $craft;
        $this->config->set($uuid, $data);
        $this->config->save();
    }
}
