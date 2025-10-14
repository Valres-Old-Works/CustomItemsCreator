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

namespace Valres\CustomItemCreator\listeners;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\StringToEffectParser;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\CallbackInventoryListener;
use pocketmine\inventory\Inventory;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use Valres\CustomItemCreator\managers\ArmorEffectsManager;
use Valres\CustomItemCreator\managers\FilesManager;
use Valres\CustomItemCreator\managers\FoodEffectsManager;

final class ArmorEffectListener implements Listener
{
    public function onEvent(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();

        foreach ($player->getArmorInventory()->getContents() as $targetItem) {
            if (!$targetItem instanceof Armor) {
                if ($targetItem->equals(VanillaItems::AIR())) {
                    $this->addEffects($player, VanillaItems::AIR(), $targetItem);
                }
                return;
            }

            $slot       = $targetItem->getArmorSlot();
            $sourceItem = $player->getArmorInventory()->getItem($slot);
            $this->addEffects($player, $sourceItem, $targetItem);
        }

        $player->getArmorInventory()->getListeners()->add(new CallbackInventoryListener(function(Inventory $inventory, int $slot, Item $oldItem): void {
            if ($inventory instanceof ArmorInventory) {
                $targetItem = $inventory->getItem($slot);
                $this->addEffects($inventory->getHolder(), $oldItem, $targetItem);
            }
        }, null));
    }

    private function addEffects(Player $player, Item $sourceItem, Item $targetItem): void {
        $identifier   = StringToItemParser::getInstance()->lookupAliases($sourceItem)[0];
        $armorEffects = ArmorEffectsManager::getInstance();

        if (array_key_exists($identifier, $armorEffects->getEffects())) {
            $effects = $armorEffects->getEffects()[$identifier];

            foreach ($effects as $effectData) {
                [$effectName, $amplifier] = explode(":", $effectData);
                $player->getEffects()->remove(StringToEffectParser::getInstance()->parse($effectName));
            }
        }

        $identifier_ = StringToItemParser::getInstance()->lookupAliases($targetItem)[0];
        if (array_key_exists($identifier_, $armorEffects->getEffects())) {
            $effects = $armorEffects->getEffects()[$identifier_];

            foreach ($effects as $effectData) {
                [$effectName, $amplifier] = explode(":", $effectData);
                $effect = new EffectInstance(StringToEffectParser::getInstance()->parse($effectName), 2147483647, $amplifier - 1, false);
                $player->getEffects()->add($effect);
            }
        }
    }
}