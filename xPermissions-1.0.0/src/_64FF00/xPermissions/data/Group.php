<?php

namespace _64FF00\xPermissions\data;

use _64FF00\xPermissions\xPermissions;

use pocketmine\level\Level;

use pocketmine\utils\Config;

class Group
{
	private $groupName, $plugin;
	
	public function __construct(xPermissions $plugin, $groupName)
	{
		$this->plugin = $plugin;
		$this->groupName = $groupName;
	}
	
	public function addGroupPermission(Level $level, $permission)
	{
		$temp_config = $this->getWorldLoadedData($level);
		
		array_push($temp_config[$this->groupName]["worlds"][$level->getName()]["permissions"], $permission);
		
		$this->plugin->setGroupsData($temp_config);
	}
	
	public function getAlias()
	{
		return $this->getGroupData()["alias"];
	}
	
	public function getGroupData()
	{
		return $this->plugin->getGroupsData()[$this->groupName];
	}
	
	public function getGroupPermissions(Level $level)
	{
		$permissions = $this->getWorldLoadedData($level)[$this->groupName]["worlds"][$level->getName()]["permissions"];
		
		if(isset($this->getGroupData()["inheritance"]) and is_array($this->getGroupData()["inheritance"]))
		{
			foreach($this->getGroupData()["inheritance"] as $groupName)
			{
				$group = $this->plugin->getGroup($groupName);
				
				if($group != null)
				{
					$permissions = array_merge($permissions, $group->getWorldLoadedData($level)[$this->groupName]["worlds"][$level->getName()]["permissions"]);
				}
			}
		}
		
		return $permissions;
	}
	
	public function getName()
	{
		return $this->groupName;
	}
	
	public function getWorldLoadedData(Level $level)
	{
		$temp_config = $this->plugin->getGroupsData();
		
		if(!isset($temp_config[$this->groupName]["worlds"][$level->getName()]))
		{
			$temp_config[$this->groupName]["worlds"][$level->getName()] = array(
				"permissions" => array(
				),
			);
			
			$this->plugin->setGroupsData($temp_config);
		}
		
		return $this->plugin->getGroupsData();
	}
	
	public function isDefault()
	{
		$node = $this->getGroupData()["default-group"];
		
		return isset($node) and $node === true;
	}
	
	public function removeGroupPermission(Level $level, $permission)
	{
		$temp_config = $this->getWorldLoadedData($level);
		
		if(!isset($temp_config[$this->groupName]["worlds"][$level->getName()]["permissions"][$permission])) return false;
		
		unset($temp_config[$this->groupName]["worlds"][$level->getName()]["permissions"][$permission]);
		
		$this->plugin->setGroupsData($temp_config);
		
		return true;
	}
}