<?php

namespace _64FF00\xPermissions\data;

use _64FF00\xPermissions\xPermissions;

use pocketmine\IPlayer;

use pocketmine\level\Level;

use pocketmine\utils\Config;

class User
{
	private $config, $plugin, $player;
	
	public function __construct(xPermissions $plugin, IPlayer $player)
	{
		$this->plugin = $plugin;
		$this->player = $player;
		
		$this->loadUserConfig();
	}
	
	public function addUserPermission(Level $level, $permission)
	{
		$temp_config = $this->getWorldLoadedData($level);
		
		array_push($temp_config["worlds"][$level->getName()]["permissions"], $permission);
		
		$this->setUserData($temp_config);
	}
	
	public function getPlayer()
	{
		return $this->player;
	}
	
	public function getUserData()
	{		
		return $this->config->getAll();
	}
	
	public function getUserGroup(Level $level)
	{
		return new Group($this->plugin, $this->getWorldLoadedData($level)["worlds"][$level->getName()]["group"]);
	}
	
	public function getUserPermissions(Level $level)
	{
		return $this->getWorldLoadedData($level)["worlds"][$level->getName()]["permissions"];
	}
	
	public function getWorldLoadedData(Level $level)
	{
		$temp_config = $this->getUserData();
		
		if(!isset($temp_config["worlds"][$level->getName()]))
		{
			$temp_config["worlds"][$level->getName()] = array(
				"group" => $this->plugin->getDefaultGroup()->getName(),
				"permissions" => array(
				),
			);
			
			$this->setUserData($temp_config);
		}
		
		return $this->getUserData();
	}
	
	public function loadUserConfig()
	{
		if(!(file_exists($this->plugin->getDataFolder() . "players/" . strtolower($this->player->getName()) . ".yml")))
		{
			$this->config = new Config($this->plugin->getDataFolder() . "players/" . strtolower($this->player->getName()) . ".yml", Config::YAML, array(
				"username" => $this->getPlayer()->getName(),
				"worlds" => array(
				)
			));
		}
		else
		{
			$this->config = new Config($this->plugin->getDataFolder() . "players/" . strtolower($this->player->getName()) . ".yml", Config::YAML, array(
			));
		}
	}
	
	public function removeUserPermission(Level $level, $permission)
	{
		$temp_config = $this->getWorldLoadedData($level);
		
		if(!isset($temp_config["worlds"][$level->getName()]["permissions"][$permission])) return false;
		
		unset($temp_config["worlds"][$level->getName()]["permissions"][$permission]);
			
		$this->setUserData($temp_config);
			
		return true;
	}
	
	public function setUserData($temp_config)
	{
		if(is_array($temp_config))
		{
			$this->config->setAll($temp_config);
			
			$this->config->save();
		}
	}
	
	public function setUserGroup(Level $level, Group $group)
	{
		$temp_config = $this->getWorldLoadedData($level);
		
		$temp_config["worlds"][$level->getName()]["group"] = $group->getName();
		
		$this->setUserData($temp_config);
	}
}