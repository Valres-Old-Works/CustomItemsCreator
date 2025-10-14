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
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use Valres\CustomItemCreator\items\CustomFood;
use Valres\CustomItemCreator\managers\FoodEffectsManager;

final class ItemConsumeListener implements Listener
{
    public function onEvent(PlayerItemConsumeEvent $event): void {
        $player = $event->getPlayer();
        $item   = $event->getItem();

        if (!$item instanceof CustomFood) {
            return;
        }

        $this->addEffects($player, $item);
    }

    public function addEffects(Player $player, Item $customFood): void {
        $identifier  = StringToItemParser::getInstance()->lookupAliases($customFood)[0];
        $foodEffects = FoodEffectsManager::getInstance();

        if (array_key_exists($identifier, $foodEffects->getEffects())) {
            $effects = $foodEffects->getEffects()[$identifier];

            foreach ($effects as $effectData) {
                [$effectName, $time, $amplifier] = explode(":", $effectData);
                $player->getEffects()->add(new EffectInstance(StringToEffectParser::getInstance()->parse($effectName), intval($time * 20), intval($amplifier) - 1));
            }
        }
    }
}