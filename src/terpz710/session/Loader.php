<?php

declare(strict_types=1);

namespace terpz710\session;

use pocketmine\plugin\PluginBase;

use terpz710\session\session\SessionManager;

use terpz710\session\event\PlayerEvent;

final class Loader extends PluginBase {

    protected static self $instance;

    protected SessionManager $manager;

    public static function getInstance() : self{ return self::$instance; }

    public function getSessionManager() : SessionManager{ return $this->manager; }

    protected function onLoad() : void{ self::$instance = $this; }

    protected function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents(new PlayerEvent(), $this);

        /*
        $this->getServer()->getPluginManager()->registerEvents(new BlockEvent(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new EntityEvent(), $this);
        **/

        $this->manager = new SessionManager($this);
    }
}