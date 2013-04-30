<?php

namespace AgentPluginlist\Model\DataHandler;

class ConfigDataCollector
{

    protected $config;

    function __construct($config)
    {
        $this->config = $config;
    }

    function execute()
    {
        $this->arr = array();

        $check = $this->existsConfigPathPlugins();
        if ($check != false) {
            $this->checkExistingPlugins($this->config["path"]["plugins"], "plugins");
        }

        foreach ($this->config["plugin_path"] as $dir) {
            $this->checkExistingPlugins($dir, "modules");
        }
        \lw_io::writeFile($this->config["path"]["resource"] . "agent_pluginlist/available_plugins.txt", serialize($this->arr));
    }

    function existsConfigPathPlugins()
    {
        $directory = \lw_directory::getInstance($this->config["path"]["server"]);
        $directories = $directory->getDirectoryContents("dir");
        foreach ($directories as $dir) {
            $contentArray[] = $dir->getName();
        }
        if (!(in_array("plugins/", $contentArray))) {
            return false;
        }
    }

    function checkExistingPlugins($directoryPath, $modulesOrPluginsDir)
    {
        $dir = \lw_directory::getInstance($directoryPath);
        $directories = $dir->getDirectoryContents("dir");

        if (!empty($directories)) {
            foreach ($directories as $directory) {
                if ($modulesOrPluginsDir == modules) {
                    $plugin_aufruf = "" . str_replace("/", "", str_replace("/", "", str_replace($this->config["path"]["server"] . "modules/", "", $directory->getBasePath())));
                }
                else {
                    $plugin_aufruf = "Plugin: " . $directory->getName();
                }
                array_push($this->arr, array(
                    "id" => " ",
                    "typ" => "Plugin",
                    "name" => str_replace("/", "", $directory->getName()),
                    "plugin_aufruf" => $plugin_aufruf,
                    "plugin_name" => str_replace("/", "", $directory->getName())
                        )
                );
            }
        }
    }
}