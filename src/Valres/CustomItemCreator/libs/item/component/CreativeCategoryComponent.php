<?php
declare(strict_types=1);

namespace Valres\CustomItemCreator\libs\item\component;

use Valres\CustomItemCreator\libs\item\CreativeInventoryInfo;

final class CreativeCategoryComponent implements ItemComponent {

	private CreativeInventoryInfo $creativeInfo;

	public function __construct(CreativeInventoryInfo $creativeInfo) {
		$this->creativeInfo = $creativeInfo;
	}

	public function getName(): string {
		return "creative_category";
	}

	public function getValue(): int {
		return $this->creativeInfo->getNumericCategory();
	}

	public function isProperty(): bool {
		return true;
	}
}