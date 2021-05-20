<?php


namespace Gsets\EasyItemRename;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EasyItemRename extends Command
{
    /** @var Main */
    private $plugin;

    public function __construct(Main $main)
    {
        parent::__construct("rename");

        $this->setAliases(["rename-item"]);
        $this->setDescription("Rename the item being held in your hand.");
        $this->setPermission("rename.command.use");
        $this->setPermissionMessage(TextFormat::RED . "Unknown command. Try /help to see a list of commands");
        $this->setUsage(TextFormat::YELLOW . "Correct Usage: /rename");

        $this->plugin = $main;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender->hasPermission($this->getPermission())) {
            if ($sender instanceof Player) {
                if (count($args) > 0) {
                    $name = implode(" ", $args);

                    if (strlen($name) > (int)$this->plugin->getConfig()->get("max-name-length")) {
                        $sender->sendMessage(str_replace("{char-len}", (string) $this->plugin->getConfig()->get("max-name-length"), TextFormat::colorize(($this->plugin->getMessages())["message-tolong"])));
                        return;
                    }

                    $item = clone $sender->getInventory()->getItemInHand();

                    $oldname = $item->getName();

                    $item->setCustomName(TextFormat::colorize($name));

                    $sender->getInventory()->setItemInHand($item);

                    $sender->sendMessage(str_replace(["{item-name}", "{new-name}", "{player}"], [$oldname, $item->getCustomName(), $sender->getName()], TextFormat::colorize(($this->plugin->getMessages())["message-success"])));


                } else {
                    $this->plugin->openForm($sender);
                }
            } else {
                $sender->sendMessage("This command can be executed inside the game only!");
            }
        } else {
            $sender->sendMessage($this->getPermissionMessage());
        }
    }
}
