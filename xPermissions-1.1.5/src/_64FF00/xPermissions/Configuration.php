<?php

namespace _64FF00\xPermissions;

use pocketmine\utils\Config;

class Configuration
{
	private $config;
	
	public function __construct(xPermissions $plugin)
	{
		$this->plugin = $plugin;
		
		$this->loadConfiguration();
	}
	
	public function getChatFormat()
	{
		return $this->config->get("chat-format");
	}
	
	public function getMSGonGroupChange()
	{
		return $this->config->get("message-on-group-change");
	}
	
	public function isFormatterEnabled()
	{
		return $this->config->get("enable-formatter");
	}
	
	/*
	
	public function isOpOverrideEnabled()
	{
		return $this->config->get("override-op-permissions");
	}
	
	*/
	
	public function loadConfiguration()
	{
		if(!(file_exists($this->plugin->getDataFolder() . "config.yml")))
		{
			$this->plugin->saveDefaultConfig();
		}

		$this->config = $this->plugin->getConfig();
		
		if(!$this->config->get("chat-format"))
		{
			$this->config->set("chat-format", "<{PREFIX} {USER_NAME}> {MESSAGE}");
		}
		
		if(!$this->config->get("enable-formatter"))
		{
			$this->config->set("enable-formatter", true);
		}
		
		/*
		
		if(!$this->config->get("override-op-permissions"))
		{
			$this->config->set("override-op-permissions", true);
		}
		
		*/

		if(!$this->config->get("message-on-group-change"))
		{
			$this->config->set("message-on-group-change", "Ta-da! Your group has been changed into... a / an {GROUP}!");
		}
	}
}