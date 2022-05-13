<?php

namespace supercrafter333\CustomTell\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use supercrafter333\CustomTell\Loader;

/**
 * Class ReplyCommand
 * @package supercrafter333\CustomTell\Commands
 */
class ReplyCommand extends Command
{

    /**
     * ReplyCommand constructor.
     * @param string $name
     * @param string $description
     * @param string|null $usageMessage
     * @param array $aliases
     */
    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        $this->setPermission("CustomTell.reply");
        parent::__construct("reply", Loader::getInstance()->translateString("reply.desc"), Loader::getInstance()->translateString("reply.usage"), ["r", "antworten"]);
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
        if (!isset($args)) {
            $string = Loader::getInstance()->translateString("reply.usage");
            $s->sendMessage($string);
            return true;
        }
        if (Loader::getInstance()->getLastSend($s->getName()) == null) {
            $string = Loader::getInstance()->translateString("reply.empty");
            $s->sendMessage($string);
            return true;
        }
        $target = $s->getServer()->getPlayerExact(Loader::getInstance()->getLastSend($s->getName()));
        if (!$target instanceof CommandSender) {
            $string = Loader::getInstance()->translateString("reply.playerNotFound");
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
        if (Loader::getInstance()->useConsole() == true) {
            $Z = Loader::getInstance()->translateString("log.Message");
            $Z = str_replace(["{sender}"], [$name], str_replace(["{target}"], [$target->getName()], str_replace(["{msg}"], [implode(" ", $args)], $Y)));
            Loader::getInstance()->getLogger()->info($Z);
        }
        Loader::getInstance()->setLastSend($s->getName(), $target->getName());
        return true;
    }
}