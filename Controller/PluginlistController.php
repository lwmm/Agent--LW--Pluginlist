<?php

namespace AgentPluginlist\Controller;

class PluginlistController
{
    protected $config;
    protected $request;
    protected $response;
    
    public function __construct($config, $request, $response)
    {
        $this->config = $config;
        $this->request = $request;  
        $this->response = $response;
    }
    
    public function execute()
    {
        
        $cmd = "listused";
        if($this->request->getAlnum("cmd")) {
            $cmd = $this->request->getAlnum("cmd");
        }
        
        if($cmd == "datacollect"){
            $this->existsSavingDirectory();
            
            $dataCollector = new \AgentPluginlist\Model\DataHandler\DataCollector($this->response->getDbObject(), $this->config);
            $configDataCollector = new \AgentPluginlist\Model\DataHandler\ConfigDataCollector($this->config);
            $configDataCollector->execute();
            $dataCollector->execute();
            
        }
        else{
            $view = new \AgentPluginlist\Views\Table($this->config);
            $this->response->setOutputByKey("AgentPluginlist", $view->render($cmd));
        }
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
                \lw_io::writeFile($this->config["path"]["resource"] . "agent_pluginlist/.htaccess", "Deny from all");
            }
        }
        else {
            return true;
        }
    }
    
}