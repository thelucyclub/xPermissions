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
		
		$level = $event->getTarget()->getName();
		
		if($player instanceof Player)
		{
			$this->plugin->setPermissions($player, $level);
		}
	}
	
	public function onPlayerChat(PlayerChatEvent $event)
	{
		$player = $event->getPlayer();
		
		$format = $this->plugin->getFormattedMessage($player, $event->getMessage());
		
		$config_node = $this->plugin->getConfiguration()->isFormatterEnabled();
		
		if(isset($config_node) and $config_node === true)
		{
			$event->setFormat($format);
		}
	}
	
	public function onPlayerJoin(PlayerJoinEvent $event)
	{
		$player = $event->getPlayer();
		
		$level = $player->getLevel()->getName();
		
		$this->plugin->removeAttachment($player);

		if(!$player->isOp() and !$this->plugin->getConfiguration()->isOpOverrideEnabled())
		{
			$this->plugin->setPermissions($player, $level);
		}
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