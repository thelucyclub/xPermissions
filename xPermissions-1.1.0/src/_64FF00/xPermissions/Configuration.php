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
	
	/*
	
	public function isExFeaturesEnabled()
	{
		return $this->config->get("enable-experimental-features");
	}
	
	*/
	
	public function getChatFormat()
	{
		return $this->config->get("chat-format");
	}
	
	public function getMSGonGroupChange()
	{
		return $this->config->get("message-on-group-change");
	}
	
	public function getMSGonIBuildPerm()
	{
		return $this->config->get("message-on-insufficient-build-permission");
	}
	
	public function getMSGonIPerms()
	{
		return $this->config->get("message-on-insufficient-permissions");
	}
	
	public function isFormatterEnabled()
	{
		return $this->config->get("enable-formatter");
	}
	
	public function loadConfiguration()
	{
		if(!(file_exists($this->plugin->getDataFolder() . "config.yml")))
		{
			$this->plugin->saveDefaultConfig();
		}

		$this->config = $this->plugin->getConfig();
		
		/*
		
		if(!$this->config->get("enable-experimental-features"))
		{
			$this->config->set("enable-experimental-features", true);
		}
		
		*/
		
		if(!$this->config->get("chat-format"))
		{
			$this->config->set("chat-format", "<{PREFIX} {USER_NAME}> {MESSAGE}");
		}
		
		if(!$this->config->get("enable-formatter"))
		{
			$this->config->set("enable-formatter", true);
		}

		if(!$this->config->get("message-on-insufficient-build-permission"))
		{
			$this->config->set("message-on-insufficient-build-permission", "You don't have permission to build here.");
		}

		if(!$this->config->get("message-on-insufficient-permissions"))
		{
			$this->config->set("message-on-insufficient-permissions", "You don't have permission to do that.");
		}

		if(!$this->config->get("message-on-group-change"))
		{
			$this->config->set("message-on-group-change", "Ta-da! Your group has been changed into... a / an {GROUP}!");
		}
	}
}