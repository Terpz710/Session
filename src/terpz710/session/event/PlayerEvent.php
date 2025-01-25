<?php

declare(strict_types=1);

namespace terpz710\session\event;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

use terpz710\session\Loader;

use terpz710\session\task\TotalPlayTimeTask;

class PlayerEvent implements Listener {

    protected array $playtimeTasks = [];

    public function join(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $name = $player->getName();
        $sessionManager = Loader::getInstance()->getSessionManager();

        /* Opens a session for the connecting player **/
        $sessionManager->openSession($player);

        $data = $sessionManager->getSession($player)->getData();

        $data->updateName($player, $name);
        $data->addJoin($player);

        $playtimeTask = new TotalPlayTimeTask($data);
        Loader::getInstance()->getScheduler()->scheduleRepeatingTask($playtimeTask, 20);
        $this->playtimeTasks[$player->getUniqueId()->getBytes()] = $playtimeTask;
    }

    public function quit(PlayerQuitEvent $event) : void{
        $player = $event->getPlayer();
        $sessionManager = Loader::getInstance()->getSessionManager();

        /* Closes the session for the disconnecting player **/
        if ($sessionManager->inSession($player)) {
            $sessionManager->closeSession($player);
        }

        if (isset($this->playtimeTasks[$player->getUniqueId()->getBytes()])) {
            $task = $this->playtimeTasks[$player->getUniqueId()->getBytes()];
            $task->getHandler()?->cancel();
            unset($this->playtimeTasks[$player->getUniqueId()->getBytes()]);
        }
    }
}