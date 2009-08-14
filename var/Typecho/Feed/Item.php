<?php
 /**
 * Univarsel Feed Writer
 * 
 * FeedItem class - Used as feed element in FeedWriter class
 *
 * @package         Feed
 * @author          Anis uddin Ahmad <anisniit@gmail.com>
 * @link            http://www.ajaxray.com/projects/rss
 */
class Typecho_Feed_Item
{
	private $elements = array();    //Collection of feed elements
	private $version;
	
	/**
	* Constructor 
	* 
	* @param    contant     (RSS1/RSS2/ATOM) RSS2 is default. 
	*/ 
	function __construct($version = Typecho_Feed::RSS2)
	{    
		$this->version = $version;
	}
	
	/**
	* Add an element to elements array
	* 
	* @access   public
	* @param    srting  The tag name of an element
	* @param    srting  The content of tag
	* @param    array   Attributes(if any) in 'attrName' => 'attrValue' format
	* @return   void
	*/
	public function addElement($elementName, $content, $attributes = null)
	{
		$this->elements[$elementName]['name']       = $elementName;
		$this->elements[$elementName]['content']    = $content;
		$this->elements[$elementName]['attributes'] = $attributes;
	}
	
	/**
	* Set multiple feed elements from an array. 
	* Elements which have attributes cannot be added by this method
	* 
	* @access   public
	* @param    array   array of elements in 'tagName' => 'tagContent' format.
	* @return   void
	*/
	public function addElementArray($elementArray)
	{
		if (! is_array($elementArray)) return;
		foreach ($elementArray as $elementName => $content) {
			$this->addElement($elementName, $content);
		}
	}
	
	/**
	* Return the collection of elements in this feed item
	* 
	* @access   public
	* @return   array
	*/
	public function getElements()
	{
		return $this->elements;
	}
	
	// Wrapper functions ------------------------------------------------------
	
	/**
	* Set the 'dscription' element of feed item
	* 
	* @access   public
	* @param    string  The content of 'description' element
	* @return   void
	*/
	public function setDescription($description)
	{
		$tag = ($this->version == Typecho_Feed::ATOM1)? 'summary' : 'description'; 
		$this->addElement($tag, $description);
	}
	
	/**
	 * setContent function.
	 * 
	 * @access public
	 * @param string $content the conent of 'content' element
	 * @return void
	 */
	public function setContent($content)
	{
	   if (Typecho_Feed::RSS2 == $this->version) {
	       $this->addElement('content:encoded', $content);
	   } else if (Typecho_Feed::ATOM1 == $this->version) {
	       $this->addElement('content', $content, array(
	           'type'  =>  'xhtml',
	           'xml:base' => 'http://diveintomark.org/'
	       ));
	   }
	}
	
	/**
	* @desc     Set the 'title' element of feed item
	* @access   public
	* @param    string  The content of 'title' element
	* @return   void
	*/
	public function setTitle($title)
	{
		$this->addElement('title', $title);  	
	}
	
	/**
	* @desc     Set the 'title' element of feed item
	* @access   public
	* @param    string  The content of 'title' element
	* @return   void
	*/
	public function setCategory($category)
	{
		$this->addElement('category', $category);  	
	}
    
	/**
	* Set the 'date' element of feed item
	* 
	* @access   public
	* @param    string  The content of 'date' element
	* @return   void
	*/
	public function setDate($date)
	{
		if (! is_numeric($date)) {
			$date = strtotime($date);
		}
		
		if ($this->version == Typecho_Feed::ATOM1) {
			$tag    = 'updated';
			$value  = gmdate(Typecho_Feed::DATE_ATOM, $date);
		} elseif ($this->version == Typecho_Feed::RSS2) {
			$tag    = 'pubDate';
			$value  = gmdate(Typecho_Feed::DATE_RSS, $date);
		} else {
			$tag    = 'dc:date';
			$value  = gmdate("Y-m-d", $date);
		}
		
		$this->addElement($tag, $value);
	}
	
	/**
	* Set the 'link' element of feed item
	* 
	* @access   public
	* @param    string  The content of 'link' element
	* @return   void
	*/
	public function setLink($link)
	{
		if ($this->version == Typecho_Feed::RSS2 || $this->version == Typecho_Feed::RSS1) {
			$this->addElement('link', $link);
		} else {
			$this->addElement('link','',array('href'=>$link));
			$this->addElement('id', Typecho_Feed_Writer::uuid($link,'urn:uuid:'));
		} 
		
	}
	
	/**
	* Set the 'encloser' element of feed item
	* For RSS 2.0 only
	* 
	* @access   public
	* @param    string  The url attribute of encloser tag
	* @param    string  The length attribute of encloser tag
	* @param    string  The type attribute of encloser tag
	* @return   void
	*/
	public function setEncloser($url, $length, $type)
	{
		$attributes = array('url'=>$url, 'length'=>$length, 'type'=>$type);
		$this->addElement('enclosure','',$attributes);
	}
	
} // end of class FeedItem
