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

use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\Pickaxe;
use pocketmine\item\Shovel;
use pocketmine\item\ToolTier;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use Valres\CustomItemCreator\libs\item\component\DamageComponent;
use Valres\CustomItemCreator\libs\item\component\DurabilityComponent;
use Valres\CustomItemCreator\libs\item\component\HandEquippedComponent;
use Valres\CustomItemCreator\libs\item\CreativeInventoryInfo as CII;
use Valres\CustomItemCreator\libs\item\ItemComponents;
use Valres\CustomItemCreator\libs\item\ItemComponentsTrait;
use Valres\CustomItemCreator\utils\DiggerList;

final class CustomShovel extends Shovel implements ItemComponents
{
    use ItemComponentsTrait {
        getComponents as _getComponents;
    }

    protected int $damage;
    protected int $durability;
    protected int $efficiency;

    public function __construct(string $name, string $texture, int $efficiency, int $damage, int $durability) {
        parent::__construct(
            new ItemIdentifier(ItemTypeIds::newId()),
            $name,
            ToolTier::DIAMOND
        );
        $this->efficiency = $efficiency;
        $this->damage = $damage;
        $this->durability = $durability;

        $this->initComponent($texture, new CII(CII::CATEGORY_EQUIPMENT, CII::GROUP_SHOVEL));
        $this->addComponent(new HandEquippedComponent());
        $this->addComponent(new DamageComponent($this->getAttackPoints()));
        $this->addComponent(new DurabilityComponent($this->getMaxDurability()));
    }

    public function getAttackPoints(): int {
        return $this->damage;
    }

    public function getMaxDurability(): int {
        return $this->durability;
    }

    public function getComponents() : CompoundTag {
        $itemData = $this->_getComponents();
        $digger = CompoundTag::create()->setByte("use_efficiency", 1);
        $destroy_speeds = new ListTag();
        foreach(DiggerList::getDiggerList($this->getBlockToolType()) as $block){
            $destroy_speeds->push(CompoundTag::create()
                ->setString("block", $block)
                ->setInt("speed", $this->efficiency)
            );
        }
        return $itemData->setTag("components", $itemData->getCompoundTag("components")->setTag("minecraft:digger", $digger->setTag("destroy_speeds", $destroy_speeds)));
    }
}