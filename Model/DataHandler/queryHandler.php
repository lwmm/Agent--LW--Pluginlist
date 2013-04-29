<?php

namespace AgentPluginlist\Model\DataHandler;

class queryHandler
{
    protected $className;
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllPluginsInTemplates()
    {
        $this->db->setStatement('SELECT id,name,template FROM t:lw_templates WHERE template IS NOT NULL ');
        return $this->db->pselect();
    }

    public function getAllPluginsInContentObjectTemplates()
    {
        $this->db->setStatement('SELECT id,name,template FROM t:lw_cobject WHERE template IS NOT NULL ');
        return $this->db->pselect();
    }

    public function getAllPluginsInContentObjects()
    {
        $this->db->setStatement('SELECT id,name,type,plugin FROM t:lw_cobject WHERE type=2 ');
        return $this->db->pselect();
    }

    public function getAllPluginsInPages_eav()
    {
        $this->db->setStatement('SELECT id,entity_id,value FROM t:lw_eav WHERE value IS NOT NULL ');
        return $this->db->pselect();
    }

    public function getAllPagePluginInfosByID($id)
    {
        $this->db->setStatement('SELECT c.id,c.page_id,c.container_id,p.name FROM t:lw_container c, t:lw_pages p WHERE c.id = :id AND c.page_id = p.id  ');
        $this->db->bindParameter("id", "i", $id);
        return $this->db->pselect1();
    }

}