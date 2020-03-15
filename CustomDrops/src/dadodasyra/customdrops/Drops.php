<?php

namespace dadodasyra\customdrops;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Drops extends PluginBase implements Listener
{
    /** @var array */
    public $list;

    public function onEnable()
    {
        $this->getLogger()->info("§eCustomDrops by §bdadodasyra");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        $this->list = $this->config->get("drops");
    }

    public function onBreak(BlockBreakEvent $event)
    {
        if(!$this->config->get("creative") && $event->getPlayer()->isCreative()) return;
        $array = [];
        if(isset($this->list["{$event->getBlock()->getId()};{$event->getBlock()->getDamage()}"])){
            foreach ($this->list["{$event->getBlock()->getId()};{$event->getBlock()->getDamage()}"] as $b){
                array_push($array, $this->getItem($b));
            }
            $event->setDrops($array);
        }
    }

    /**
     * @param array $item
     * @return Item
     */
    public function getItem(array $item): Item
    {

        if (is_string($item[0])) {
            $data = Item::fromString($item[0]);
            $result = Item::get($data->getId(), $data->getDamage(), 1);
        } else {
            $result = Item::get($item[0], $item[1], 1);
        }

        if (isset($item[1])) {
            $result->setCount($item[1]);
        }

        return $result;
    }

}