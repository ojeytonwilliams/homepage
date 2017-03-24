<?php
class Aphsh_Build
{
	public function build_files()
	{
		$options = get_option(APHSH_OPTION);
		if (!$options['token'])
		{
			$token = time();
			$options['token'] = $time;
			update_option(APHSH_OPTION, $options);
		}
		$css_path = APHSH_PLUGIN_PATH . APHSH_DS . 'includes' . APHSH_DS . 'syntaxhighlighter' . APHSH_DS . 'themes' . APHSH_DS . $options['theme'] . '.css';
				
		if (file_exists($css_path))
		{ 
			$font_size = $options['font-size'] == 'default' ? '1em !important' : $options['font-size'] . ' !important';
		
			$css_file = file_get_contents($css_path);
			
			
			
			$add_css = PHP_EOL . '.syntaxhighlighter {
	padding-bottom: 0.5px !important;
	font-size: ' . $font_size . ';';
			if ($options['max-height']){
				$add_css .= PHP_EOL . '	max-height: ' . $options['max-height'] . 'px;';
			}
			$add_css .= PHP_EOL . '}';
			
			// Additional Language
			$add_css .= "\r\n" . 
			"pre.aphsh-adddarkplain,
pre.aphsh-addlightplain {
    padding: 15px 20px !important;
    display: block;
    font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace;
    white-space: pre-wrap;
    white-space: -moz-pre-wrap;
    white-space: -o-pre-wrap;
    white-space: -pre-wrap;
    word-wrap: break-word;
    text-align: left
}
pre.aphsh-addlightplain {
	background: #efefef !important;
    color: #4a4a4a;
}
pre.aphsh-adddarkplain {
	background: #131313 !important;
	color: #CACACA;
}";
			
			if ($options['add-css']){
				$add_css .= PHP_EOL . $options['add-css-value'] . ';';
			}
			
			// $css_file = preg_replace('/font-size:\s*[^;]*\;/', 'font-size: ' . $font_size . ';', $css_file);
			array_map('unlink', glob(APHSH_PLUGIN_PATH . APHSH_DS . 'css' . APHSH_DS . 'syntaxhighlighter' . APHSH_DS . '*.*'));
			
			$target_path = APHSH_PLUGIN_PATH . APHSH_DS . 'css' . APHSH_DS . 'syntaxhighlighter' . APHSH_DS . 'aphsh-syntaxhighlighter-' . $options['token'] . '.css';
			file_put_contents($target_path, $css_file . $add_css, LOCK_EX);
		}
		
		$js_path = APHSH_PLUGIN_PATH . APHSH_DS . 'includes' . APHSH_DS . 'syntaxhighlighter' . APHSH_DS . 'syntaxhighlighter-' . $options['lang-pack'] . '.min.js';

		if (file_exists($js_path))
		{
			array_map('unlink', glob(APHSH_PLUGIN_PATH . APHSH_DS . 'js' . APHSH_DS . 'syntaxhighlighter' . APHSH_DS . '*.*'));
			$target_path = APHSH_PLUGIN_PATH . APHSH_DS . 'js' . APHSH_DS . 'syntaxhighlighter' . APHSH_DS . 'aphsh-syntaxhighlighter-' . $options['token'] . '.js';
			$js_file = file_get_contents($js_path);
			file_put_contents($target_path, $js_file, LOCK_EX);
		}
	}
}

?>