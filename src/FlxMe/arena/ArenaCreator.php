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

namespace FlxMe\arena;


use FlxMe\SkyWars;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;

class ArenaCreator {

    /** @var Player */
    public $sender;
    /** @var string */
    public $arena;
    /** @var array */
    public $creators = [];
    public static $instance;
    public $clock = 1;

    public function __construct(Player $sender, ?string $arena) {
        $this->sender = $sender;
        $this->arena = $arena;
        array_push($this->creators, $sender->getName());
    }

    public static function getInstance(): self {
        if (!self::$instance instanceof Self){
            self::$instance = new Self();
        }
        return self::$instance;
    }

    public function init() {
        if (!is_file($this->getDataFolder() . "/arenas/" . "{$this->arena}.yml")) {
            $this->basicConfig();
        }
    }

    public function basicConfig() {
        $config = new Config($this->getDataFolder() . "/arenas/" . "{$this->arena}.yml", Config::YAML, [
            "slots" => [],
            "spawns" => [],
            "enable" => false,
            "in-game" => false,
            "creating" => true,
            "in-reset" => false
        ]);
        $spawns = [];
        for ($i = 1; $i <= 12; $i++) {
            $spawn[] = $i;
        }
        $config->set("spawns", array_fill_keys($spawns, [
            'x' => null,
            'y' => null,
            'z' => null
        ]));
        $config->set("slots", array_fill_keys($spawns, null));
        $config->save();
    }

    public function saveSpawn(Player $sender, $level, array $position) {
        $prefix = C::YELLOW . C::BOLD . "Sky" . C::BLUE . "Wars" . C::GOLD . " : ";
        if (!is_file($this->getDataFolder() . "/arenas/" . "{$level}.yml")) {
            return;
        }

        $config = new Config($this->getDataFolder() . "/arenas/" . "{$level}.yml", Config::YAML);
        if ($config->get("creating") === false)  {
            return;
        } else {
            if ($this->clock === 13){
                $sender->sendMessage($prefix . C::GREEN . " spawns guardados");
                sleep(2);
                $sender->sendMessage($prefix . C::GREEN . " usa /sw save (arena)");
                return;
            }

            $spawns = $config->get("sapwns");
            $spawns[$this->clock] = [
                'x' => $position[0],
                'y' => $position[1],
                'z' => $position[2]
            ];

            $config->set("spawns", $spawns);
            $config->save();
            $sender->sendMessage($prefix . C::GREEN . " spawn " . C::DARK_GREEN . "{$this->clock}" . C::GREEN  . "registrado");
            $this->clock++;
        }
    }

    public function saveArena(Player $sender, string $arena) {
        $prefix = C::YELLOW . C::BOLD . "Sky" . C::BLUE . "Wars" . C::GOLD . " : ";
        if (!in_array($sender->getName(), $this->creators)){
            return;
        }
        if (!is_file($this->getDataFolder() . "/arenas/" . "{$arena}.yml")) {
            $sender->sendMessage($prefix . C::RED . "no esta creada la arena " . C::DARK_GREEN . "{$arena}");
            return;
        }

        $config = new Config($this->getDataFolder() . "/arenas/" . "{$arena}.yml", Config::YAML);
        if ($config->get("enable") !== false){
            $sender->sendMessage($prefix . C::RED . "ya esta habilitada la arena " . C::DARK_GREEN . "{$arena}");
            return;
        }

        $config->set("enable", true);
        $config->set("creating", false);
        $config->save();
        $sender->sendMessage($prefix . C::GREEN . "sea habilitado la arena " . C::DARK_GREEN . "{$arena}");
        $this->clock = 1;
        unset($this->creators[$sender->getName()]);
    }

    public function getDataFolder() {
        return SkyWars::getInstance()->getDataFolder();
    }

}