<?php

declare(strict_types=1);

namespace Gsets\EasyItemRename;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\utils\TextFormat;

class Main extends PluginBase{

    public function onEnable()
    {
        $this->saveDefaultConfig();

        $this->getServer()->getCommandMap()->register("Rename", new RenameCommand($this));
    }

    public function getMessages () : array
    {
        return $this->getConfig()->get("messages");
    }

    public function openForm (Player $player) : void
    {
        $form = new CustomForm(function (Player $player, array $data = null){
            if ($data === null) return;

            if (strlen($data[0]) > (int)$this->getConfig()->get("max-name-length")) {
                $player->sendMessage(str_replace("{char-len}", (string) $this->getConfig()->get("max-name-length"), TextFormat::colorize(($this->getMessages())["message-success"])));
                return;
            }

            $item = clone $player->getInventory()->getItemInHand();

            $oldname = $item->getName();

            $item->setCustomName(TextFormat::colorize($data[0]));

            $player->getInventory()->setItemInHand($item);

            $player->sendMessage(str_replace(["{item-name}", "{new-name}", "{player}"], [$oldname, $item->getCustomName(), $player->getName()], TextFormat::colorize(($this->getMessages())["message-tolong"])));

        });
        $form->setTitle(TextFormat::colorize(($this->getMessages())["form"]["form-title"]));
        $form->addInput(TextFormat::colorize(($this->getMessages())["form"]["form-content"]), TextFormat::colorize(($this->getMessages())["form"]["form-input"]));
        $player->sendForm($form);
    }

}

   
