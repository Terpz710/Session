# Description
This plugin was supposed to be used by WavyCraftNetwork's KitPvP server. Plans changed and so this plugin is now public!

Sessions is a plugin that runs on PocketMine-MP API 5.0.0-latest. Sessions creates a session instance for a player when they join, once a player is in session it'll save certain data not saved by the internal server such as last log out location, last block mined/placed and much more!

Currently Sessions is still under development so everything is bare-bones!

**Note:**
All data is saved within a JSON file, I'll add MySQL support or similar in the near future!

# API/For Developers!
**How to retrieve player data without the user being in a session**
```
use terpz710\session\utils\User;

$player should be an instance of Player::class

$user = new User($player);

$user->getLogoutCoordinates(); returns the users last logout position including world folder name.

$user->getPlaytime(); returns the total amount of time spent on the server. (e.g 1d15m35s)

$user->getFirstJoin(); returns the join-date including time of a user.

$user->getLastJoin(); returns the last date and time a user has joined.

$user->getLastBlockMined(); returns the name of the last block mined.

$user->getLastBlockPlaced(); returns the name of the last block placed.

$user->getLastItemPickedUp(); returns the name of the last item picked up.

$user->getUsername(); returns the username thats in the user data.
```

**How to retrieve player data with the user being in a session**
```
use terpz710\session\Loader as SessionLoader;

$player should be an instance of Player::class

$manager = SessionLoader::getInstance()->getSessionManager();

$user = $manager->getSession($player)->getUserData();

$user->getUsername();
```

**How to get a session of a user**
```
use terpz710\session\Loader as SessionLoader;

$player should be an instance of Player::class

$manager = SessionLoader::getInstance()->getSessionManager();

$manager->getSession($player);
```

**How to save user data**
```
The user/player MUST be in a session in order to save data, failure to do so will return a crash/error!

See example below.

use terpz710\session\Loader as SessionLoader;

$player should be an instance of Player::class

$manager = SessionLoader::getInstance()->getSessionManager();

Check to see if the player who triggered the event is in a session, if yes it will call the function of your choice. The player will always be in a session unless the player is offline which will never happened....

if ($manager->inSession($player)) {
    $manager->getSession($player)->getUserData()->setLastBlockMined($block->getName);
}
```
