<?php

/*
*  _____           _        _   _____  _____   _____ 
* |  __ \         | |      | | |  __ \|  __ \ / ____|
* | |__) |__   ___| | _____| |_| |__) | |__) | |  __ 
* |  ___/ _ \ / __| |/ / _ \ __|  _  /|  ___/| | |_ |
* | |  | (_) | (__|   <  __/ |_| | \ \| |    | |__| |
* |_|   \___/ \___|_|\_\___|\__|_|  \_\_|     \_____|
*
*/                                                    
                                                   
namespace PocketRPG;

use PocketRPG\commands\QuestCommands;
use PocketRPG\commands\RpgCommands;
use PocketRPG\commands\PartyCommands;
use PocketRPG\eventlistener\EventListener;
use PocketRPG\tasks\ManaTask;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\Config;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\level\Level;
use pocketmine\level\Position;

class Main extends PluginBase implements Listener {
  
  public function onEnable() {
    $this->getLogger()->info(TF:: GREEN . "Enabling PocketRPG");
    $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    $this->getCommand("rpg")->setExecutor(new RpgCommands($this));
    $this->getCommand("quest")->setExecutor(new QuestCommands($this));
    $this->getCommand("party")->setExecutor(new PartyCommands($this));
    $this->getServer ()->getScheduler()->scheduleRepeatingTask (new ManaTask($this), 40);
    
    @mkdir($this->getDataFolder());
    $this->saveResource("config.yml");
    $this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML);
    $this->playerclass = new Config($this->getDataFolder(). "class.yml", Config::YAML);
  }
  
  public function onDisable() {
    $this->getLogger()->info(TF:: RED . "Disabling PocketRPG");
  }

      ////////// API \\\\\\\\\\

  public function setClass(Player $p, $class) {
      $this->playerclass->set($p->getName(), $class);
      $this->playerclass->set($p->getName() . ".class", true);
	  $this->playerclass->save();
  }
  
  public function getClass(Player $p) {
    $class = $this->playerclass->get($p->getName());
    return $class;
  }
  
  public function unsetClass(Player $p){
    $this->playerclass->set($p->getName(). ".class", false);
    //unset($this->playerclass->get($p->getName()));
    $this->playerclass->save();
  }

  public function hasQuestFinished(Player $p, $quest) {
    $this->quest = new Config($this->getDataFolder() . "quests/" . $quest . ".yml");
    if($this->quest->get("Finished", $p->getName()) != NULL) {
      return true;
    }
  }

  public function hasQuestStarted(Player $p, $quest) {
    $this->quest = new Config($this->getDataFolder() . "quests/" . $quest . ".yml");
    if($this->quest->get("Started", $p->getName()) != NULL) {
      return true;
    }
  }
}
