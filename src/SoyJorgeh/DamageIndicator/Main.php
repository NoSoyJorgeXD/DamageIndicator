<?php

namespace SoyJorgeh\DamageIndicator;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TE;

use pocketmine\event\Listener;

class Main extends PluginBase implements Listener {

    public function onEnable(){
        $this->getLogger()->info(TE::GREEN."The plugin was turned on correctly!");

        Entity::registerEntity(SampleEntity::class, true);
    }

    public function onDamage(EntityDamageEvent $event){
       if ($event instanceof EntityDamageByEntityEvent){
           $damager = $event->getDamager();
           $victim = $event->getEntity();

           if ($victim instanceof SampleEntity){
               $event->setCancelled();
               return;
           }

           if ($damager instanceof Player && $victim instanceof Player){

               $motion = new Vector3(lcg_value() * 0.2 - 0.1, 0.5, lcg_value() * 0.2 - 0.1);

               $nbt = Entity::createBaseNBT($victim->add(0, 1, 0), $motion, 0, 0);

               $skinTag = $victim->namedtag->getCompoundTag("Skin");
               assert($skinTag !== null);
               $nbt->setTag(clone $skinTag);

               $sampleEntity = Entity::createEntity("SampleEntity", $victim->getLevelNonNull(), $nbt, $victim);
               if ($sampleEntity !== null){
                   $sampleEntity->getDataPropertyManager()->setFloat(38, 0);
                   $sampleEntity->setNameTag($event->getFinalDamage());
                   $sampleEntity->spawnToAll();

               }

           }
       }
    }
}
