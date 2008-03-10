<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 国际化字符翻译
 *
 * @package I18n
 */
class TypechoGetText
{
    static private $loaded = false;
    static private $_handle;
    static private $_files;
    static public $strings;
    static public $meta;

    /**
     * initialize i18n
     *
     * @return void
     */
    static public function init($lang)
    {
        self::$loaded = true;
        self::$_handle = NULL;
        self::$strings = array();
        self::$meta = array();
        self::$_files = array();
        
        self::load($lang);
    }

    /**
    * _read
    *
    * @access  private
    * @return  mixed
    * @param   int     $bytes
    */
    static private function _read($bytes = 1)
    {
        if (0 < $bytes = abs($bytes)) {
        return fread(self::$_handle, $bytes);
        }
        
        return null;
    }

    /**
    * _readInt
    *
    * @access  private
    * @return  int
    * @param   bool    $bigendian
    */
    static private function _readInt($bigendian = false)
    {
        return current($array = unpack($bigendian ? 'N' : 'V', self::_read(4)));
    }

    /**
    * _readStr
    *
    * @access  private
    * @return  string
    * @param   array   $params     associative array with offset and length 
    *                              of the string
    */
    static private function _readStr($params)
    {
        fseek(self::$_handle, $params['offset']);
        return self::_read($params['length']);
    }

    /**
    * meta2array
    *
    * @static
    * @access  private
    * @return  array
    * @param   string  $meta
    */
    static private function meta2array($meta)
    {
        $array = array();
        foreach (explode("\n", $meta) as $info) {
            if ($info = trim($info)) 
            {
                list($key, $value) = explode(':', $info, 2);
                $array[trim($key)] = trim($value);
            }
        }
        return $array;
    }
    
    /**
     * I18n function
     * Example:
     * #output '你好' in chinese
     * Lt18n::get('Hello');
     *
     * @param string $string
     * @return string
     */
    static public function get($key)
    {
        if(!self::$loaded) self::init();
        return isset(self::$strings[$key]) ? self::$strings[$key] : $key;
    }

    /**
    * File::Gettext
    * 
    * PHP versions 4 and 5
    *
    * @category   FileFormats
    * @package    File_Gettext
    * @author     Michael Wallner <mike@php.net>
    * @copyright  2004-2005 Michael Wallner
    * @license    BSD, revised
    * @version    CVS: $Id$
    * @link       http://pear.php.net/package/File_Gettext
    */
    static public function load($file)
    {
        if(!isset(self::$_files[$file]))
        {
        if(!file_exists($file))
        {
            return false;
        }

        // open MO file
        if (!is_resource(self::$_handle = @fopen($file, 'rb'))) {
            trigger_error('Unable To Read ' . $file, E_USER_WARNING);
            return false;
        }
        // lock MO file shared
        if (!@flock(self::$_handle, LOCK_SH)) {
            @fclose(self::$_handle);
            trigger_error('Unable To Unblock ' . $file, E_USER_WARNING);
            return false;
        }

        // read (part of) magic number from MO file header and define endianess
        switch ($magic = current($array = unpack('c', self::_read(4))))
        {
            case -34:
            $be = false;
            break;

            case -107:
            $be = true;
            break;

            default:
            trigger_error("No GNU mo file: $file (magic: $magic)", E_USER_WARNING);
            return false;
        }

        // check file format revision - we currently only support 0
        if (0 !== ($_rev = self::_readInt($be))) {
            trigger_error('Invalid file format revision: ' . $_rev, E_USER_WARNING);
            return false;
        }

        // count of strings in this file
        $count = self::_readInt($be);

        // offset of hashing table of the msgids
        $offset_original = self::_readInt($be);
        // offset of hashing table of the msgstrs
        $offset_translat = self::_readInt($be);

        // move to msgid hash table
        fseek(self::$_handle, $offset_original);
        // read lengths and offsets of msgids
        $original = array();
        for ($i = 0; $i < $count; $i++) {
        $original[$i] = array(
        'length' => self::_readInt($be),
        'offset' => self::_readInt($be)
        );
        }

        // move to msgstr hash table
        fseek(self::$_handle, $offset_translat);
        // read lengths and offsets of msgstrs
        $translat = array();
        for ($i = 0; $i < $count; $i++) {
        $translat[$i] = array(
        'length' => self::_readInt($be),
        'offset' => self::_readInt($be)
        );
        }

        // read all
        for ($i = 0; $i < $count; $i++) {
        self::$strings[self::_readStr($original[$i])] = 
        self::_readStr($translat[$i]);
        }

        // done
        @flock(self::$_handle, LOCK_UN);
        @fclose(self::$_handle);
        self::$_handle = null;

        // check for meta info
        if (isset(self::$strings[''])) {
        self::$meta = self::$meta2array(self::$strings['']);
        unset(self::$strings['']);
        }
            self::$_files[$file] = true;
        }
        return true;
    }
}
?>
