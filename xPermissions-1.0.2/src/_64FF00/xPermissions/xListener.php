<?php

namespace _64FF00\xPermissions;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerQuitEvent;

use pocketmine\Player;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class xListener implements Listener
{
	public function __construct(xPermissions $plugin)
	{
		$this->plugin = $plugin;
	}

	public function onBlockBreak(BlockBreakEvent $event)
	{
		$player = $event->getPlayer();
		
		if(!$player->hasPermission("xperms.build"))
		{
			$player->sendMessage(TextFormat::RED . $this->plugin->getConfiguration()->getMSGonIBuildPerm());
			
			$event->setCancelled(true);
		}
	}

	public function onBlockPlace(BlockPlaceEvent $event)
	{
		$player = $event->getPlayer();
		
		if(!$player->hasPermission("xperms.build"))
		{
			$player->sendMessage(TextFormat::RED . $this->plugin->getConfiguration()->getMSGonIBuildPerm());
			
			$event->setCancelled(true);
		}
	}

	public function onLevelChange(EntityLevelChangeEvent $event)
	{		
		$player = $event->getEntity();
		
		$user = $this->plugin->getUser($player->getName());
		
		$level = $event->getTarget();
		
		if($player instanceof Player)
		{
			$this->plugin->setPermissions($level, $user);
		}
	}
	
	public function onPlayerChat(PlayerChatEvent $event)
	{
		$player = $event->getPlayer();
		
		$user = $this->plugin->getUser($player->getName());
		
		$groupName = $user->getUserGroup($player->getLevel())->getName();
		
		$node = $this->plugin->getConfiguration()->isFormatterEnabled();
		
		if(isset($node) and $node === true)
		{
			$event->setFormat("<[" . $groupName . "] " . $player->getName() . "> " . $event->getMessage());
		}
	}
	
	public function onPlayerJoin(PlayerJoinEvent $event)
	{
		$player = $event->getPlayer();
		
		$user = $this->plugin->getUser($player->getName());
		
		$this->plugin->setPermissions($player->getLevel(), $user);
	}

	public function onPlayerKick(PlayerKickEvent $event)
	{
		$player = $event->getPlayer();
		
		$this->plugin->removeAttachment($player);
	}

	public function onPlayerQuit(PlayerQuitEvent $event)
	{
		$player = $event->getPlayer();
		
		$this->plugin->removeAttachment($player);
	}
}