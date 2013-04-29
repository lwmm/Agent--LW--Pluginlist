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
            $dataCollector = new \AgentPluginlist\Model\DataHandler\DataCollector($this->response->getDbObject(), $this->config);
            $configDataCollector = new \AgentPluginlist\Model\DataHandler\ConfigDataCollector($this->config);
            $dataCollector->execute();
            $configDataCollector->execute();
        }
        else{
            $view = new \AgentPluginlist\Views\Table($this->config);
            $this->response->setOutputByKey("AgentPluginlist", $view->render($cmd));
        }
    }
}