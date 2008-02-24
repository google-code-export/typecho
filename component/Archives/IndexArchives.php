<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

require_once 'AbstractArchives.inc.php';

class IndexArchivesComponent extends AbstractArchivesComponent
{
    private $timeFormat;

    public function __construct(OptionsComponent $options)
    {
        $db = TypechoDb::get();
        
        $db->fetchRows($db->sql()
        ->select('table.posts')
        ->order('post_id', 'DESC')
        ->limit(5), array($this, 'push'));
    }
    
    public function time($format = 'Y-m-d H:i:s')
    {
        echo date($format, $this->rows['post_time']);
    }
}
