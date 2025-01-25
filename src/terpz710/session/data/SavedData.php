<?php

declare(strict_types=1);

namespace terpz710\session\data;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

use pocketmine\player\Player;

use terpz710\session\Loader;

final class SavedData {
    use SingletonTrait;

    private Config $data;

    public function __construct() {
        $this->data = new Config(Loader::getInstance()->getDataFolder() . "player_data.json");
    }

    public function getId(Player|string $player) : ?string{
        if ($player instanceof Player) {
            return $player->getUniqueId()->toString();
        }

        foreach ($this->data->getAll() as $uuid => $record) {
            if (strcasecmp($record["username"], $player) === 0) {
                return $uuid;
            }
        }

        return null;
    }

    public function getLogoutPosition(Player|string $player) {
        $uuid = $this->getId($player);

        if ($uuid === null) {
            return;
        }

        $data = $this->data->get($uuid);
        return $data ? $data["last_logout_position"] : null;
    }

    public function getJoinDate(Player|string $player) {
        $uuid = $this->getId($player);

        if ($uuid === null) {
            return;
        }

        $data = $this->data->get($uuid);
        return $data ? $data["join_date"] : null;
    }

    public function getTotalJoins(Player|string $player) {
        $uuid = $this->getId($player);

        if ($uuid === null) {
            return;
        }

        $data = $this->data->get($uuid);
        return $data ? $data["total_joins"] : null;
    }

    public function getTotalQuit(Player|string $player) {
        $uuid = $this->getId($player);

        if ($uuid === null) {
            return;
        }

        $data = $this->data->get($uuid);
        return $data ? $data["total_quit"] : null;
    }

    public function hasPlayerData(Player|string $player) : bool{
        $uuid = $this->getId($player);
        return $this->data->exist($uuid);
    }

    public function getTotalPlaytime() {
        $uuid = $this->getId($player);

        if ($uuid === null) {
            return;
        }

        $data = $this->data->get($uuid);

        $seconds = $this->data->get(uuid)["total_playtime"];

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

    public function getName(string $player) {
        $uuid = $this->getId($player);

        if ($uuid === null) {
            return;
        }

        $data = $this->data->get($uuid);
        return $data ? $data["username"] : null;
    }
}