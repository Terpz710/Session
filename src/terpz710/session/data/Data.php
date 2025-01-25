<?php

declare(strict_types=1);

namespace terpz710\session\data;

use pocketmine\player\Player;

use pocketmine\utils\Config;

use function date;

use DateTime;
use DateTimeZone;

use terpz710\session\Loader;

final class Data implements IData, SaveableData {

    private Config $data;

    public function __construct(Player $player) {
        $this->data = new Config(Loader::getInstance()->getDataFolder() . "player_data.json");
        $uuid = $player->getUniqueId()->toString();
        $date = date("m-d-Y");
        $dateTime = new DateTime("now", new DateTimeZone("America/Los_Angeles"));
        $time = (string) $dateTime->format("h:i A");

        if (!$this->data->exists($uuid)) {
            $this->data->set($uuid, [
                "username" => $player->getName(),
                "join_date" => $date . " " . $time,
                "last_logout_position" => null,
                "total_playtime" => 0,
                "total_joins" => 0,
                "total_quit" => 0
            ]);
            $this->savePlayerData();
        }
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

    public function hasPlayerData(Player|string $player): bool {
        $uuid = $this->getId($player);
        return $uuid !== null && $this->data->exists($uuid);
    }

    public function addJoin(Player|string $player): void {
        $uuid = $this->getId($player);
        if ($uuid !== null) {
            $data = $this->data->get($uuid);
            $data["total_joins"] += 1;
            $this->data->set($uuid, $data);
            $this->savePlayerData();
        }
    }

    public function addQuit(Player|string $player): void {
        $uuid = $this->getId($player);
        if ($uuid !== null) {
            $data = $this->data->get($uuid);
            $data["total_quit"] += 1;
            $this->data->set($uuid, $data);
            $this->savePlayerData();
        }
    }

    public function saveLogoutPosition(Player $player): void {
        $uuid = $this->getId($player);
        if ($uuid !== null) {
            $data = $this->data->get($uuid);
            $data["last_logout_position"] = sprintf(
                "World: %s, X: %.2f, Y: %.2f, Z: %.2f",
                $player->getWorld()->getFolderName(),
                $player->getPosition()->getX(),
                $player->getPosition()->getY(),
                $player->getPosition()->getZ()
            );
            $this->data->set($uuid, $data);
            $this->savePlayerData();
        }
    }

    public function updateName(Player|string $player, string $name): void {
        $uuid = $this->getId($player);
        if ($uuid !== null) {
            $data = $this->data->get($uuid);
            $data["username"] = $name;
            $this->data->set($uuid, $data);
            $this->savePlayerData();
        }
    }

    public function addPlaytime(Player|string $player, int $seconds): void {
        $uuid = $this->getId($player);
        if ($uuid !== null) {
            $data = $this->data->get($uuid);
            $data["total_playtime"] += $seconds;
            $this->data->set($uuid, $data);
            $this->savePlayerData();
        }
    }

    public function getSavedData() : SavedData{
        return SavedData::getInstance();
    }

    public function savePlayerData() {
        $this->data->save();
    }

    /* TODO: Make use for this function, Preferably in some sort of AntiCheat. **/
    public function deletePlayerData(Player|string $player) {
        $uuid = $this->getId($player);
        if ($uuid !== null) {
            $this->data->remove($uuid);
            $this->savePlayerData();
        }
    }

    public function getName(string $player) : string{
        $uuid = $this->getId($player);
        if ($uuid === null) {
            return null;
        }

        $data = $this->data->get($uuid);
        return $data["username"] ?? null;
    }
}