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

    public static function isCreator(string $name) {
        if (in_array($name, ArenaCreator::getInstance()->creators)) {
            return true;
        }
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

        if (self::inGame($levle) and $player->isCreative()) {
            return true;
        }
    }

    public function setData() {

    }



    public static function DataFolder() {
        return SkyWars::getInstance()->getDataFolder();
    }

}