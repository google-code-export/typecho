<?php

class Options extends TypechoWidget
{
    private $headers;

    private function getSiteUrl()
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
    }
    
    public function push($value)
    {
        //将行数据按顺序置位
        $this->_row[$value['name']] = $value['value'];
        $this->_stack[] = $value;
        return $value;
    }

    public function render()
    {
        $db = TypechoDb::get();

        $db->fetchAll($db->sql()
        ->select('table.options')
        ->where('user = 0'), array($this, 'push'));

        $this->_row['site_url'] = $this->getSiteUrl();
        $this->_row['template_url'] = $this->_row['site_url'] . '/var/template/' . $this->_row['template'];
        $this->_row['gmt_time'] = gmmktime();
        $this->_row['rss_url'] = TypechoRoute::parse('rss', NULL, $this->_rows['site_url']);
        
        header('content-Type: text/html;charset= ' . $this->_row['charset']);
    }
}
