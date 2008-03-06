<?php

class Options extends TypechoWidget
{
    public function push($value)
    {
        //将行数据按顺序置位
        $this->_rows[$value['option_name']] = $value['option_value'];
        $this->_stack[] = $value;
        return $value;
    }

    public function render()
    {
        $db = TypechoDb::get();

        $db->fetchAll($db->sql()
        ->select('table.option'), array($this, 'push'));
    }
}
