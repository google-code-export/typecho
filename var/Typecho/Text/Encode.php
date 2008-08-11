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
     * 解析字体格式
     * 
     * @access public
     * @return void
     */
    public function parseTypeface()
    {
        $pattern = array(
            "/\/\/([^\/]+)\/\//"    =>  "<i>\\1</i>",
            "/\*\*([^\*]+)\*\*/"    =>  "<strong>\\1</strong>",
            "/\^([^\^]+)\^/"        =>  "<sup>\\1</sup>",
            "/\,\,([^\,]+)\,\,/"    =>  "<sub>\\1</sub>",
            "/\~\~([^\~]+)\~\~/"    =>  "<del>\\1</del>",
            "/`([^`]+)`/e"           =>  "htmlspecialchars('\\1')",
        );
        
        $this->text = preg_replace(array_keys($pattern), array_values($pattern), $this->text);
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
            "/=([^=]+)=/"       =>  "<h1>\\1</h1>",
            "/==([^=]+)==/"       =>  "<h2>\\1</h2>",
            "/===([^=]+)===/"       =>  "<h3>\\1</h3>",
            "/====([^=]+)====/"       =>  "<h4>\\1</h4>",
            "/=====([^=]+)=====/"       =>  "<h5>\\1</h5>",
            "/======([^=]+)======/"       =>  "<h6>\\1</h6>",
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
     * 列表ul回调解析
     * 
     * @access public
     * @param array $matches 匹配的值
     * @return string
     */
    public function __parseListUl(array $matches)
    {
        $deep = count($matches[1]);
        $string  = $deep > $this->deep ? '<ul>' : '';
        $string .= '<li>' . $matches[2] . '</li>';
        $nextDeep = substr_count($matches[3], ' ');
        $this->deep = $deep;
        
        if($nextDeep == $deep && '-' == $matches[4])
        {
            return $string . $matches[3] . $matches[4];
        }
        else
        {
            return $string . "</ul>" . $matches[3] . $matches[4];
        }
    }
    
    /**
     * 解析列表
     * 
     * @access public
     * @return string
     */
    public function parseList()
    {
        $offsets = array();
        $stack = array();
        $pos = 0;
        
        /** 解析所有列表位置 */
        if(preg_match_all("/^( +)(-|#) (.+)/m", $this->text, $matches, PREG_OFFSET_CAPTURE))
        {
            foreach($matches[0] as $key => $val)
            {
                $stack[strlen($matches[1][$key][0])][] = array($val[1], $val[1] + strlen($val[0]), $matches[2][$key][0]);
            }
        }
        
        foreach($stack as $deep => $rows)
        {
            foreach($rows as $key => $row)
            {                
                if(0 < $key && (($rows[$key - 1][2] != $row[2]) || 
                '' != trim(preg_replace("/( +)(-|#) (.+)/", '', substr($this->text, $rows[$key - 1][1], $row[1])))
                ))
                {
                    $pos ++;
                }
                
                if(empty($offsets[$pos]))
                {
                    $offsets[$pos] = $row;
                }
                else
                {
                    $offsets[$pos][1] = $row[1];
                }
            }
            
            $pos ++;
        }
        
        $sorted = array();
        foreach($offsets as $offset)
        {
            $sorted[$offset[0]] = ('-' == $offset[2] ? "<ul>\n" : "<ol>\n");
            $sorted[$offset[1]] = ('-' == $offset[2] ? "\n</ul>" : "\n</ol>");
        }
        ksort($sorted);
        
        $count = 0;
        foreach($sorted as $offset => $tag)
        {
            $this->text = substr_replace($this->text, $tag, $offset + $count, 0);
            $count += strlen($tag);
        }
        
        $this->text = preg_replace("/^( +)(-|#) (.+)/m", "<li>\\3</li>", $this->text);
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
     * @param string $string
     * @return string
     */
    public function cutParagraph($string)
    {
        $string = "\n\n" . $string . "\n\n";
        
        //过滤非转义分段
        //$string = preg_replace("/<\/*(div|blockquote|pre|table|tr|th|td|li|ol|ul)[^>]*>/i", "\n\n\\0\n\n", $string);
        
        //区分段落
        $rows = explode("\n\n", trim($string));
        
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
        $this->text = $this->cutParagraph($this->text);
        $this->releaseHTML();
        
        return $this->text;
    }
}

$e = new Typecho_Text_Encode("sdfsadf - eeeeeee

<input type=\"text\" />
<a href=\"#\">adsdf</a>

 - a
  - b
   - j
   # **das**
   # adf
 - c
 - e");
echo $e;
