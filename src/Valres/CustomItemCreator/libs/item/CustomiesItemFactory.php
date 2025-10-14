<?php
declare(strict_types=1);

namespace Valres\CustomItemCreator\libs\item;

use InvalidArgumentException;
use pocketmine\block\Block;
use pocketmine\data\bedrock\item\BlockItemIdMap;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\inventory\CreativeCategory;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\StringToItemParser;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\Utils;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use ReflectionClass;
use function array_values;

final class CustomiesItemFactory {
	use SingletonTrait;

	/**
	 * @var ItemTypeEntry[]
	 */
	private array $itemTableEntries = [];

	/**
	 * Get a custom item from its identifier. An exception will be thrown if the item is not registered.
	 */
	public function get(string $identifier, int $amount = 1): Item {
		$item = StringToItemParser::getInstance()->parse($identifier);
		if($item === null) {
			throw new InvalidArgumentException("Custom item " . $identifier . " is not registered");
		}
		return $item->setCount($amount);
	}

	/**
	 * Returns custom item entries
	 * @return ItemTypeEntry[]
	 */
	public function getItemTableEntries(): array {
		return array_values($this->itemTableEntries);
	}

	/**
	 * Registers the item to the item factory and assigns it an ID. It also updates the required mappings and stores the
	 * item components if present.
	 * @phpstan-param class-string $className
	 */
	public function registerItem(Item $item, string $identifier, ?CreativeInventoryInfo $CII = null): void {
        /** @var Item&ItemComponents $item */
        $item = clone $item;
		$itemId = $item->getTypeId();

		GlobalItemDataHandlers::getDeserializer()->map($identifier, fn() => clone $item);
		GlobalItemDataHandlers::getSerializer()->map($item, fn() => new SavedItemData($identifier));

		StringToItemParser::getInstance()->register(str_replace("minecraft:", "", $identifier), fn() => clone $item);

		$nbt = ($componentBased = $item instanceof ItemComponents) ? $item->getComponents()
			->setInt("id", $itemId)
			->setString("name", $identifier) : CompoundTag::create();

		$this->itemTableEntries[$identifier] = $entry = new ItemTypeEntry($identifier, $itemId, $componentBased, $componentBased ? 1 : 0, new CacheableNbt($nbt));
		$this->registerCustomItemMapping($identifier, $itemId, $entry);

        if ($CII !== null) {
            $existingGroups = [];
            foreach (CreativeInventory::getInstance()->getAllEntries() as $entry) {
                if ($entry->getGroup() != null) {
                    $existingGroups[$entry->getGroup()->getName()->getText()] = $entry->getGroup();
                }
            }

            $category = match ($CII->getCategory()) {
                CreativeInventoryInfo::CATEGORY_NATURE => CreativeCategory::NATURE,
                CreativeInventoryInfo::CATEGORY_CONSTRUCTION => CreativeCategory::CONSTRUCTION,
                CreativeInventoryInfo::CATEGORY_EQUIPMENT => CreativeCategory::EQUIPMENT,
                default => CreativeCategory::ITEMS
            };
            $group = $existingGroups[$CII->getGroup()] ?? null;

            CreativeInventory::getInstance()->add($item, $category, $group);
        }
    }

	/**
	 * Registers a custom item ID to the required mappings in the global ItemTypeDictionary instance.
	 */
	private function registerCustomItemMapping(string $identifier, int $itemId, ItemTypeEntry $entry): void {
		$dictionary = TypeConverter::getInstance()->getItemTypeDictionary();
		$reflection = new ReflectionClass($dictionary);

		$intToString = $reflection->getProperty("intToStringIdMap");
		/** @var int[] $value */
		$value = $intToString->getValue($dictionary);
		$intToString->setValue($dictionary, $value + [$itemId => $identifier]);

		$stringToInt = $reflection->getProperty("stringToIntMap");
		/** @var int[] $value */
		$value = $stringToInt->getValue($dictionary);
		$stringToInt->setValue($dictionary, $value + [$identifier => $itemId]);

		$itemTypes = $reflection->getProperty("itemTypes");
		$value = $itemTypes->getValue($dictionary);
		$value[] = $entry;
		$itemTypes->setValue($dictionary, $value);
	}

	/**
	 * Registers the required mappings for the block to become an item that can be placed etc. It is assigned an ID that
	 * correlates to its block ID.
	 */
	public function registerBlockItem(string $identifier, Block $block): void {
		$itemId = $block->getIdInfo()->getBlockTypeId();
		StringToItemParser::getInstance()->registerBlock($identifier, fn() => clone $block);
		$this->itemTableEntries[] = $entry = new ItemTypeEntry($identifier, $itemId, false, 2, new CacheableNbt(CompoundTag::create()));
		$this->registerCustomItemMapping($identifier, $itemId, $entry);

		$blockItemIdMap = BlockItemIdMap::getInstance();
		$reflection = new ReflectionClass($blockItemIdMap);

		$itemToBlockId = $reflection->getProperty("itemToBlockId");
		/** @var string[] $value */
		$value = $itemToBlockId->getValue($blockItemIdMap);
		$itemToBlockId->setValue($blockItemIdMap, $value + [$identifier => $identifier]);
	}
}
