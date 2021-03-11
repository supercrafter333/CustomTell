<?php

namespace supercrafter333\CustomTell\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use supercrafter333\CustomTell\Loader;

/**
 * Class TellCommand
 * @package supercrafter333\CustomTell\Commands
 */
class TellCommand extends Command
{

    /**
     * TellCommand constructor.
     * @param string $name
     * @param string $description
     * @param string|null $usageMessage
     * @param array $aliases
     */
    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        $this->setPermission("pocketmine.command.tell");
        parent::__construct("tell", Loader::getInstance()->translateString("tell.desc"), Loader::getInstance()->translateString("tell.usage"), ["w", "msg"]);
    }

    /**
     * @param CommandSender $s
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $s, string $commandLabel, array $args): bool
    {
        if (!$s->hasPermission($this->getPermission())) {
            $string = Loader::getInstance()->translateString("tell.noPerm");
            $s->sendMessage($string);
            return true;
        }
        if (count($args) < 2) {
            $string = Loader::getInstance()->translateString("tell.usage");
            $s->sendMessage($string);
            return true;
        }
        $target = $s->getServer()->getPlayer(array_shift($args));
        if ($target === $s) {
            $string = Loader::getInstance()->translateString("tell.sameTarget");
            $s->sendMessage($string);
            return true;
        }
        if (!$target instanceof Player) {
            $string = Loader::getInstance()->translateString("tell.playerNotFound");
            $s->sendMessage($string);
            return true;
        }
        $X = Loader::getInstance()->translateString("tell.senderMessage");
        $X = str_replace(["{sender}"], [$s->getName()], str_replace(["{target}"], [$target->getName()], str_replace(["{msg}"], [implode(" ", $args)], $X)));
        $s->sendMessage($X);
        $name = $s instanceof Player ? $s->getDisplayName() : $s->getName();
        $Y = Loader::getInstance()->translateString("tell.targetMessage");
        $Y = str_replace(["{sender}"], [$name], str_replace(["{target}"], [$target->getName()], str_replace(["{msg}"], [implode(" ", $args)], $Y)));
        $target->sendMessage($Y);
        Loader::getInstance()->setLastSend($s->getName(), $target->getName());
        return true;
    }
}