<?php

/**
 *
 *     ______           __                  ______                 ______                __
 *   / ____/_  _______/ /_____  ____ ___  /  _/ /____  ____ ___  / ____/_______  ____ _/ /_____  _____
 *  / /   / / / / ___/ __/ __ \/ __ `__ \ / // __/ _ \/ __ `__ \/ /   / ___/ _ \/ __ `/ __/ __ \/ ___/
 * / /___/ /_/ (__  ) /_/ /_/ / / / / / // // /_/  __/ / / / / / /___/ /  /  __/ /_/ / /_/ /_/ / /
 * \____/\__,_/____/\__/\____/_/ /_/ /_/___/\__/\___/_/ /_/ /_/\____/_/   \___/\__,_/\__/\____/_/
 *
 * ENG: This file is strictly confidential and personal.
 * It contains code developed for private purposes and must not be distributed, shared or used without the explicit permission of the author.
 * Any violation will be subject to legal action.
 * FRA: Ce fichier est strictement confidentiel et personnel.
 * Il contient du code développé à des fins privées et ne doit en aucun cas être distribué, partagé ou utilisé sans autorisation explicite de l'auteur.
 * Toute violation sera passible de poursuites légales.
 *
 * @author ValresMC
 * @version v0.0.1
 */

declare(strict_types=1);

namespace Valres\CustomItemCreator\managers;

use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use Valres\CustomItemCreator\CustomItemCreator;

class FilesManager
{
    use SingletonTrait;

    public function load(): void {
        $files = ["swords.yml", "armors.yml", "tools.yml", "items.yml", "foods.yml"];
        foreach ($files as $file) {
            $this->getPlugin()->saveResource($file);
        }
    }

    public function getArmorData(): Config {
        return new Config($this->getPlugin()->getDataFolder() . "armors.yml", Config::YAML);
    }

    public function getSwordData(): Config {
        return new Config($this->getPlugin()->getDataFolder() . "swords.yml", Config::YAML);
    }

    public function getToolsData(): Config {
        return new Config($this->getPlugin()->getDataFolder() . "tools.yml", Config::YAML);
    }

    public function getItemsData(): Config {
        return new Config($this->getPlugin()->getDataFolder() . "items.yml", Config::YAML);
    }

    public function getFoodsData(): Config {
        return new Config($this->getPlugin()->getDataFolder() . "foods.yml", Config::YAML);
    }

    public function getPlugin(): CustomItemCreator {
        return CustomItemCreator::getInstance();
    }
}