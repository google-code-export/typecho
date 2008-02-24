<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

class ArchivesComponent extends TypechoComponent
{
    public function __construct(OptionsComponent $options)
    {
        require_once 'Archives/IndexArchives.php';
        $index = new IndexArchivesComponent($options);
        $this->proxy($index);
    }
}
