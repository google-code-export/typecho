<?php

class Typecho_Text_Encode
{
    /**
     * 已锁定的html块
     * 
     * @access private
     * @var array
     */
    private $_blocks = array();

    /**
     * 待处理的文本
     * 
     * @access public
     * @var string
     */
    public $text;

    /**
     * 构造函数,处理输入文本
     * 
     * @access public
     * @param string $text 输入文本
     * @return void
     */
    public function __construct($text)
    {
        $this->text = $text;
    }
    
    /**
     * 增加匹配模式
     * 
     * @access private
     * @param string $mark 标记
     * @param string $tag html标签
     * @return void
     */
    private function addPattern($mark, $tag)
    {
        $mark = preg_quote($mark);
        $this->text = preg_replace("@{$mark}(.+){$mark}@m", "<$tag>\\1</$tag>", $this->text);
    }
    
    /**
     * 解析字体格式
     * 
     * @access public
     * @return void
     */
    public function parseTypeface()
    {
        $this->addPattern('//', 'i');
        $this->addPattern('__', 'u');
        $this->addPattern('**', 'strong');
        $this->addPattern('^', 'sup');
        $this->addPattern(',,', 'sub');
        $this->addPattern('~~', 'del');
    }
    
    /**
     * 解析标题
     * 
     * @access public
     * @return void
     */
    public function parseHeading()
    {
        $pattern = array(
            "/^[ ]*=([^=]+)=/em"       =>  "'<h1>' . trim(\\1) . '</h1>'",
            "/^[ ]*==([^=]+)==/em"       =>  "'<h2>' . trim(\\1) . '</h2>'",
            "/^[ ]*===([^=]+)===/em"       =>  "'<h3>' . trim(\\1) . '</h3>'",
            "/^[ ]*====([^=]+)====/em"       =>  "'<h4>' . trim(\\1) . '</h4>'",
            "/^[ ]*=====([^=]+)=====/em"       =>  "'<h5>' . trim(\\1) . '</h5>'",
            "/^[ ]*======([^=]+)======/em"       =>  "'<h6>' . trim(\\1) . '</h6>'",
        );
        
        $this->text = preg_replace(array_keys($pattern), array_values($pattern), $this->text);
    }
    
    /**
     * 图片解析正则回调函数
     * 
     * @access public
     * @param array $matches 匹配的值
     * @return string
     */
    public function __parseImage(array $matches)
    {
        $inside = $matches[1];
        $align  = NULL;
        
        /** 判断对齐 */
        if(' ' == $inside[0])
        {
            $align = ' align="right"';
        }
        
        if(" " == $inside[strlen($inside) - 1])
        {
            $align = (' align="right"' == $align) ? ' align="center"' : ' align="left"';
        }
        
        $image = array_map('trim', explode('|', $inside));
        $count = count($image);
        
        if(1 == $count)
        {
            $alt = basename($image[0]);
            return '<img src="' . $image[0] . '" alt="' . $alt . '"' . $align . ' />';
        }
        else if(2 == $count)
        {
            return '<img src="' . $image[0] . '" title="' . $image[1] . '" alt="' . $image[1] . '"' . $align . ' />';
        }
        else if(3 == $count)
        {
            $size = explode('x', str_replace('*', 'x', $image[2]));
            $size = 1 < count($size) ? $size : array($size[0], $size[0]);
            
            return '<img src="' . $image[0] . '" title="' . $image[1] . '" alt="' . $image[1] .
            '" width="' . $size[0] . '" height="' . $size[1] . '"' . $align . ' />';
        }
    }
    
    /**
     * 解析图片
     * 
     * @access public
     * @return void
     */
    public function parseImage()
    {
        $this->text = preg_replace_callback("/\{\{([^\}]+)\}\}/s", array($this, '__parseImage'), $this->text);
    }
    
    /**
     * 链接解析正则回调函数
     * 
     * @access public
     * @param array $matches 匹配的值
     * @return string
     */
    public function __parseLink(array $matches)
    {
        $inside = $matches[1];
        $link = array_map('trim', explode('|', $inside));
        
        return '<a href="' . $link[0] . '" title="' . $link[0] . '">' .
        (1 < count($link) ? $link[1] : $link[0]) . '</a>';
    }
    
    /**
     * 解析链接
     * 
     * @access public
     * @return void
     */
    public function parseLink()
    {
        $this->text = preg_replace_callback("/\[\[([^\]]+)\]\]/s", array($this, '__parseLink'), $this->text);
    }
    
