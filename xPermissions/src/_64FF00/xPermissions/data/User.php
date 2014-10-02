<?php

namespace _64FF00\xPermissions\data;

use _64FF00\xPermissions\xPermissions;

use pocketmine\IPlayer;

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
	
	public function addUserPermission($permission, $level)
	{
		$temp_config = $this->getWorldLoadedData($level);
		
		$temp_config["worlds"][$level]["permissions"][] = $permission;
		
		$this->setUserData($temp_config);
	}
	
	public function getDataPath()
	{
		$userName = $this->getPlayer()->getName();
		
		return $this->plugin->getDataFolder() . "players/" . strtolower($userName) . ".yml";
	}
	
	public function getPlayer()
	{
		return $this->player;
	}
	
	public function getUserData()
	{		
		return $this->config->getAll();
	}
	
	public function getUserGroup($level)
	{
		$groupName = $this->getWorldLoadedData($level)["worlds"][$level]["group"];
		
		$userGroup = $this->plugin->getGroup($groupName);
		
		if($userGroup == null)
		{
			$userGroup = $this->plugin->getDefaultGroup();
			
			$this->setUserGroup($userGroup, $level);
		}
		
		return $userGroup;
	}
	
	public function getUserPermissions($level)
	{
		return $this->getWorldLoadedData($level)["worlds"][$level]["permissions"];
	}
	
	public function getWorldLoadedData($level)
	{
		$temp_config = $this->getUserData();
		
		if(!isset($temp_config["worlds"][$level]))
		{
			$temp_config["worlds"][$level] = array(
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
		$data_path = $this->getDataPath();
		
		if(!(file_exists($data_path)))
		{
			$this->config = new Config($data_path, Config::YAML, array(
				"username" => $this->getPlayer()->getName(),
				"worlds" => array(
				)
			));
		}
		else
		{
			$this->config = new Config($data_path, Config::YAML, array(
			));
		}
	}
	
	public function removeUserPermission($permission, $level)
	{
		$temp_config = $this->getWorldLoadedData($level);
		
		$permissions = $temp_config["worlds"][$level]["permissions"];
		
		if(!isset($permissions) || !in_array($permission, $permissions)) return false;
		
		$temp_config["worlds"][$level]["permissions"] = array_diff($permissions, [$permission]);
		
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
	
	public function setUserGroup(Group $group, $level)
	{
		$temp_config = $this->getWorldLoadedData($level);
		
		$temp_config["worlds"][$level]["group"] = $group->getName();
		
		$this->setUserData($temp_config);
	}
}