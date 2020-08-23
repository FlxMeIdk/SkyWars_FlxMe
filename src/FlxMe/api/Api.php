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
use pocketmine\Player;
use pocketmine\utils\Config;

class Api {

    /** @var array  */
    public static $players = [];

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

    public function getPlayerSpawn(Player $player) {

    }

    public static function getPlayerSlot(Player $player) {
        $level = $player->getLevel()->getFolderName();
        $confg = new Config(self::DataFolder() . "/arenas/" . "{$level}.yml", Config::YAML);

        $slots = $confg->get("slots");
        for ($i = 1; $i < 12; $i++) {
            if ($slots[$i] === $player->getName()) {
               return $i;
            }
        }
    }

    public function disconectPlayer(Player $player) {
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