<?php

namespace _64FF00\xPermissions\data;

use _64FF00\xPermissions\xPermissions;

use pocketmine\utils\Config;

class Group
{
	private $groupName, $plugin;
	
	public function __construct(xPermissions $plugin, $groupName)
	{
		$this->plugin = $plugin;
		$this->groupName = $groupName;
	}
	
	public function addGroupPermission($permission, $level)
	{
		$temp_config = $this->getWorldLoadedData($level);
		
		$permissions = $temp_config[$this->groupName]["worlds"][$level]["permissions"];
		
		$permissions[] = $permission;
		
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
	
	public function getGroupPermissions($level)
	{
		$permissions = $this->getWorldLoadedData($level)[$this->groupName]["worlds"][$level]["permissions"];
		
		$inherited_groups = $this->getInheritedGroups();
		
		if(isset($inherited_groups) and is_array($inherited_groups))
		{
			foreach($inherited_groups as $groupName)
			{
				$group = $this->plugin->getGroup($groupName);
				
				if($group === null)
				{
					$this->plugin->getLogger()->error("Group " . $groupName . " not found in " . $this->groupName . "'s inheritance section.");
					
					continue;
				}
				
				$group_permissions = $group->getWorldLoadedData($level)[$group->groupName]["worlds"][$level]["permissions"];
				
				$permissions = array_merge($permissions, $group_permissions);
			}
		}
		
		return $permissions;
	}
	
	public function getName()
	{
		return $this->groupName;
	}
	
	public function getGroupPrefix()
	{
		return $this->getGroupData()["prefix"];
	}
	
	public function getGroupSuffix()
	{
		return $this->getGroupData()["suffix"];
	}

	public function getInheritedGroups()
	{
		return $this->getGroupData()["inheritance"];
	}
	
	public function getWorldLoadedData($level)
	{
		$temp_config = $this->plugin->getGroupsData();
		
		if(!isset($temp_config[$this->groupName]["worlds"][$level]))
		{
			$this->plugin->getLogger()->warning("Permissions not set in Group: " . $this->groupName . " in Level: " . $level . ".");
			
			$temp_config[$this->groupName]["worlds"][$level] = array(
				"permissions" => array(
				),
			);
			
			$this->plugin->setGroupsData($temp_config);
		}
		
		return $this->plugin->getGroupsData();
	}
	
	public function isDefault()
	{
		if(!isset($this->getGroupData()["default-group"]))
		{
			return null;
		}
		
		return $this->getGroupData()["default-group"] === true;
	}
	
	public function removeGroupPermission($permission, $level)
	{
		$temp_config = $this->getWorldLoadedData($level);
		
		$permissions = $temp_config[$this->groupName]["worlds"][$level]["permissions"];
		
		if(!isset($permissions) || !in_array($permission, $permissions)) return false;
		
		$temp_config[$this->groupName]["worlds"][$level]["permissions"] = array_diff($permissions, [$permission]);
		
		$this->plugin->setGroupsData($temp_config);
		
		return true;
	}
}