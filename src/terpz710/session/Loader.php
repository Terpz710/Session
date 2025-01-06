<?php

declare(strict_types=1);

namespace terpz710\session;

use pocketmine\plugin\PluginBase;

use terpz710\session\utils\SessionManager;

final class Loader extends PluginBase {

    protected static $instance;

    private $sessionManager;

    protected function onLoad() : void{ self::$instance = $this; }

    protected function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->sessionManager = new SessionManager();
    }

    public static function getInstance() : self{ return self::$instance; }

    public function getSessionManager() : SessionManager{ return $this->sessionManager; }
}