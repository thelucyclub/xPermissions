<?php

namespace _64FF00\xPermissions;

use pocketmine\utils\Config;

class Configuration
{
	private $config;
	
	public function __construct(xPermissions $plugin)
	{
		$this->plugin = $plugin;
		
		$this->loadConfig();
	}
	
	public function fixConfigData()
	{
		if(!$this->config->get("chat-format"))
		{
			$this->config->set("chat-format", "<{PREFIX} {USER_NAME}> {MESSAGE}");
		}
		
		if(!$this->config->get("enable-formatter"))
		{
			$this->config->set("enable-formatter", true);
		}
		
		if(!$this->config->get("override-op-permissions"))
		{
			$this->config->set("override-op-permissions", false);
		}

		if(!$this->config->get("message-on-group-change"))
		{
			$this->config->set("message-on-group-change", "Ta-da! Your group has been changed into... a / an {GROUP}!");
		}
		
		if(!$this->config->get("message-on-insufficient-build-permission"))
		{
			$this->config->set("message-on-insufficient-build-permission", "You don't have permission to build here.");
		}
		
		$this->plugin->saveConfig();
	}
	
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
	
	public function isFormatterEnabled()
	{
		return $this->config->get("enable-formatter");
	}

	public function isOpOverrideEnabled()
	{
		return $this->config->get("override-op-permissions");
	}
	
	public function loadConfig()
	{
		$this->plugin->saveDefaultConfig();
		
		$this->config = $this->plugin->getConfig();
		
		$this->fixConfigData();
	}
	
	public function reloadConfig()
	{	
		$this->plugin->reloadConfig();
		
		$this->loadConfig();
	}
}