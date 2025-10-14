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

namespace Valres\CustomItemCreator;

use pocketmine\inventory\ArmorInventory;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use Valres\CustomItemCreator\items\CustomArmor;
use Valres\CustomItemCreator\items\CustomAxe;
use Valres\CustomItemCreator\items\CustomFood;
use Valres\CustomItemCreator\items\CustomHoe;
use Valres\CustomItemCreator\items\CustomItem;
use Valres\CustomItemCreator\items\CustomPickaxe;
use Valres\CustomItemCreator\items\CustomShovel;
use Valres\CustomItemCreator\items\CustomSword;
use Valres\CustomItemCreator\libs\item\CreativeInventoryInfo as CII;
use Valres\CustomItemCreator\libs\item\CustomiesItemFactory;
use Valres\CustomItemCreator\listeners\ArmorEffectListener;
use Valres\CustomItemCreator\listeners\CustomiesListener;
use Valres\CustomItemCreator\listeners\ItemConsumeListener;
use Valres\CustomItemCreator\managers\ArmorEffectsManager;
use Valres\CustomItemCreator\managers\FilesManager;
use Valres\CustomItemCreator\managers\FoodEffectsManager;

class CustomItemCreator extends PluginBase
{
    const AXE = "axe";
    const HOE = "hoe";
    const PICKAXE = "pickaxe";
    const SHOVEL = "shovel";
    
    use SingletonTrait;

    protected function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents(new CustomiesListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new ArmorEffectListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new ItemConsumeListener(), $this);
    }

    protected function onLoad(): void {
        self::setInstance($this);

        $filesManager = new FilesManager();
        $filesManager->load();

        foreach ($filesManager->getSwordData()->getAll() as $identifier => $data) {
            CustomiesItemFactory::getInstance()->registerItem(new CustomSword(
                $data["name"],
                $data["texture"],
                $data["damage"],
                $data["durability"]
            ), "minecraft:" . $identifier, new CII(CII::CATEGORY_EQUIPMENT, CII::GROUP_SWORD));
        }


        CustomiesItemFactory::getInstance()->registerItem(new CustomSword("épée lourd", "diamond_sword", 2, 1000), "minecraft:sword_lourd");

        foreach($filesManager->getArmorData()->getAll() as $identifier => $data){
            CustomiesItemFactory::getInstance()->registerItem(new CustomArmor(
                $data["name"],
                $data["texture"],
                new ArmorTypeInfo($data["protection"], $data["durability"], match($data["slot"]){
                    "helmet" => ArmorInventory::SLOT_HEAD,
                    "chestplate" => ArmorInventory::SLOT_CHEST,
                    "leggings" => ArmorInventory::SLOT_LEGS,
                    "boots" => ArmorInventory::SLOT_FEET
                })
            ), "minecraft:" . $identifier, new CII(CII::CATEGORY_EQUIPMENT, match($data["slot"]){
                "helmet" => CII::GROUP_HELMET,
                "chestplate" => CII::GROUP_CHESTPLATE,
                "leggings" => CII::GROUP_LEGGINGS,
                "boots" => CII::GROUP_BOOTS,
            }));

            if(isset($data["effects"])){
                ArmorEffectsManager::getInstance()->register($identifier, $data["effects"]);
            }
        }

        foreach($filesManager->getToolsData()->getAll() as $identifier => $data){
            switch($data["type"]){
                case self::PICKAXE:
                    CustomiesItemFactory::getInstance()->registerItem(new CustomPickaxe(
                        $data["name"],
                        $data["texture"],
                        $data["efficiency"],
                        $data["damage"],
                        $data["durability"]
                    ), "minecraft:" . $identifier, new CII(CII::CATEGORY_EQUIPMENT, CII::GROUP_PICKAXE));
                    break;
                case self::AXE:
                    CustomiesItemFactory::getInstance()->registerItem(new CustomAxe(
                        $data["name"],
                        $data["texture"],
                        $data["efficiency"],
                        $data["damage"],
                        $data["durability"]
                    ), "minecraft:" . $identifier, new CII(CII::CATEGORY_EQUIPMENT, CII::GROUP_AXE));
                    break;
                case self::HOE:
                    CustomiesItemFactory::getInstance()->registerItem(new CustomHoe(
                        $data["name"],
                        $data["texture"],
                        $data["efficiency"],
                        $data["damage"],
                        $data["durability"]
                    ), "minecraft:" . $identifier, new CII(CII::CATEGORY_EQUIPMENT, CII::GROUP_HOE));
                    break;
                case self::SHOVEL:
                    CustomiesItemFactory::getInstance()->registerItem(new CustomShovel(
                        $data["name"],
                        $data["texture"],
                        $data["efficiency"],
                        $data["damage"],
                        $data["durability"]
                    ), "minecraft:" . $identifier, new CII(CII::CATEGORY_EQUIPMENT, CII::GROUP_SHOVEL));
                    break;
            }
        }

        foreach($filesManager->getItemsData()->getAll() as $identifier => $data){
            CustomiesItemFactory::getInstance()->registerItem(new CustomItem(
                $data["name"],
                $data["texture"],
                $data["handEquipped"],
                $data["maxStack"]
            ), "minecraft:" . $identifier, new CII(CII::CATEGORY_ITEMS));
        }

        foreach($filesManager->getFoodsData()->getAll() as $identifier => $data){
            CustomiesItemFactory::getInstance()->registerItem(new CustomFood(
                $data["name"],
                $data["texture"],
                $data["food"],
                $data["saturation"]
            ), "minecraft:" . $identifier, new CII(CII::CATEGORY_NATURE));

            if(isset($data["effects"])){
                FoodEffectsManager::getInstance()->register($identifier, $data["effects"]);
            }
        }
    }
}
