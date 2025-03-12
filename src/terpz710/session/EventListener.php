<?php

declare(strict_types=1);

namespace terpz710\session;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityItemPickupEvent;

use pocketmine\player\Player;

use terpz710\session\task\TotalPlayTimeTask;

class EventListener implements Listener {

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

    public function break(BlockBreakEvent $event) : void{
        $player = $event->getPlayer();
        $name = $event->getBlock()->getName();
        $data = Loader::getInstance()->getSessionManager()->getSession($player)->getData();
        
        $data->addBlockMined($player, 1);
        $data->saveBlockBroken($player, $name);
    }

    public function place(BlockPlaceEvent $event) : void{
        $player = $event->getPlayer();
        $data = Loader::getInstance()->getSessionManager()->getSession($player)->getData();
        
        $data->addBlockPlaced($player, 1);
    }

    public function pickup(EntityItemPickupEvent $event) : void{
        $entity = $event->getEntity();
        $name = $event->getItem()->getVanillaName();
        
        if ($entity instanceof Player) {
            $data = Loader::getInstance()->getSessionManager()->getSession($entity)->getData();
            $data->saveItemPickedUp($entity, $name);
        }
    }
}
