<?php
declare(strict_types=1);

namespace Valres\CustomItemCreator\libs\item\component;

final class RenderOffsetsComponent implements ItemComponent {

    # Thanks to https://github.com/dadodasyra to the class.
    private int $textureSize;

    public function __construct(int $textureSize) {
        $this->textureSize = $textureSize;
    }

    public function getName(): string {
        return "minecraft:render_offsets";
    }

    public function getValue(): array {
        $textureSize = $this->textureSize;
        $mainHand_fp = round(0.039 * 16 / $textureSize, 8);
        $offhand_fp = round(0.065 * 16 / $textureSize, 8);
        $mainHand_tp = $offhand_tp = round(0.0965 * 16 / $textureSize, 8);

        return [
            "main_hand" => [
                "first_person" => [
                    "scale" => [$mainHand_fp, $mainHand_fp, $mainHand_fp],
                ],
                "third_person" => [
                    "scale" => [$mainHand_tp, $mainHand_tp, $mainHand_tp]
                ]
            ],
            "off_hand" => [
                "first_person" => [
                    "scale" => [$offhand_fp, $offhand_fp, $offhand_fp],
                ],
                "third_person" => [
                    "scale" => [$offhand_tp, $offhand_tp, $offhand_tp]
                ]
            ]
        ];
    }

    public function isProperty(): bool {
        return false;
    }
}