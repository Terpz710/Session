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
        return $this->config->get($this->UUID())["logout_coordinates"];
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
        return $this->config->get($this->UUID())["last_block_placed"];
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

    public function getUsername() : string{
        return $this->config->get($this->getUUID())["username"];
    }

    public function updateUsername(string $name) : void{
        $uuid = $this->getUUID();
        $data = $this->config->get($uuid);
        $data["username"] = $name;
        $this->config->set($uuid, $data);
        $this->config->save();
    }
}