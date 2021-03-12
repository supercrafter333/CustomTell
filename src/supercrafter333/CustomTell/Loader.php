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
        if ($this->isVersion("1.1.0") == false) {
            $this->updateBaseConfig();
        }
        $this->loadLanguages();
        if ($this->isLangVersions("1.1.0") == false) {
            $this->updateLanguageFiles();
        }
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

    /**
     * @return bool
     */
    public function useConsole(): bool
    {
        if ($this->getBaseConfig()->get("use.ConsoleLog") == "true" || $this->getBaseConfig()->get("use.ConsoleLog") == "on") {
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function updateBaseConfig()
    {
        unlink($this->getDataFolder() . "config.yml");
        $this->saveResource("config.yml");
        $this->getLogger()->warning("config.yml was updated!");
    }

    /**
     * @return string
     */
    public function checkVersion(): string
    {
        return $this->getBaseConfig()->get("version");
    }

    /**
     * @param string $version
     * @return bool
     */
    public function isVersion(string $version): bool
    {
        if ($this->checkVersion() == $version) {
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function updateLanguageFiles()
    {
        unlink($this->getDataFolder() . "lang/TEMPLATE.yml");
        unlink($this->getDataFolder() . "lang/deu.yml");
        unlink($this->getDataFolder() . "lang/eng.yml");
        $this->loadLanguages();
        $this->getLogger()->warning("Language files was updated for new version!");
    }

    /**
     * @param string $version
     * @return bool
     */
    public function isLangVersions(string $version): bool
    {
        $baseCfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $language = $baseCfg->get("language");
        if ($language === "deu" || $language === "eng") {
            $useLang = new Config($this->getDataFolder() . "lang/" . $language . ".yml", Config::YAML);
            if ($useLang->exists("version")) {
                if ($this->translateString("version") == $version) {
                    return true;
                }
                return false;
            }
            return false;
        }
        return false;
    }
}