    /**
     * 列表回调解析
     * 
     * @access public
     * @param array $matches 匹配的值
     * @return string
     */
    public function __parseList(array $matches)
    {
        $tag = ('-' == $matches[2] ? 'ul' : 'ol');
        return $matches[1] . "<{$tag}>" . '<li>' . trim($matches[3]) . '</li>' . "</{$tag}>";
    }
    
    /**
     * 解析列表
     * 
     * @access public
     * @return string
     */
    public function parseList()
    {
        $this->text = preg_replace_callback("/^([ ]+)(-|#)[ ]+(.+)/m", array($this, '__parseList'), $this->text);
    
        $lines = preg_split("(\r\n|\n|\r)", $this->text);
        $last = false;
        $space = '';
        $tag = '';
        
        foreach($lines as $key => $line)
        {
            if(preg_match("/^([ ]+)\<(ul|ol)\>(.+)\<\/(ul|ol)\>$/", $line, $matches))
            {
                if($key == $last + 1)
                {
                    $posx = substr_count($matches[1], ' ');
                    $posy = substr_count($space, ' ');
                
                    if($posx > $posy)
                    {
                        $lines[$last] = substr($lines[$last], 0, -5);
                    }
                    if($posx < $posy)
                    {
                        $lines[$key] = substr_replace($lines[$key], '', $posx, 4);
                    }
                    else if($tag == $matches[2] && $posx == $posy)
                    {
                        $lines[$last] = substr($lines[$last], 0, -5);
                        $lines[$key] = substr_replace($lines[$key], '', $posx, 4);
                    }
                }
            
                $last = $key;
                $space = $matches[1];
                $tag = $matches[4];
            }
        }
        
        $this->text = implode("\n", $lines);
    }
    
    /**
     * 锁定标签回调函数
     * 
     * @access public
     * @param array $matches 匹配的值
     * @return string
     */
    public function __lockHTML(array $matches)
    {
        $guid = uniqid(time());
        $this->_blocks[$guid] = $matches[0];
        return $guid;
    }
    
    /**
     * 锁定转义标签回调函数
     * 
     * @access public
     * @param array $matches 匹配的值
     * @return string
     */
    public function __lockSpecialHTML(array $matches)
    {
        $guid = uniqid(time());
        $this->_blocks[$guid] = htmlspecialchars($matches[1]);
        return $guid;
    }
    
    /**
     * 锁定所有HTML标签
     * 
     * @access public
     * @return void
     */
    public function lockHTML()
    {
        /** 锁定自闭合标签 */
        $this->text = preg_replace_callback("/\<\w+[^\>]*\/\>/is", array($this, '__lockHTML'), $this->text);
        
        /** 锁定开标签 */
        $this->text = preg_replace_callback("/\<\w+[^\>]*\>.*\<\/\w+\>/is", array($this, '__lockHTML'), $this->text);
        
        /** 锁定转义标签 */
        $this->text = preg_replace_callback("/``([^`]+)``/m", array($this, '__lockSpecialHTML'), $this->text);
    }
    
    /**
     * 释放HTML锁定
     * 
     * @access public
     * @return void
     */
    public function releaseHTML()
    {
        $this->text = str_replace(array_keys($this->_blocks), array_values($this->_blocks), $this->text);
    }
    
    /**
     * 文本分段函数
     *
     * @access public
     * @return void
     */
    public function parseParagraph()
    {
        /** 区分段落 */
        $this->text = preg_replace("/(\r\n|\n|\n)/", "\n", $this->text);
        $rows = explode("\n\n", trim($this->text));
        
        $finalRows = array();
        
        //去掉空段落
        foreach($rows as $row)
        {
            $row = trim($row);
            
            if($row)
            {
                $result = '';
                if(!preg_match("/^<\/*(div|code|blockquote|pre|table|tr|th|td|li|ol|ul)(.*)$/is", $row))
                {
                    $row = '<p>' . $row . '</p>';
                }
                
                $finalRows[] = preg_replace("/(\w)\n(\w)/", '\\1<br />\\2', $row);
            }
        }
        
        return implode('', $finalRows);
    }
    
    /**
     * 解析所有文本
     * 
     * @access public
     * @return string
     */
    public function __toString()
    {
        $this->lockHTML();
        $this->parseTypeface();
        $this->parseHeading();
        $this->parseImage();
        $this->parseLink();
        $this->parseList();
        $this->parseParagraph();
        $this->releaseHTML();
        
        return $this->text;
    }
}

$e = new Typecho_Text_Encode("**sdfsadf** - eeeeeee

<input type=\"text\" />
<a href=\"#\">adsdf</a>

= h1 =

 - a
  - b
   - j
   # **das**
   # adf
 - c
 - e");
echo $e;
