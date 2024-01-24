<?php

declare(strict_types=1);

namespace Fred;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\ItemBlock;
use pocketmine\block\BlockTypeIds;
use pocketmine\tile\Tile;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\player\GameMode;

class ShiftPick extends PluginBase implements Listener {

    public function onEnable() : void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();

        if (!$player->isSneaking() || $player->getGamemode() !== GameMode::CREATIVE) {
            return;
        }

        $blockClicked = $event->getBlock();
        if ($blockClicked->getTypeId() === BlockTypeIds::AIR) {
            return;
        }

        $pickedItem = new ItemBlock($blockClicked);
        
        $pos = $blockClicked->getPosition();
        $tile = $pos->getWorld()->getTile($pos);
        if ($tile instanceof Tile) {
            $nbt = $tile->getCleanedNBT();
            if ($nbt instanceof CompoundTag) {
                $pickedItem->setCustomBlockData($nbt);
            }
        }
        
        $player->getInventory()->addItem($pickedItem);

        $event->cancel();

        $player->sendTip("[^] You picked up a " . $blockClicked->getName());
    }
}
