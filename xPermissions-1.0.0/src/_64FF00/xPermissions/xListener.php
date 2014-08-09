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

class xListener implements Listener
{
	public function __construct(xPermissions $plugin)
	{
		$this->plugin = $plugin;
	}

	public function onBlockBreak(BlockBreakEvent $event)
	{
		if(!$event->getPlayer()->hasPermission("xperms.build"))
		{
			$event->setCancelled(true);
		}
	}

	public function onBlockPlace(BlockPlaceEvent $event)
	{	
		if(!$event->getPlayer()->hasPermission("xperms.build"))
		{
			$event->setCancelled(true);
		}
	}

	public function onLevelChange(EntityLevelChangeEvent $event)
	{		
		if($event->getEntity() instanceof Player)
		{
			$this->plugin->setPermissions($event->getTarget(), $this->plugin->getUser($event->getEntity()->getName()));
		}
	}
	
	public function onPlayerJoin(PlayerJoinEvent $event)
	{
		$this->plugin->setPermissions($event->getPlayer()->getLevel(), $this->plugin->getUser($event->getPlayer()->getName()));
	}

	public function onPlayerKick(PlayerKickEvent $event)
	{
		$this->plugin->getUser($event->getPlayer()->getName())->removeAttachment();
	}

	public function onPlayerQuit(PlayerQuitEvent $event)
	{
		$this->plugin->getUser($event->getPlayer()->getName())->removeAttachment();
	}
}