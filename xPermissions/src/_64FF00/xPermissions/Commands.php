<?php

namespace _64FF00\xPermissions;

use _64FF00\xPermissions\data\Group;
use _64FF00\xPermissions\data\User;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;

use pocketmine\Player;

use pocketmine\utils\TextFormat;

class Commands implements CommandExecutor
{
	public function __construct(xPermissions $plugin)
	{
		$this->plugin = $plugin;
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args)
	{
		$output = "";
		
		if(!isset($args[0]))
		{
			if(!$this->checkPermission($sender, "xperms.help")) return true;

			$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Usage: /xperms <group / help / info / perms / reload / user>");
				
			return true;
		}

		switch($args[0])
		{
			case "gr":
			case "group":
			
				if(!$this->checkPermission($sender, "xperms.group.help")) break;
				
				if(!isset($args[1]))
				{
					$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Usage: /xperms group <create / list / remove / setperm / unsetperm>");

					break;
				}
						
				switch($args[1])
				{
					case "cr":
					case "create":
						
						if(!$this->checkPermission($sender, "xperms.group.create")) break;
						
						if(!isset($args[2]) || count($args) > 3)
						{
							$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Usage: /xperms group create <GROUP_NAME>");

							break;
						}
						
						if($this->plugin->getGroup($args[2]) != null)
						{
							$sender->sendMessage(TextFormat::RED . "[xPermissions] [ERROR] Group " . $args[2] . " already exists.");
							
							break;
						}
						
						$this->plugin->createGroup($args[2]);
						
						$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Added " . $args[2] . " to the group list successfully.");
						
						break;
						
					case "ls":
					case "list":
							
						if(!$this->checkPermission($sender, "xperms.group.list")) break;
							
						foreach($this->plugin->getAllGroups() as $group)
						{
							$output .= $group->getName() . ", ";
						}
								
						$sender->sendMessage(TextFormat::GREEN . "[xPermissions] List of all groups: " . substr($output, 0, -2));
								
						break;
						
					case "rm":
					case "remove":
						
						if(!$this->checkPermission($sender, "xperms.group.remove")) break;
						
						if(!isset($args[2]) || count($args) > 3)
						{
							$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Usage: /xperms group remove <GROUP_NAME>");

							break;
						}
						
						if($this->plugin->getGroup($args[2]) == null)
						{
							$sender->sendMessage(TextFormat::RED . "[xPermissions] [ERROR] Group " . $args[2] . " doesn't exist.");
							
							break;
						}
						
						$this->plugin->removeGroup($args[2]);
						
						$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Removed " . $args[2] . " from the group list successfully.");
						
						break;
					
					case "sp":
					case "setperm":
							
						if(!$this->checkPermission($sender, "xperms.group.setperm")) break;
							
						if(!isset($args[2]) || !isset($args[3]) || count($args) > 5)
						{
							$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Usage: /xperms group setperm <GROUP_NAME> <PERMISSION> [LEVEL_NAME]");

							break;
						}
								
						$group = $this->plugin->getGroup($args[2]);
								
						if(!isset($group))
						{
							$sender->sendMessage(TextFormat::RED . "[xPermissions] [ERROR] Group " . $args[2] . " doesn't exist.");
									
							break;
						}
								
						$permission = strtolower($args[3]);
						
						if(!$this->plugin->isValidPerm($permission))
						{
							$sender->sendMessage(TextFormat::RED . "[xPermissions] [ERROR] Permission node " . $args[3] . " doesn't exist.");
											
							break;
						}
								
						$level = isset($args[4]) ? $this->plugin->getServer()->getLevelByName($args[4]) : $this->plugin->getServer()->getDefaultLevel();
							
						if(!isset($level))
						{
							$sender->sendMessage(TextFormat::RED . "[xPermissions] [ERROR] Level " . $args[4] . " doesn't exist.");
											
							break;
						}
								
						$group->addGroupPermission($permission, $level->getName());
								
						$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Added the permission to the group successfully.");
							
						break;
					
					case "usp":			
					case "unsetperm":
							
						if(!$this->checkPermission($sender, "xperms.group.unsetperm")) break;
							
						if(!isset($args[2]) || !isset($args[3]) || count($args) > 5)
						{
							$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Usage: /xperms group unsetperm <GROUP_NAME> <PERMISSION> [LEVEL_NAME]");

							break;
						}
								
						$group = $this->plugin->getGroup($args[2]);
						
						if(!isset($group))
						{
							$sender->sendMessage(TextFormat::RED . "[xPermissions] [ERROR] Group " . $args[2] . " doesn't exist.");
									
							break;
						}
								
						$permission = strtolower($args[3]);
						
						if(!$this->plugin->isValidPerm($permission))
						{
							$sender->sendMessage(TextFormat::RED . "[xPermissions] [ERROR] Permission node " . $args[3] . " doesn't exist.");
											
							break;
						}
								
						$level = isset($args[4]) ? $this->plugin->getServer()->getLevelByName($args[4]) : $this->plugin->getServer()->getDefaultLevel();
							
						if(!isset($level))
						{
							$sender->sendMessage(TextFormat::RED . "[xPermissions] [ERROR] Level " . $args[4] . " doesn't exist.");
											
							break;
						}
								
						$group->removeGroupPermission($permission, $level->getName());
								
						$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Removed the permission from the group successfully.");
								
						break;
								
					default:
						
						$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Usage: /xperms group <create / list / remove / setperm / unsetperm>");
								
						break;					
				}
				
				break;
			
			case "if":
			case "info":
			
				if(!$this->checkPermission($sender, "xperms.info")) break;
				
				$sender->sendMessage(TextFormat::GREEN . "[xPermissions] <-- xPermissions v" . $this->plugin->getDescription()->getVersion() . " by " . $this->plugin->getDescription()->getAuthors()[0] . "! -->");

				break;	
				
			case "ps":
			case "perms":
				
				if(!$this->checkPermission($sender, "xperms.perms")) break;
				
				$perms = $this->plugin->getAllPermissions();
				
				// TODO
				
				break;
			
			case "rl":
			case "reload":
			
				if(!$this->checkPermission($sender, "xperms.reload")) break;
				
				$this->plugin->loadAll();
				
				$sender->sendMessage(TextFormat::GREEN . "[xPermissions] All config files were successfully reloaded!");
		
				break;
			
			case "us":
			case "user":
			
				if(!$this->checkPermission($sender, "xperms.user.help")) break;
			
				if(!isset($args[1]))
				{
					$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Usage: /xperms user <info / setgroup / setperm / unsetperm>");

					return true;
				}
						
				switch($args[1])
				{
					case "if":
					case "info":
							
						if(!$this->checkPermission($sender, "xperms.user.info")) break;
								
						if(!isset($args[2]) || count($args) > 4)
						{
							$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Usage: /xperms user info <USER_NAME> [LEVEL_NAME]");

							break;
						}
								
						$player = $this->plugin->getValidPlayer($args[2]);

						$level = isset($args[4]) ? $this->plugin->getServer()->getLevelByName($args[4]) : $this->plugin->getServer()->getDefaultLevel();
							
						if(!isset($level))
						{
							$sender->sendMessage(TextFormat::RED . "[xPermissions] [ERROR] Level " . $args[4] . " doesn't exist.");
											
							break;
						}
						
						$user = $this->plugin->getUser($player->getName());
								
						$status = $player instanceof Player ? "ONLINE" : "OFFLINE";
								
						$sender->sendMessage(TextFormat::GREEN . "[xPermissions] ----- Information for " . $player->getName() . " -----");		
						$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Username: " . $player->getName() . " [" . $status . "]");					
						$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Group: " . $user->getUserGroup($level->getName())->getName());
								
						foreach($user->getUserPermissions($level->getName()) as $permission)
						{
							$output .= TextFormat::GREEN . "[xPermissions] - " . $permission . "\n";
						}
								
						if(!$output == "")
						{
							$sender->sendMessage(TextFormat::GREEN . "[xPermissions] User Permissions: \n" . $output);
						}
								
						break;
					
					case "sg":			
					case "setgroup":
							
						if(!$this->checkPermission($sender, "xperms.user.setgroup")) break;
								
						if(!isset($args[2]) || !isset($args[3]) || count($args) > 5)
						{
							$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Usage: /xperms user setgroup <USER_NAME> <GROUP_NAME> [LEVEL_NAME]");

							break;
						}
								
						$player = $this->plugin->getValidPlayer($args[2]);
								
						$group = $this->plugin->getGroup($args[3]);
								
						if(!isset($group))
						{
							$sender->sendMessage(TextFormat::RED . "[xPermissions] [ERROR] Group " . $args[3] . " doesn't exist.");
									
							break;
						}
								
						$level = isset($args[4]) ? $this->plugin->getServer()->getLevelByName($args[4]) : $this->plugin->getServer()->getDefaultLevel();
							
						if(!isset($level))
						{
							$sender->sendMessage(TextFormat::RED . "[xPermissions] [ERROR] Level " . $args[4] . " doesn't exist.");
											
							break;
						}
								
						$this->plugin->setGroup($player, $group, $level->getName());
						
						if($player instanceof Player)
						{
							$this->plugin->setPermissions($player, $level->getName());
						}
								
						$message = str_replace("{GROUP}", strtolower($group->getName()), $this->plugin->getConfiguration()->getMSGonGroupChange());
										
						$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Added " . $player->getName() . " to the group successfully.");
										
						if($player instanceof Player)
						{
							$player->sendMessage(TextFormat::GREEN . "[xPermissions] " . $message);
						}	
									
						break;
					
					case "sp":			
					case "setperm":
							
						if(!$this->checkPermission($sender, "xperms.user.setperm")) break;
								
						if(!isset($args[2]) || !isset($args[3]) || count($args) > 5)
						{
							$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Usage: /xperms user setperm <USER_NAME> <PERMISSION> [LEVEL_NAME]");

							break;
						}
								
						$player = $this->plugin->getValidPlayer($args[2]);
								
						$permission = strtolower($args[3]);
						
						if(!$this->plugin->isValidPerm($permission))
						{
							$sender->sendMessage(TextFormat::RED . "[xPermissions] [ERROR] Permission node " . $args[3] . " doesn't exist.");
											
							break;
						}
								
						$level = isset($args[4]) ? $this->plugin->getServer()->getLevelByName($args[4]) : $this->plugin->getServer()->getDefaultLevel();
							
						if(!isset($level))
						{
							$sender->sendMessage(TextFormat::RED . "[xPermissions] [ERROR] Level " . $args[4] . " doesn't exist.");
											
							break;
						}
						
						$user = $this->plugin->getUser($player->getName());
								
						$user->addUserPermission($permission, $level);
						
						if($player instanceof Player)
						{
							$this->plugin->setPermissions($player, $level->getName());
						}
								
						$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Added the permission to " . $player->getName() . " successfully.");
							
						break;
					
					case "usp":
					case "unsetperm":
							
						if(!$this->checkPermission($sender, "xperms.user.unsetperm")) break;
								
						if(!isset($args[2]) || !isset($args[3]) || count($args) > 5)
						{
							$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Usage: /xperms user unsetperm <USER_NAME> <PERMISSION> [LEVEL_NAME]");

							break;
						}
								
						$player = $this->plugin->getValidPlayer($args[2]);
								
						$permission = strtolower($args[3]);
						
						if(!$this->plugin->isValidPerm($permission))
						{
							$sender->sendMessage(TextFormat::RED . "[xPermissions] [ERROR] Permission node " . $args[3] . " doesn't exist.");
											
							break;
						}
								
						$level = isset($args[4]) ? $this->plugin->getServer()->getLevelByName($args[4]) : $this->plugin->getServer()->getDefaultLevel();
							
						if(!isset($level))
						{
							$sender->sendMessage(TextFormat::RED . "[xPermissions] [ERROR] Level " . $args[4] . " doesn't exist.");
											
							break;
						}
						
						$user = $this->plugin->getUser($player->getName());
								
						$user->removeUserPermission($permission, $level->getName());
						
						if($player instanceof Player)
						{
							$this->plugin->setPermissions($player, $level->getName());
						}
								
						$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Removed the permission from " . $player->getName() . " successfully.");
								
						break;
								
					default:
							
						$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Usage: /xperms user <info / setgroup / setperm / unsetperm>");
								
						break;					
				}
				
				break;
				
			default:
				
				if(!$this->checkPermission($sender, "xperms.help")) break;

				$sender->sendMessage(TextFormat::GREEN . "[xPermissions] Usage: /xperms <group / help / info / reload / user>");
				
				break;
		}

		return true;
	}
	
	private function checkPermission(CommandSender $sender, $permission)
	{
		if(!$sender->hasPermission($permission))
		{
			$sender->sendMessage(TextFormat::RED . "You don't have permission to do that.");

			return false;
		}

		return true;
	}
}