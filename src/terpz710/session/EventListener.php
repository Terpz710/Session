<?php

declare(strict_types=1);

namespace terpz710\session;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\CraftItemEvent;

use pocketmine\player\Player;

use terpz710\session\task\PlaytimeTask;

class EventListener implements Listener {

    private $plugin;

    private array $playtimeTasks = [];

    public function __construct() {
        $this->plugin = Loader::getInstance();
    }

    public function onPlayerJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $name = $player->getName();
        $sessionManager = $this->plugin->getSessionManager();

        $sessionManager->openSession($player);

        $userData = $sessionManager->getSession($player)->getUserData();
        $userData->updateUsername($name);

        $playtimeTask = new PlaytimeTask($userData);
        $this->plugin->getScheduler()->scheduleRepeatingTask($playtimeTask, 20);
        $this->playtimeTasks[$player->getName()] = $playtimeTask;
    }

    public function onPlayerQuit(PlayerQuitEvent $event) : void{
        $player = $event->getPlayer();
        $sessionManager = $this->plugin->getSessionManager();

        if ($sessionManager->inSession($player)) {
            $sessionManager->closeSession($player);
        }

        if (isset($this->playtimeTasks[$player->getName()])) {
            $task = $this->playtimeTasks[$player->getName()];
            $task->getHandler()?->cancel();
            unset($this->playtimeTasks[$player->getName()]);
        }
    }

    public function onBlockBreak(BlockBreakEvent $event) : void{
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $sessionManager = $this->plugin->getSessionManager();
        $session = $sessionManager->getSession($player)->getUserData();

        if ($sessionManager->inSession($player)) {
            $session->setLastBlockMined($block->getName());
            $session->setTotalBlockMined(1);
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event) : void{
        $player = $event->getPlayer();
        $item = $event->getItem();

        $sessionManager = $this->plugin->getSessionManager();
        $session = $sessionManager->getSession($player)->getUserData();

        if ($sessionManager->inSession($player)) {
            $session->setLastBlockPlaced($item->getName());
            $session->setTotalBlockPlaced(1);
        }
    }

    public function onPickup(EntityItemPickupEvent $event) : void{
        $entity = $event->getEntity();
        $item = $event->getItem();
        $sessionManager = $this->plugin->getSessionManager();
        
        if ($entity instanceof Player) {
            if ($sessionManager->inSession($entity)) {
                $sessionManager->getSession($entity)->getUserData()->setLastItemPickedUp($item->getVanillaName());
            }
        }
    }

    public function onKill(PlayerDeathEvent $event) : void{
        $player = $event->getPlayer();
        $sessionManager = $this->plugin->getSessionManager();
        $session = $sessionManager->getSession($player)->getUserData();
        
	if ($player instanceof Player) {
	    $session->addDeath(1);
	}
        
	$cause = $player->getLastDamageCause();
        
	if ($cause instanceof EntityDamageByEntityEvent) {
	    $damager = $cause->getDamager();
	    if ($damager instanceof Player) {
	        $session->addKill(1);
	    }
	}
    }

    public function onRespawn(PlayerRespawnEvent $event) : void{
        $player = $event->getPlayer()
        $sessionManager = $this->plugin->getSessionManager();

        if ($sessionManager->inSession($player)) {
            $sessionManager->getSession($player)->getUserData()->addRespawn(1);
        }
    }

    public function onCraft(CraftItemEvent $event) : void{
        $player = $event->getPlayer();
        $sessionManager = $this->plugin->getSessionManager();

        if ($sessionManager->inSession($player)) {
            $sessionManager->getSession($player)->getUserData()->setTotalItemCraft(1);
        }
    }
}
