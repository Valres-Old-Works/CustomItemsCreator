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
use pocketmine\item\Food;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\player\Player;
use Valres\CustomItemCreator\libs\item\component\FoodComponent;
use Valres\CustomItemCreator\libs\item\component\UseAnimationComponent;
use Valres\CustomItemCreator\libs\item\CreativeInventoryInfo as CII;
use Valres\CustomItemCreator\libs\item\ItemComponents;
use Valres\CustomItemCreator\libs\item\ItemComponentsTrait;

final class CustomFood extends Food implements ItemComponents
{
    use ItemComponentsTrait;

    private int $food;
    private float $saturation;

    public function __construct(string $name, string $texture, int $food, float $saturation) {
        parent::__construct(
            new ItemIdentifier(ItemTypeIds::newId()),
            $name
        );

        $this->initComponent($texture, new CII(CII::CATEGORY_EQUIPMENT, CII::GROUP_MISC_FOOD));
        $this->addComponent(new UseAnimationComponent(UseAnimationComponent::ANIMATION_EAT));

        $this->food = $food;
        $this->saturation = $saturation;
    }

    public function getFoodRestore(): int {
        return $this->food;
    }

    public function getSaturationRestore(): float {
        return $this->saturation;
    }

    public function getEffects(): ?array {
        return $this->effects;
    }

    public function requiresHunger(): bool {
        return false;
    }
}