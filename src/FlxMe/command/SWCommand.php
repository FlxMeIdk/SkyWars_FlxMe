<?php

/**
 * Copyright 2020-2022 FlxMeIdk
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace FlxMe\command;



use FlxMe\arena\ArenaCreator;
use FlxMe\SkyWars;
use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as C;

class SWCommand extends PluginCommand{

    public $owner;

    public function __construct(string $name, SkyWars $owner) {
        parent::__construct($name, $owner);
        $this->owner = $owner;
        $this->setDescription("Minigame SkyWars .FlxMeIdk");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        $prefix = C::YELLOW . C::BOLD . "Sky" . C::BLUE . "Wars" . C::GOLD . " : ";
        if (!$sender instanceof Player) {
            Server::getInstance()->getLogger()->info($prefix . C::RED . "usa el command en el juego");
            return;
        }
        if ($sender->hasPermission("sw.cmd")) {
            $sender->sendMessage($prefix . C::RED . "no tienes premiso del comando");
            return;

        } elseif (!isset($args[0])) {
            $sender->sendMessage($prefix . C::YELLOW . "usa /sw help");
            return;
        }

        switch ($args[0]) {
            case "create":
                if (!$sender->hasPermission("sw.cmd.create")) {
                    $sender->sendMessage($prefix . C::RED . "no tienes premiso del comando");
                    break;
                }
                if (!isset($args[1])){
                    $sender->sendMessage($prefix . C::YELLOW . "usa /sw create (arena)");
                    break;
                }
                if (!Server::getInstance()->getLevelByName($args[1])) {
                    $sender->sendMessage($prefix . C::RED . "no existe la arena {$args[1]}");
                    break;
                }
                if (isset($this->owner->arenas[$args[1]])) {
                    $sender->sendMessage($prefix . C::RED . "ya existe la arena {$args[1]}");
                    break;
                }
                $level = $args[1];
                $arenacreate = new ArenaCreator($sender, $level);
                $arenacreate->init();
                $arena = Server::getInstance()->getLevelByName($level);
                $arena->loadChunk($arena->getSafeSpawn()->getFloorX(), $arena->getSafeSpawn()->getFloorZ());
                $sender->teleport($arena);
                break;
            case "1":
                $sender->setGamemode(1);
                break;
            case "0":
                $sender->setGamemode(0);
                break;
            case "2":
                $sender->setGamemode(2);
                break;
            case "3":
                $sender->setGamemode(3);
                break;
        }
    }

}