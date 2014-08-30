<?php

namespace _64FF00\xPermissions;

use _64FF00\xPermissions\data\Group;
use _64FF00\xPermissions\data\User;

use pocketmine\level\Level;

use pocketmine\OfflinePlayer;

use pocketmine\permission\Permission;

use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

class xPermissions extends PluginBase
{
	private $attachments = [];
	
	private $config, $groups;
	
	public function onEnable()
	{
		$this->config = new Configuration($this);
		
		$this->loadAll();

		$this->getLogger()->info("Loaded all plugin configurations.");
		
		$this->getCommand("xperms")->setExecutor(new Commands($this));
		
		$this->getServer()->getPluginManager()->registerEvents(new xListener($this), $this);
	}
	
	public function fixGroupsData()
	{
		if($this->getDefaultGroup() == null)
		{
			$this->getLogger()->critical("Failed to load default group in groups.yml. Please check your groups.yml again.");
		}
		
		foreach($this->getAllGroups() as $group)
		{
			$groupsData = $this->getGroupsData();
			
			if(!isset($groupsData[$group->getName()]["alias"])) 
			{	
				$groupsData[$group->getName()]["alias"] = "";
			}
			
			if(!isset($groupsData[$group->getName()]["prefix"])) 
			{
				$groupsData[$group->getName()]["prefix"] = "";
			}
			
			if(!isset($groupsData[$group->getName()]["suffix"])) 
			{
				$groupsData[$group->getName()]["suffix"] = "";
			}
			
			if(!isset($groupsData[$group->getName()]["inheritance"])) 
			{	
				$groupsData[$group->getName()]["inheritance"] = [];
			}
			
			if(!isset($groupsData[$group->getName()]["worlds"]))
			{			
				$groupsData[$group->getName()]["worlds"] = [];
			}
			
			$this->setGroupsData($groupsData);
		}
	}

	public function getAllGroups()
	{
		$result = [];
		
		foreach($this->groups->getAll(true) as $groupName)
		{
			array_push($result, new Group($this, $groupName));
		}
		
		return $result;
	}
	
	public function getAttachment(Player $player)
	{
		if(!isset($this->attachments[$player->getName()]))
		{
			$this->attachments[$player->getName()] = $player->addAttachment($this);
			
			echo "NEW ATTACHMENT";
			
			return $this->attachments[$player->getName()];
		}
		
		echo "OLD ATTACHMENT";
		
		return $this->attachments[$player->getName()];
	}
	
	public function getConfiguration()
	{
		return $this->config;
	}
	
	/*
	
	public function getCustomConfig($fileName)
	{
		if(file_exists($this->getDataFolder() . $fileName))
		{
			return new Config($this->getDataFolder() . $fileName, Config::YAML, array());
		}
		
		return null;
	}
	
	*/
	
	public function getDefaultGroup()
	{
		foreach($this->getAllGroups() as $group)
		{
			if($group->isDefault()) return $group;
		}
		
		return null;
	}
	
	public function getFixedPerm($node)
	{
		return $this->isNegativePerm($node) ? substr($node, 1) : $node;
	}
	
	public function getFormattedMessage(Player $player, $message)
	{
		$format = $this->getConfiguration()->getChatFormat();
		
		$format = str_replace("{USER_NAME}", $player->getName(), $format);	
		$format = str_replace("{MESSAGE}", $message, $format);
		
		$level = $player->getLevel()->getName();
		
		$group = $this->getUser($player->getName())->getUserGroup($level);
		
		$prefix = $group->getGroupPrefix();
		
		if($prefix == null)
		{	
			$prefix = "";
		}
		
		$suffix = $group->getGroupSuffix();
		
		if($suffix == null)
		{		
			$suffix = "";
		}
		
		$format = str_replace("{PREFIX}", $prefix, $format);
		$format = str_replace("{SUFFIX}", $suffix, $format);
		
		return $format;
	}
	
	public function getGroup($groupName)
	{
		foreach($this->getAllGroups() as $group)
		{
			if(strtolower($group->getName()) == strtolower($groupName) || strtolower($group->getAlias()) == strtolower($groupName)) return $group;
		}
		
		return null;
	}
	
	public function getGroupsData()
	{
		return $this->groups->getAll();
	}
	
	public function getPermissions(User $user, $level)
	{
		$group_permissions = $user->getUserGroup($level)->getGroupPermissions($level);
		
		$user_permissions = $user->getUserPermissions($level);
		
		return array_merge($group_permissions, $user_permissions);
	}
	
	public function getValidPlayer($userName)
	{
		$player = $this->getServer()->getPlayer($userName);

		return $player instanceof Player ? $player : $this->getServer()->getOfflinePlayer($userName);
	}
	
	public function getUser($userName)
	{
		return new User($this, $this->getValidPlayer($userName));
	}
	
	public function isNegativePerm($node)
	{
		return substr($node, 0, 1) === "-";
	}
		
	public function isValidPerm($node)
	{
		$fixed_perm = $this->getFixedPerm($node);
		
		$permission = $this->getServer()->getPluginManager()->getPermission($fixed_perm);
		
		return $permission instanceof Permission;
	}
	
	public function isValidRegExp($pattern)
	{
		return preg_match($pattern, null) === false;
	}
	
	public function loadGroupsConfig()
	{
		$this->saveResource("groups.yml");
		
		$this->groups = new Config($this->getDataFolder() . "groups.yml", Config::YAML, array(
		));
	}
	
	public function recalculatePermissions()
	{		
		foreach($this->getServer()->getLevels() as $level)
		{
			foreach($this->getServer()->getOnlinePlayers() as $player)
			{
				$this->setPermissions($player, $level->getName());	
			}
		}
	}
	
	public function loadAll()
	{
		@mkdir($this->getDataFolder() . "players/", 0777, true);
		
		$this->config->reloadConfig();
		
		$this->loadGroupsConfig();
		
		$this->fixGroupsData();
		
		$this->recalculatePermissions();
	}
	
	public function removeAttachment(Player $player)
	{
		$player->removeAttachment($this->getAttachment($player));
		
		unset($this->attachments[$player->getName()]);
	}
	
	public function setGroup($player, Group $group, $level)
	{
		$user = $this->getUser($player->getName());
		
		$user->setUserGroup($group, $level);
	}
	
	public function setGroupsData($temp_config)
	{
		if(is_array($temp_config))
		{
			$this->groups->setAll($temp_config);
			
			$this->groups->save();
		}
	}
	
	public function setPermissions(Player $player, $level)
	{
		$attachment = $this->getAttachment($player);
		
		$user = $this->getUser($player->getName());
		
		foreach(array_keys($attachment->getPermissions()) as $old_perm)
		{
			$attachment->unsetPermission($old_perm);
		}
		
		foreach($this->getPermissions($user, $level) as $new_perm)
		{			
			if(!$this->isNegativePerm($new_perm))
			{
				$attachment->setPermission($new_perm, true);
			}
			else
			{
				$fixed_perm = $this->getFixedPerm($new_perm);
				
				$attachment->setPermission($fixed_perm, false);
			}
		}
	}
}