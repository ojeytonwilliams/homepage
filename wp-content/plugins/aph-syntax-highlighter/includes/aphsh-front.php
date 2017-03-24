<?php

class Aphsh_Front 
{
	private $options;
	private $options_data;
	private $regex;
	private $match_post;
	private $match_comment = false;
	
	public function __construct()
	{
		$this->options = get_option(APHSH_OPTION);
		// Check the compiled file of css and js file of syntaxhighlighter
		$this->check_file();
			
		/**
		 * Regex to match all <pre> tag that have class value of lang:
		 * Pattern: <pre - space \s - any character [^>]* - class - space character(s) 
						 - = - space character(s) - " or ' - space character(s) - any character(s) - >
					any character until next group (.*)
					</pre>
			example: <pre class = " lang:  or <pre class="lang: or <pre 
																		class = "lang: 
		 */

		$this->options_data = get_option(APHSH_SHDATA);
		$this->regex = "/(<pre\s[^>]*class\s*=\s*[\"\']lang\s*:[^>]*>)(.*)(<\s*\/pre\s*>)/isU";
		
		add_action('wp_enqueue_scripts', array($this, 'load_scripts') );
		add_filter( 'the_content', array($this, 'filter_content'), 10, 1 );		
		add_filter( 'comment_text', array($this, 'filter_comments'), 10, 1);
	}
	
	private function check_file()
	{
		$css_file = APHSH_PLUGIN_PATH . APHSH_DS . 'css' . APHSH_DS . 'syntaxhighlighter' . APHSH_DS . 'aphsh-syntaxhighlighter-' . $this->options['token'] . '.css';	
		$js_file = APHSH_PLUGIN_PATH . APHSH_DS . 'js' . APHSH_DS . 'syntaxhighlighter' . APHSH_DS . 'aphsh-syntaxhighlighter-' . $this->options['token'] . '.js';
		
		if (!file_exists($css_file) || !file_exists($js_file))
		{
			require_once 'aphsh-build.php';
			$obj = new Aphsh_Build();
			$obj->build_files();
		}
	}
	
	public function load_scripts()
	{
		// If not post or page, we cannot check this earlier
		if (!is_single())
			return;
		
		/**
			Only load javascript and css file when needed,
			we check it in the post and comment content
		*/
		global $post;
		preg_match_all($this->regex, $post->post_content, $match_post);
		if ($match_post[0])
		{
			$this->match_post = $match_post;
		}

		if ($post->comment_count)
		{
			$comments = get_comments( array('post_id' => $post->ID, 'status' => 'approve') );
			if ($comments)
			{
				foreach ($comments as $comment)
				{
					preg_match_all($this->regex, $comment->comment_content, $match_comment);
					if ($match_comment[0])
					{
						$this->match_comment = true;
						break;
					}
				}
			}
		}
		
		if ($this->match_post || $this->match_comment)
		{			
			wp_enqueue_script( 'aphsh-syntaxhighlighter', APHSH_PLUGIN_URL . '/js/syntaxhighlighter/aphsh-syntaxhighlighter-' . $this->options['token'] . '.js' );
			wp_enqueue_style( 'aphsh-' . $this->options['theme'], APHSH_PLUGIN_URL . '/css/syntaxhighlighter/aphsh-syntaxhighlighter-'.$this->options['token'].'.css' );
		}
	}
	
	public function filter_comments($comment)
	{
		if ($this->match_comment)
		{
			preg_match_all($this->regex, $comment, $matches);
			
			if ($matches[0])
			{
				$comment = $this->alter_content($comment, $matches);
			}
		}
		return $comment;
	}
	
	public function filter_content($content)
	{
		// If not post or page, we cannot check this earlier
		if (is_single())
		{
			if ($this->match_post)
			{		
				return $this->alter_content($content, $this->match_post);
			}
		}
		return $content;
	}
		
	public function alter_content($content, $matches)
	{
		/**
		  Merge pre tag that have same value
		  <pre class="lang:php">
		  <pre class=" lang:php">
		*/
		$matches[1] = array_unique($matches[1]);
		
		/*
		* Make new variable $matches_fix to save new pre tag to acomodate the default title
		* $matches[0] = <pre class="lang:php" title="Specific Title">
		* $matches[1] = <pre class="lang:php">
		* $matches_fix[0] = <pre class="lang:php" title="Specific Title">
		* $matches_fix[1] = <pre class="lang:php" title="Default title">
		* Then, after we convert the class in the $matches_fix[0], we replace $matches[0] with $matches_fix[0]
		*/
		$matches_fix = $matches[1];

		foreach ($matches_fix as $tag_index => $tag)
		{
			// Get class value
			preg_match('/class\s*=\s*[\"\']([^\"\']*)[\"\']/si', $tag, $attr_class);
			
			/**
			 * fix class value by removing space between : sign => lang :  php become lang:php
			*/
			$fixed_class = preg_replace('/(\s*:\s*)/', ':', trim($attr_class[1]));
				
			$exp = explode(' ', $fixed_class);
			$add_language = '';
			foreach ($exp as $key => $class_item)
			{
				$split = explode(':', $class_item);
				$param = trim($split[0]);
				$value = trim($split[1]);
				
				if (strpos($value,'add') !== false) {
					$add_language = $value;
					continue;
				}
				// Used to compare with default options
				$param_list[$param] = $param;
				
				/* As is: html-script, auto-links*/
				
				// language
				if ($param == 'lang') {
					if ($value == 'markup')
						$value = 'xml';
					$curr_lang = $value;
					$exp[$key] = 'brush:' . $value;
				} 
				// highlight
				elseif ($param == 'mark') {
					$exp[$key] = 'highlight:' . $value;
				} 
				// class-name
				elseif ($param == 'class')
				{
					if ($this->options['class'])
						$value = $this->options['class'] . ' ' . $value;
					
					$exp[$key] = "class-name:'" . str_replace(';', ' ', $value) . "'";
				}
				// gutter
				elseif ($param == 'start')
				{
					$exp[$key] = 'first-line:' . $value;
				}
			}
			
			/* TITLE */
			$title = '';
			preg_match('/title\s*=\s*[\"\']([^\"\']*)[\"\']/si', $tag, $attr_title);
			if ($attr_title[1]) {
				$title = $all_attr[1]['title'];
			} 
			else if ($this->options['title'])  {
				$title = $this->options['title'];
			}
			if ($title) {
				$title = str_replace('{language}', $this->options_data['lang-list'][$this->options['lang-pack']][$curr_lang], $title);
				$tag = preg_replace('/([^>]*)>/', '$1'. ' title="'.$title.'">',  $tag);
			}
			
			// Add default options as nedded
			$def_options = array('gutter', 'auto-links', 'tab-size', 'class');
			foreach($def_options as $opt)
			{
				if (!key_exists($opt, $param_list))
				{
					$exp[] = $opt . ':' . $this->options[$opt];
					if ($opt == 'gutter')
					{
						$exp['first-line'] = 'first-line:' . $this->options['start-number'];
					}
				}
			}
			if ($this->options['smart-tab'])
			{
				$exp[] = 'smart-tabs:' . $this->options['smart-tab']; 
			}
			
			if ($this->options['tab-size'] != 4)
			{
				$exp[] = 'tab-size:' . $this->options['tab-size']; 
			}
			
			if ($add_language) {
				$exp = array('aphsh-' . $add_language);
			}
			$new_class = join($exp, ';');
			$tag = preg_replace('/'.$attr_class[1].'/', $new_class, $tag);
			
			$content = preg_replace('/'.$matches[1][$tag_index].'/', $tag, $content);
		}
		return $content;
	}
}