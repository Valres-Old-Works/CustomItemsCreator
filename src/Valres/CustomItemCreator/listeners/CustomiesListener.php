<?php
declare(strict_types=1);

namespace Valres\CustomItemCreator\listeners;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\player\Player;
use Valres\CustomItemCreator\libs\block\CustomiesBlockFactory;
use function count;

final class CustomiesListener implements Listener
{
	/** @var BlockPaletteEntry[] */
	private array $cachedBlockPalette = [];

    /** @var array<string,float> */
    private array $cooldowns = [];

    public function onDataPacketSend(DataPacketSendEvent $event): void {
        foreach ($event->getPackets() as $packet) {
            if ($packet instanceof StartGamePacket) {
                if (count($this->cachedBlockPalette) === 0) {
                    $this->cachedBlockPalette = CustomiesBlockFactory::getInstance()->getBlockPaletteEntries();
                }
                $packet->levelSettings->experiments = new Experiments(["data_driven_items" => true,], true);
                $packet->blockPalette = $this->cachedBlockPalette;
            } else if ($packet instanceof ResourcePackStackPacket) {
                $packet->experiments = new Experiments(["data_driven_items" => true,], true);
            }
        }
    }

    /** @priority LOW */
    /*public function onDamageEvent(EntityDamageByEntityEvent $event): void {
        $damager        = $event->getDamager();
        $attackCooldown = $event->getAttackCooldown();

        if (!$damager instanceof Player) return;

        if (isset($this->cooldowns[$damager->getName()]) and $this->cooldowns[$damager->getName()] > microtime(true)) {
            $event->cancel();
            return;
        }

        var_dump($event->getBaseDamage());
        $this->cooldowns[$damager->getName()] = microtime(true) + ($attackCooldown / 20);
    }*/
}
