<?php

class DoWidget extends TypechoWidget
{
    public function render()
    {
        require_once TypechoRoute::handle('./widget/do', 'do', NULL, array('Post'));
        widget('do.' . $_GET['do']);
    }
}
