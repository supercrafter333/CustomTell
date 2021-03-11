<?php

namespace supercrafter333\CustomTell;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use supercrafter333\CustomTell\Commands\ReplyCommand;

/**
 * Class Loader
 * @package supercrafter333\CustomTell
 */
class Loader extends PluginBase
{

    /**
     * @var
     */
    protected $lastSend;
    /**
     * @var
     */
    protected $baseConfig;

    /**
     * @var
     */
    protected static $instance;

    /**
     *
     */
    public function onEnable()
    {
        self::$instance = $this;
        $this->saveResource("config.yml");
        $this->loadLanguages();
        $cmdMap = $this->getServer()->getCommandMap();
        $PMMPTellCmd = $cmdMap->getCommand("tell");
        $cmdMap->unregister($PMMPTellCmd);
        $cmdMap->register("CustomTell", new \supercrafter333\CustomTell\Commands\TellCommand("tell"));
        $cmdMap->register("CustomTell", new ReplyCommand("reply"));
        $this->baseConfig = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    }

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        return self::$instance;
    }

    /**
     * @return Config
     */
    public function getBaseConfig(): Config
    {
        return $this->baseConfig;
    }

    /**
     * @param string $sender
     * @param string $target
     */
    public function setLastSend(string $sender, string $target)
    {
        $this->lastSend[$target] = $sender;
    }

    /**
     * @return mixed
     */
    public function getLastSend(string $name)
    {
        if (isset($this->lastSend[$name])) {
            return $this->lastSend[$name];
        } else {
            return null;
        }
    }

    /**
     * @param string $string
     * @return string
     */
    public function translateString(string $string): string
    {
        $baseCfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $language = $baseCfg->get("language");
        if ($language === "deu" || $language === "eng") {
            $useLang = new Config($this->getDataFolder() . "lang/" . $language . ".yml", Config::YAML);
            return $useLang->get($string);
        }
        return "Error";
    }

    /**
     * @return string[]
     */
    public function getLangs(): array
    {
        return ["eng", "deu"];
    }

    /**
     *
     */
    public function loadLanguages()
    {
        @mkdir($this->getDataFolder() . "lang/");
        $this->saveResource("lang/eng.yml");
        $this->saveResource("lang/deu.yml");
    }
}