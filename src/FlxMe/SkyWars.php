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

namespace FlxMe;


use FlxMe\command\SWCommand;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class SkyWars extends PluginBase implements Listener {

    public static $instance;
    public $arenas;
    public $ids = [];

    public function onEnable() {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        Server::getInstance()->getLogger()->info(" created by FlxMeIdk");
        $this->getServer()->getCommandMap()->register("sw", new SWCommand("sw", $this));
        $this->init();
    }

    public static function getInstance(): SkyWars {
        return self::$instance;
    }

    public function init(): void {
        if (!is_dir($this->getDataFolder())) {
            @mkdir($this->getDataFolder());
        }
        if (!is_dir($this->getDataFolder() . "/arenas/")) {
            @mkdir($this->getDataFolder() ."/arenas/");
        }
        if (!is_dir($this->getDataFolder() . "/players/")) {
            @mkdir($this->getDataFolder() ."/players/");
        }
    }

}