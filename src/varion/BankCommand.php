<?php

namespace varion;

use onebone\economyapi\EconomyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class BankCommand extends Command {

    public function __construct(Main $plugin)
    {
        parent::__construct("bank", "Bank Command", "/bank <add | remove | show | see> [money or player]");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player) {
            if(isset($args[0])) {
                if($args[0] ===  "show") {
                    $msg = $this->plugin->cfg->getNested("messages.show");
                    $msg = str_replace("{money}", $this->plugin->bank->get(strtolower($sender->getName())), $msg);
                    $sender->sendMessage($msg);
                } else if($args[0] === "see") {
                    if(isset($args[1])) {
                        if($this->plugin->bank->exists(strtolower($args[1]))) {
                            $msg = $this->plugin->cfg->getNested("messages.see");
                            $msg = str_replace("{player}", strtolower($args[1]), $msg);
                            $msg = str_replace("{money}", $this->plugin->bank->get(strtolower($args[0])), $msg);
                            $sender->sendMessage($msg);
                        } else {
                            $sender->sendMessage($this->plugin->cfg->getNested("messages.not-found"));
                        }
                    } else {
                        $sender->sendMessage($this->getUsage());
                    }
                } else if($args[0] === "add") {
                    if(isset($args[1])) {
                        if(is_numeric($args[1])) {
                            if((EconomyAPI::getInstance()->myMoney($sender) - $args[1]) > -1) {
                                EconomyAPI::getInstance()->reduceMoney($sender, $args[1]);
                                $this->plugin->bank->set(strtolower($sender->getName()), $this->plugin->bank->get(strtolower($sender->getName())) + $args[1]);
                                $this->plugin->bank->save();
                                $msg = $this->plugin->cfg->getNested("messages.add");
                                $msg = str_replace("{money}", $args[1], $msg);
                                $sender->sendMessage($msg);
                            } else {
                                $sender->sendMessage($this->plugin->cfg->getNested("messages.little-money"));
                            }
                        } else {
                            $sender->sendMessage($this->plugin->cfg->getNested("messages.not-numeric"));
                        }
                    } else {
                        $sender->sendMessage($this->getUsage());
                    }
                } else if($args[0] === "remove") {
                    if(isset($args[1])) {
                        if(is_numeric($args[1])) {
                            if(($this->plugin->bank->get(strtolower($sender->getName())) - $args[1]) > -1) {
                                EconomyAPI::getInstance()->addMoney($sender, $args[1]);
                                $this->plugin->bank->set(strtolower($sender->getName()), $this->plugin->bank->get(strtolower($sender->getName())) - $args[1]);
                                $this->plugin->bank->save();
                                $msg = $this->plugin->cfg->getNested("messages.remove");
                                $msg = str_replace("{money}", $args[1], $msg);
                                $sender->sendMessage($msg);
                            } else {
                                $sender->sendMessage($this->plugin->cfg->getNested("messages.little-bank-money"));
                            }
                        } else {
                            $sender->sendMessage($this->plugin->cfg->getNested("messages.not-numeric"));
                        }
                    } else {
                        $sender->sendMessage($this->getUsage());
                    }
                }
            } else {
                $sender->sendMessage($this->getUsage());
            }
        } else {
            $sender->sendMessage("Run this command InGame!");
        }
    }
}