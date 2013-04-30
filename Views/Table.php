<?php

namespace AgentPluginlist\Views;

class Table
{
    protected  $config;
    
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function render($cmd)
    {
        if ($this->existsSavingDirectory(true) == true) {
            $data = unserialize(file_get_contents($this->config["path"]["resource"] . "agent_pluginlist/used_plugins.txt"));
            $data_config = unserialize(file_get_contents($this->config["path"]["resource"] . "agent_pluginlist/available_plugins.txt"));

        }
        
        $url = substr(\AgentPluginlist\Services\Page::getUrl(), 0, strpos(\AgentPluginlist\Services\Page::getUrl(), "index.php"))."admin.php";
        
        $view = new \lw_view(dirname(__FILE__) . '/Templates/Table.phtml');
        
        $view->SRCspin = $this->config["url"]["media"]."modules/spinloader/spin.js";
        $view->SRCspinmin = $this->config["url"]["media"]."modules/spinloader/spin.min.js";
        $view->bootstrapCSS = $this->config["url"]["media"] . "bootstrap/css/bootstrap.min.css";
        $view->bootstrapJS = $this->config["url"]["media"] . "bootstrap/js/bootstrap.min.js";
        $view->jqueryUICss = $this->config["url"]["media"] . "jquery/ui/css/smoothness/jquery-ui-1.8.7.custom.css";
        
        $view->SRCdataTables = $this->config["url"]["media"]."modules/dataTables/js/jquery.dataTables.min.js";
        $view->SRCdataTablesCSS = $this->config["url"]["media"]."modules/dataTables/css/dataTables.css";
        $view->dataTablesDemoCSS = $this->config["url"]["media"]."jquery/datatables/media/css/demo_page.css";
        $view->dataTablesHeaderCSS = "";
        $view->dataTablesJUICSS = $this->config["url"]["media"]."jquery/datatables/media/css/demo_table_jui.css";
        $view->dataTablesJS = $this->config["url"]["media"]."jquery/datatables/media/js/jquery.dataTables.min.js";
        
        $view->baseUrl = $url;
        $view->cmdd = $cmd;
        
        if($cmd == "listall"){
            $view->array = $data_config;
            $view->array2 = $data;
            $view->title = "verf&uuml;gbare Plugins";
            $view->link = $this->config["url"]["client"]."admin.php?obj=pluginlist";
            $view->linktext = "Verwendete Plugins anzeigen";
            
        } else {
            $view->array = $data;
            $view->array2 = $data_config;
            $view->title = "Plugins in Verwendung";
            $view->link = $this->config["url"]["client"]."admin.php?obj=pluginlist&cmd=listall";
            $view->linktext = "Verf&uuml;gbare Plugins anzeigen";
        }
        
        $view->link_scan = $this->config["url"]["client"]."admin.php?obj=pluginlist&cmd=datacollect";
        $view->linktext_scan = "Start Plugin Scan";
        
        $view->lastscandate = date("d.m.Y - H:i:s", filemtime($this->config["path"]["web_resource"] . "agent_pluginlist/used_plugins.txt"));
        return $view->render();
    }

    private function existsSavingDirectory($justCheckIfExists = false)
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
                return true;
            }
        }
        else {
            return true;
        }
    }

}