<?php

namespace AgentPluginlist\Model\DataHandler;

class DataCollector
{

    protected $db;
    protected $config;

    function __construct($db, $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    function execute()
    {
        $this->dh = new \AgentPluginlist\Model\DataHandler\queryHandler($this->db);
        $this->arr = array();

        $plugin_template = $this->dh->getAllPluginsInTemplates();
        $plugin_cobject_template = $this->dh->getAllPluginsInContentObjectTemplates();
        $plugin_cobjects = $this->dh->getAllPluginsInContentObjects();
        $plugin_pages = $this->dh->getAllPluginsInPages_eav();

        foreach ($plugin_template as $entry) {
            $this->search_plugincall($entry, "Template");
        }

        foreach ($plugin_cobject_template as $entry) {
            $this->search_plugincall($entry, "CObject");
        }

        foreach ($plugin_cobjects as $entry) {
            $this->cobject_plugincall($entry);
        }

        foreach ($plugin_pages as $entry) {
            $this->search_plugincall_page($entry);
        }

        $this->existsSavingDirectory();
        \lw_io::writeFile($this->config["path"]["web_resource"] . "agent_pluginlist/datacollection.txt", serialize($this->arr));
        \AgentPluginlist\Services\Page::reload($this->config["url"]["client"] . "admin.php?obj=pluginlist");
    }

    function search_plugincall($entry, $type)
    {
        if ($type == "CObject") {
            $ifcobject = "Template: ";
        }
        else {
            $ifcobject = "";
        }

        preg_match_all("[\[PLUGIN:.*\]]", $entry["template"], $plugincalls_all_templates);

        foreach ($plugincalls_all_templates as $plugincalls_one_template) {
            foreach ($plugincalls_one_template as $plugincall) {
                array_push(
                        $this->arr, array(
                    "id" => $entry["id"],
                    "typ" => $type,
                    "name" => $entry["name"],
                    "plugin_aufruf" => $ifcobject . $plugincall,
                    "plugin_name" => $this->filterPluginName($plugincall)
                        )
                );
            }
        }
    }

    function cobject_plugincall($entry)
    {
        array_push(
                $this->arr, array(
            "id" => $entry["id"],
            "typ" => "CObject",
            "name" => $entry["name"],
            "plugin_aufruf" => $entry["plugin"],
            "plugin_name" => $entry["plugin"]
                )
        );
    }

    function search_plugincall_page($entry)
    {
        $page_infos = $this->dh->getAllPagePluginInfosByID($entry["entity_id"]);

        if ($page_infos["container_id"] == 1) {
            $ifContainerMain = "Main : ";
        }
        else {
            $ifContainerMain = "Teaser : ";
        }

        preg_match_all("[\[PLUGIN:.*\]]", $entry["value"], $plugincalls_all_values);
        foreach ($plugincalls_all_values as $plugincalls_one_value) {
            foreach ($plugincalls_one_value as $plugincall) {
                array_push(
                        $this->arr, array(
                    "id" => $page_infos["page_id"],
                    "typ" => "Page",
                    "name" => $page_infos["name"],
                    "plugin_aufruf" => $ifContainerMain . $plugincall,
                    "plugin_name" => $this->filterPluginName($plugincall)
                        )
                );
            }
        }
    }

    function filterPluginName($plugincall)
    {
        $pluginname = str_replace("[PLUGIN:", "", $plugincall);
        if (strpos($pluginname, "?") != false) {
            $pluginname = substr($pluginname, 0, strpos($pluginname, "?"));
        }
        else {
            $pluginname = str_replace("]", "", $pluginname);
        }

        return $pluginname;
    }

    function existsSavingDirectory($justCheckIfExists = false)
    {
        $directory = \lw_directory::getInstance($this->config["path"]["web_resource"]);
        $directories = $directory->getDirectoryContents("dir");
        foreach ($directories as $dir) {
            $contentArray[] = $dir->getName();
        }
        if (!(in_array("agent_pluginlist/", $contentArray))) {
            if ($justCheckIfExists == false) {
                $directory->add("agent_pluginlist");
                \lw_io::writeFile($this->config["path"]["web_resource"] . "agent_pluginlist/.htaccess", "Deny from all");
            }
        }
        else {
            return true;
        }
    }

}