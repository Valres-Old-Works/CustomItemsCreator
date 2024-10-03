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

namespace Valres\CustomItemCreator\items;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\Living;
use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\player\Player;
use Valres\CustomItemCreator\libs\item\component\DurabilityComponent;
use Valres\CustomItemCreator\libs\item\component\WearableComponent;
use Valres\CustomItemCreator\libs\item\CreativeInventoryInfo as CII;
use Valres\CustomItemCreator\libs\item\ItemComponents;
use Valres\CustomItemCreator\libs\item\ItemComponentsTrait;

final class CustomArmor extends Armor implements ItemComponents
{
    use ItemComponentsTrait;

    public function __construct(string $name, string $texture, ArmorTypeInfo $info) {
        parent::__construct(
            new ItemIdentifier(ItemTypeIds::newId()),
            $name,
            $info
        );

        $this->initComponent($texture, new CII(CII::CATEGORY_EQUIPMENT, match($info->getArmorSlot()){
            0 => CII::GROUP_HELMET,
            1 => CII::GROUP_CHESTPLATE,
            2 => CII::GROUP_LEGGINGS,
            3 => CII::GROUP_BOOTS
        }));

        $this->addComponent(new DurabilityComponent($info->getMaxDurability()));
        $this->addComponent(new WearableComponent(match($info->getArmorSlot()){
            0 => WearableComponent::SLOT_ARMOR_HEAD,
            1 => WearableComponent::SLOT_ARMOR_CHEST,
            2 => WearableComponent::SLOT_ARMOR_LEGS,
            3 => WearableComponent::SLOT_ARMOR_FEET
        }, $info->getDefensePoints()));
    }
}
