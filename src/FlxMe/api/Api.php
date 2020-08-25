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

namespace FlxMe\api;


use FlxMe\arena\ArenaCreator;
use FlxMe\SkyWars;
use pocketmine\level\Position;
use pocketmine\level;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;

class Api {

    /** @var array  */
    public static $players = [];
    public static $onHold = [];
    public static $prefix = C::YELLOW . C::BOLD . "Sky" . C::BLUE . "Wars" . C::GOLD . " : ";

    public static function isCreator(string $name) {
        if (in_array($name, ArenaCreator::getInstance()->creators)) {
            return true;
        }
    }

    public static function getAllPlayers(): int {
        return count(self::$players);
    }

    public static function inGame(string $arena) {
        $confg = new Config(self::DataFolder() . "/arenas/" . "{$arena}.yml", Config::YAML);

        if ($confg->get("in-game") !== false){
            return true;
        }
    }

    public static function inCreation(string $arena) {
        $confg = new Config(self::DataFolder() . "/arenas/" . "{$arena}.yml", Config::YAML);

        if ($confg->get("creating") !== false){
            return true;
        }
    }

    public static function inReset(string $arena) {
        $confg = new Config(self::DataFolder() . "/arenas/" . "{$arena}.yml", Config::YAML);

        if ($confg->get("in-reset") !== false){
            return true;
        }
    }

    public static function playerInGame(Player $player) {
        $levle = $player->getLevel()->getFolderName();
        if (!is_file(self::DataFolder() . "/arenas/" . "{$levle}.yml")) {
            return;
        }

        if (self::inGame($levle) and $player->isSurvival()) {
            return true;
        }
    }



    public static function setRandSlot($player, string $level) {
        $name = $player->getName();
        $confg = new Config(self::DataFolder() . "/arenas/" . "{$level}.yml", Config::YAML);
        $slots = $confg->get("slots");
        $confg->save();
        $rand = mt_rand(1, 12);

        if (is_null($slots[$rand])) {
            $slots[$rand] = $name;
            $confg->set("slots", $slots);
            $confg->save();
            return $rand;
        } else {
            self::setRandSlot($player, $level);
        }
    }

    public static function teleportToSpawn(Player $player, string $level, int $num) {
        $confg = new Config(self::DataFolder() . "/arenas/" . "{$level}.yml", Config::YAML);
        $spawns = $confg->get("spawns");
        $confg->save();
        $position = $spawns[$num];

        $arena = Server::getInstance()->getLevelByName($level);
        $arena->loadChunk($arena->getSafeSpawn()->getFloorX(), $arena->getSafeSpawn()->getFloorZ());
        $player->teleport(new Position($position[0], $position[1]+0.1, $position[2], $arena));
    }

    public static function addHold(string $arena) {
        array_push(self::$onHold, $arena);
    }

    public static function removeHold(string $arena) {
        unset(self::$onHold[$arena]);
    }

    public static function getRandomArena(){
        $files = [];
        foreach (glob(self::DataFolder() . "/arenas/" . '*.yml') as $file) {
            array_push($files, strstr($file, ".yml"));
        }

        if (self::getAllPlayers() === 0) {
            if (count($files) === 0) {
                return null;
            } else {
                if (self::inGame($files[array_rand($files)]) or self::isCreating($files[array_rand($files)])) {
                    self::getRandomArena();
                } else {
                    return $files[array_rand($files)];
                }
            }
        } else {
            if (count(self::$onHold) !== 0) {
                if (self::inGame(self::$onHold[array_rand(self::$onHold)]) or self::isCreating(self::$onHold[array_rand(self::$onHold)])) {
                    self::getRandomArena();
                } else {
                    return self::$onHold[array_rand(self::$onHold)];
                }
            } else {
                if (self::inGame($files[array_rand($files)]) or self::isCreating($files[array_rand($files)])) {
                    self::getRandomArena();
                } else {
                    return $files[array_rand($files)];
                }
            }
        }
    }

    public static function joinToArena(Player $player) {
        if (is_null(self::getRandomArena())) {
            $player->sendMessage(self::$prefix . C::RED . "no hay arenas creadas");

        } else {
            $arena = self::getRandomArena();
            $slot = self::setRandSlot($player, $arena);
            self::teleportToSpawn($player, $arena, $slot);
            array_push(self::$players, $player->getName());
            foreach (Server::getInstance()->getLevelByName($arena)->getPlayers() as $users) {
                $users->sendMessage(self::$prefix . C::GRAY . "{$player->getName()} sea unido");
            }
        }
    }

    public function disconectPlayer(Player $player) {
        $level = $player->getLevel()->getFolderName();
        $confg = new Config(self::DataFolder() . "/arenas/" . "{$level}.yml", Config::YAML);
        $slots = $confg->get("slots");

        if (in_array($player->getName(),  self::$players)) {
            unset(self::$players[$player->getName()]);
        }
        for ($i = 1; $i < 12; $i++) {
            if ($slots[$i] === $player->getName()) {
                $slots[$i] = null;
                $confg->set("slots", $slots);
                $confg->save();
            }
        }
        foreach (Server::getInstance()->getLevelByName($level)->getPlayers() as $users) {
            $users->sendMessage(self::$prefix . C::DARK_GRAY . "{$player->getName()} a salido");
        }
    }

    public static function isCreating(string $arena) {
        $confg = new Config(self::DataFolder() . "/arenas/" . "{$arena}.yml", Config::YAML);

        if ($confg->get("creating") !== false){
            return true;
        }
    }

    public static function DataFolder() {
        return SkyWars::getInstance()->getDataFolder();
    }

}