<?php

class agent_pluginlist extends lw_agent
{

    protected $config;
    protected $request;
    protected $response;


    public function __construct()
    {
        parent::__construct();
        $this->config = $this->conf;
        $this->className = "agent_directoryobserver";
        $this->adminSurfacePath = $this->config['path']['agents'] . "adminSurface/templates/";

        $usage = new lw_usage($this->className, "0");
        $this->secondaryUser = $usage->executeUsage();
        
        include_once(dirname(__FILE__) . '/Services/Autoloader.php');
        $autoloader = new \AgentPluginlist\Services\Autoloader($this->config);
        
        $this->response = new \AgentPluginlist\Services\Response();
        $this->response->setDbObject($this->db);
    }

    protected function showEdit()
    {
        $controller = new \AgentPluginlist\Controller\PluginlistController($this->config, $this->request, $this->response);
        $controller->execute();
        return $this->response->getOutputByKey("AgentPluginlist");
    }

    protected function buildNav()
    {
        $view = new \AgentPluginlist\Views\Navigation();
        return $view->render();
    }

    protected function deleteAllowed()
    {
        return true;
    }
}