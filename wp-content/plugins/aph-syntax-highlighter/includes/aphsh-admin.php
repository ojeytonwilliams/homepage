<?php
class Aphsh_Admin
{
    
	private $languages;
	
	/**
		Initiate options when the plugin is activated
	*/
	private $default_param = array(
							// Styles
							'lang-pack' => array('compact-1' => 'Compact 1',
												'compact-2' => 'Compact 2',
												'common-1' => 'Common 1',
												'full' => 'Full'
										),
							'lang-list'	=> array('compact-1' => array (
																	'php' => 'PHP',
																	'xml' => 'XML / HTML',
																	'css' => 'CSS',
																	'javascript' => 'Javascript',
																	'sass' => 'SASS',
																	'sql' => 'SQL',
																	'plain' => 'SH Plain',
																	'adddarkplain' => 'Dark Plain (Console)',
																	'addlightplain' => 'Light Plain'
																),
												'compact-2' => array ('java' => 'Java',
																	'bash' => 'Bash',
																	'ruby' => 'Ruby',
																	'python' => 'Python',
																	'cpp' => 'C++',
																	'csharp' => 'C#',
																	'perl' => 'Perl',
																	'sql' => 'SQL',
																	'plain' => 'SH Plain',
																	'adddarkplain' => 'Dark Plain (Console)',
																	'addlightplain' => 'Light Plain'
																),
												'common-1' => array(
																	'php' => 'PHP',
																	'xml' => 'XML / HTML',
																	'css' => 'CSS',
																	'javascript' => 'Javascript',
																	'sass' => 'SASS',
																	'java' => 'Java',
																	'bash' => 'Bash',
																	'ruby' => 'Ruby',
																	'python' => 'Python',
																	'cpp' => 'C++',
																	'csharp' => 'C#',
																	'perl' => 'Perl',
																	'sql' => 'SQL',
																	'diff' => 'Diff',
																	'plain' => 'SH Plain',
																	'adddarkplain' => 'Dark Plain (Console)',
																	'addlightplain' => 'Light Plain'
												),
												'full' => array(
																	'php' => 'PHP',
																	'xml' => 'XML / HTML',
																	'css' => 'CSS',
																	'javascript' => 'Javascript',
																	'sass' => 'SASS',
																	'java' => 'Java',
																	'bash' => 'Bash',
																	'ruby' => 'Ruby',
																	'python' => 'Python',
																	'cpp' => 'C++',
																	'csharp' => 'C#',
																	'perl' => 'Perl',
																	'vb' => 'VB',
																	'poweshell' => 'Power Shell',
																	'typescript' => 'Typescript',
																	'tap' => 'Tap',
																	'swift' => 'Swift',
																	'scala' => 'Scala',
																	'javafx' => 'Java FX',
																	'haxe' => 'Haxe',
																	'groovy' => 'Groovy',
																	'erlang' => 'Erlang',
																	'delphi' => 'Delphi',
																	'coldfushion' => 'Coldfushion',
																	'as3' => 'Action Script 3',
																	'applescript' => 'Applescript',
																	'base' => 'Base',
																	'sql' => 'SQL',
																	'diff' => 'Diff',
																	'plain' => 'SH Plain',
																	'adddarkplain' => 'Dark Plain (Console)',
																	'addlightplain' => 'Light Plain'
												),
											),
											
							'themes'	=>	array(
												'default' => 'Default',
												'django' => 'Django',
												'eclipse' => 'Eclipse',
												'emacs' => 'EMACS',
												'fadetogrey' => 'Fade to Grey',
												'mdultra' => 'MDUltra',
												'midnight' => 'Midnight',
												'rdark' => 'RDark',
												'swift' => 'Swift'
											)
						);
	/**
	 * Default options, we use dash instead of undercore, because we'll use this
	 * in the foreach loop at aphms-front.php
	*/
	private $data_options = array ('lang-pack' => 'compact-1',
									'default-lang' => 'php',
									'theme' => 'default',
									'gutter' => 1,
									'start-number' => 1,
									'auto-links' => 1,
									'collapse' => 0,
									'class' => '',
									'smart-tabs' => 1,
									'tab-size' => 4,
									'title' => '',
									'font-size' => 'default',
									'max-height' => '480',
									'add-css' => 0,
									'add-css-value' => '',
									'token' => '1470914799'
								);
	
	private $admin_notice;
								
    public function __construct()
    {
		$this->admin_notices = new Aphsh_Admin_Notices;
		$this->options = get_option( APHSH_OPTION, array() );
		
		if (!$this->options) {
			update_option(APHSH_OPTION, $this->data_options);
		}
		
		register_activation_hook ( APHSH_PLUGIN_PATH . APHSH_DS . APHSH_PLUGIN_FILE_NAME, array($this, 'activate_plugin') );
		
		add_action( 'admin_init', array( $this, 'page_init' ) );
		add_action( 'admin_head', array( $this, 'print_style') );
		
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_filter( 'plugin_action_links', array($this, 'action_link'), 10, 5);
		add_action( 'admin_enqueue_scripts', array($this, 'register_scripts') );
		
		add_action( 'updated_option', array($this, 'update_files'), 1); 
		// add_action( 'admin_notices', array( $this, 'adminNotices' ), 1 );
		add_action( 'plugins_loaded', array($this, 'check_update') );
		
		// AJAX
		add_action( 'wp_ajax_nopriv_aphsh-dismiss-notice' , array( $this , 'ajax_no_priv' ) );
		add_action( 'wp_ajax_aphsh-dismiss-notice', array($this, 'ajax_dismiss_notice') );
    }
	
	public function ajax_no_priv()
	{
		// echo 'xxx';  die;
		
	}
	
	// When the close button is clicked 
	public function ajax_dismiss_notice()
	{
		$check = wp_verify_nonce($_POST['nonce'], 'aphsh-admin-all');
		if ($check)
		{
			$this->admin_notices->delete_notice($_POST['msg']);
		}
		wp_send_json_success(
			array(
				'msg' => 'success',
				'check' => $check
			)
		);
	}
	
	public function update_files()
	{
		$obj = new Aphsh_Build();
		$obj->build_files();
	}
	
	public function activate_plugin()
	{
		if (!$this->options) {
			update_option(APHSH_OPTION, $this->data_options);
			update_option(APHSH_OPTION_VERSION, APHSH_PLUGIN_VERSION);
			update_option(APHSH_OPTION_SHDATA, $this->default_param);
		}
	}
	
	public function check_update() 
	{
		$plugin_option_version = get_option( APHSH_OPTION_VERSION, '0' );
		// $plugin_option_version = '0';
		if (version_compare(APHSH_PLUGIN_VERSION, $plugin_option_version) > 0)
		{
			update_option(APHSH_OPTION_VERSION, APHSH_PLUGIN_VERSION);
			update_option(APHSH_OPTION_SHDATA, $this->default_param);
			if (!$plugin_option_version || $plugin_option_version < 1.2)
			{
				$msg = 'APH SYNTAX HIGHLIGHTER v' . APHSH_PLUGIN_VERSION . ' Language added: Dark Plain (Console Style) and Light Plain';
				$this->admin_notices->add_notice($msg, 'success', false, true);
			}
		}
	}
	
	public function register_scripts($hook)
	{
		if ($hook == 'settings_page_'.APHSH_PLUGIN_DIR_NAME)
		{
			wp_enqueue_style('aphsh-style', APHSH_PLUGIN_URL . '/css/aphsh-admin.css?rand='.time(), '', APHSH_PLUGIN_VERSION);
			wp_enqueue_script('aphsh-admin', APHSH_PLUGIN_URL . '/js/aphsh-admin.js?rand='.time(), '', APHSH_PLUGIN_VERSION);
			wp_enqueue_script('aphsh-taboverride', APHSH_PLUGIN_URL . '/js/taboverride/taboverride.min.js', '', APHSH_PLUGIN_VERSION);
		}
		
		wp_enqueue_script('aphsh-admin-all', APHSH_PLUGIN_URL . '/js/aphsh-admin-all.js?rand='.time(), '', APHSH_PLUGIN_VERSION);
		wp_localize_script (
			'aphsh-admin-all', 
			'aphsh', 
			array(
				'nonce'	=> wp_create_nonce('aphsh-admin-all'),
				'ajaxurl' => admin_url('admin-ajax.php')
			)
		);
	}
	
	/**
	* Add Settings ling to plugin list Settings | Deactivate | Edit
	*/
	public function action_link($links, $file)	
	{
		static $plugin;
		
		if (!isset($plugin))
			$plugin = APHSH_PLUGIN_DIR_NAME . '/' . APHSH_PLUGIN_FILE_NAME;
		
		if ($plugin == $file)
		{
			$setting_link = '<a href="'.get_bloginfo('wpurl').'/wp-admin/admin.php?page='.APHSH_PLUGIN_DIR_NAME.'">Settings</a>';
			array_unshift($links, $setting_link);
		}
		return $links;
	}
	
	public function print_style() {
		echo '<style>
			.lang-list{
				margin-right: 25px;
			}
		} 
		</style>';
	}
	
	/**
     * Add options page
     */
    public function add_plugin_page()
    {
        $page_title	= 'APH Syntax Highlighter Options';
		$menu_title	= 'Syntax Highlighter';
		$url = 'aph-syntax-highlighter';
        add_options_page(
            $page_title, 
            $menu_title, 
            'manage_options', 
            $url,
            array( $this, 'admin_setting_page' )
        );
    }

    public function admin_setting_page()
    {
		// echo '<pre>'; print_r($this->options);
		?>
		<div class="wrap">
            <h2 style="display:none">Options</h2>
			<div class="aphsh-wrap">
				<h2 class="title">APH Syntax Highlighter Options</h2>
				<div class="aphsh-form-container"> 
					<form method="post" action="options.php" id="aphsh-form">
					<?php settings_fields('aphsh_option_group'); ?>
					<table class="form-table">
						<tr>
							<th>Language</th>
							<td>
								<?php echo $this->option_language_list(); ?>
							</td>
						</tr>
						<tr>
							<th>Default Language</th>
							<td>
								<?php echo $this->option_default_language()?>
							</td>
						<tr>
							<th>Theme</th>
							<td>
								<?php echo $this->option_theme_list(); ?>
							</td>
						</tr>
						<tr>
							<th>Options</th>
							<td>
								<?php echo $this->option_plugin_options(); ?>
							</td>
						</tr>
						<tr>
							<th>Starting Line Number</th>
							<td>
								<input type="text" class="small-text" name="<?php echo APHSH_OPTION ?>[start-number]" value="<?php echo $this->options['start-number'] ?>"/>
								<p class="description">Used when "Show line numbers" is checked<p>
							</td>
						</tr>
						<tr>
							<th>Tab Size</th>
							<td>
								<input type="text" class="small-text" name="<?php echo APHSH_OPTION ?>[tab-size]" value="<?php echo $this->options['tab-size']?>"/>
								<p class="description">How many <code>&amp;nbsp;</code> character used to replace a single tab</p>
							</td>
						</tr>
						<tr>
							<th>Add Class</th>
							<td>
								<input type="text" name="<?php echo APHSH_OPTION ?>[class]" value="<?php echo $this->options['class']?>"/>
								<p class="description">Add class to each code block container</p>
							</td>
						</tr>
						<tr>
							<th>Title</th>
							<td>
								<input type="text" name="<?php echo APHSH_OPTION ?>[title]" value="<?php echo $this->options['title']?>"/>
								<p class="description">Add title attribute to each code block container, e.q: &lt;pre class="classes" title="Some Title"&gt; Use {language} to display the language of the code, for example: {language} Code will be displayed as Javascript Code</p>
							</td>
						</tr>
						<tr>
							<th>Font Size</th>
							<td>
								<?php
								$font_size = array(
													'default' => 'Default',
													'11px' => '11px', 
													'12px' => '12px', 
													'13px' => '13px', 
													'14px' => '14px',
													'15px' => '15px',
													'16px' => '16px',
												);
												
								$options = '<select name="' . APHSH_OPTION . '[font-size]">';
								foreach($font_size as $font_size => $display) {
									$selected = $this->options['font-size'] == $font_size ? ' selected="selected"' : '';
									$options .= '<option value="'.$font_size.'"' . $selected . '>' . $display . '</option>';
								}
								$options .= '</select>';
								
								echo $options;
								?>
							</td>
						</tr>
						<tr>
							<th>Max Height</th>
							<td>
								<input type="text" class="small-text" name="<?php echo APHSH_OPTION ?>[max-height]" value="<?php echo $this->options['max-height'] ?>"/>px
								<p class="description">Set maximum height of code container. Recomended 480px. This is usefull in long code, users don't need to scroll page a lot when they want to continue reading the article</p>
							</td>
						</tr>
						<tr>
							<th>Additional CSS</th>
							<td>
								<?php
								$list = array(0 => 'No', 1 => 'Yes');
								echo '<select name="' . APHSH_OPTION . '[add-css]" id="aphsh-add-css-option">';
								foreach ($list as $key => $val)
								{
									$selected = $key == $this->options['add-css'] ? ' selected="selected"' : '';
									echo '<option value="'.$key.'"'.$selected.'>'. $val .'</option>';
								}
								echo '</select>';
								
								$show_add_css = !$this->options['add-css'] ? ' style="display:none"' : '';
								?>
								<p class="description">
									Add css code to the compiled css file. This is useful for example if we want to add responsive style to the code.
								</p>
								<div id="aphsh-add-css-container"<?=$show_add_css?>>
									<p>
										<textarea class="aphsh-textarea" id="aphsh-add-css-textarea" name="<?php echo APHSH_OPTION ?>[add-css-value]"/><?=$this->options['add-css-value']?></textarea>
									</p>
									<p class="description">
										The container class is syntaxhighlighter. <a href="#" id="aphsh-css-example-btn">Click here for example of responsive style</a>
									</p>
									<pre class="aphsh-css-example" id="aphsh-css-example" style="display:none">@media screen and (max-width: 640px) {
    .syntaxhighlighter {
		font-size: 15px !important;
	}
}

@media (min-width:641px) and (max-width:800px) {
    .syntaxhighlighter {
		font-size: 16px !important;
	}
}</pre>
							</div>
							</td>
						</tr>
						<tr>
							<th>Add global class</th>
							<td>
								<input type="text" name="<?php echo APHSH_OPTION ?>[class]" value="<?php echo $this->options['class']?>"/>
								<p class="description">Add class to each code block container</p>
							</td>
						</tr>
					</table>
					<?php if (function_exists('submit_button'))
					{
						submit_button('Save Changes', 'primary', 'aphsh-submit', false);
						echo ' ';
						submit_button('Restore to Defaults', 'primary', 'aphsh-defaults', false);
					} else {
						echo '
							<input type="submit" name="aphsh-submit" id="aphsh-submit" class="button button-primary" value="Save Changes"/> 
							<input type="submit" name="aphsh-defaults" id="aphsh-defaults" class="button button-primary" value="Restore to Defaults"/>
						';
					}?>
					</form>
				</div>
			</div>
		</div>
		
       
        <?php
    }
	
	public function page_init()
    {    
		register_setting(
            'aphsh_option_group', // Option group
            APHSH_OPTION,
			array ($this, 'submit_validation')
        );
	}
	
	public function option_language_list() {

		$lang_pack = $this->default_param['lang-pack'];
		
		
		echo '<select name="' . APHSH_OPTION . '[lang-pack]" id="opt-aphsh-lang-pack">';
		foreach($lang_pack as $pack => $pack_name)
		{
			echo '<option value="'.$pack.'">' . $pack_name . '</option>';
		}
		echo '</select>';
		
		echo '<div class="aphsh-info">Since syntax highlighter 4 do not support dynamically load brushes, so we need to build it at first. In order not to bloat the file size, we sparate the bruses</div>';
		echo '<ul>';
		foreach($lang_pack as $pack => $pack_name)
		{
			$lang_list = $this->default_param['lang-list'][$pack];
			echo '<li><strong>' . $pack_name . '</strong>: ' . join($lang_list, ', ') . '</li>';
		}
		echo '</ul>';
	}
	
	public function option_default_language() {
		
		$lang_pack = $this->default_param['lang-pack'];
		$lang_list = $this->default_param['lang-list'];
		
		// Get first key
		if ($this->options['lang-pack'])
		{
			$pack_default = $this->options['lang-pack'];
		} else {
			$keys = array_keys($lang_pack);
			$pack_default = $keys[0];
		}
			
		$langs = $lang_list[$pack_default];
		echo '<select name="' . APHSH_OPTION . '[default-lang]" id="opt-aphsh-default-lang">';
		foreach($langs as $lang => $lang_name)
		{
			$selected = $this->options['default-lang'] == $lang ? ' selected="selected"' : '';
			echo '<option value="'.$lang.'"' . $selected . '>' . $lang_name . '</option>';
		}
		echo '</select>
				<p class="description">Default language in the code editor\'s drop down menu</p>';
		
		// Other list of language, hidden, displayed when the language pack changed;
		echo '<span id="aphsh-json-lang-list" style="display:none">'.json_encode($this->default_param['lang-list']).'</span>
				<span id="aphsh-json-user-options" style="display:none">'.json_encode($this->options).'</span>';
	}
	
	public function option_theme_list() {
		
		$theme_list = $this->default_param['themes'];
		
		$options = '<select name="' . APHSH_OPTION . '[theme]">';
		foreach($theme_list as $theme => $theme_name)
		{
			$selected = $this->options['theme'] == $theme ? ' selected="selected"' : '';
			$options .= '<option value="'.$theme.'"' . $selected . '>' . $theme_name . '</option>';
		}
		$options .= '</select>';
		return $options;
	}
	
	public function option_plugin_options() {
		$options = $this->options['options'];
		?>
		<p>
			<label for="aphsh-gutter">
				<?php $checked = $this->options['gutter'] == 1 ? ' checked="checked"' : ''; ?>
				<input type="checkbox" name="<?php echo APHSH_OPTION?>[gutter]" id="aphsh-gutter" value="1"<?php echo $checked?>/>
				Show line numbers
			</label>
		</p>
		<p>
			<label for="aphsh-auto-links">
				<?php $checked = $this->options['auto-links'] == 1 ? ' checked="checked"' : ''; ?>
				<input type="checkbox" name="<?php echo APHSH_OPTION?>[auto-links]" id="aphsh-auto-links" value="1"<?php echo $checked?>/>
				Make all url links in the code clickable
			</label>
		</p>
		<p>
			<label for="aphsh-smart-tabs">
				<?php $checked = $this->options['smart-tabs'] == 1 ? ' checked="checked"' : ''; ?>
				<input type="checkbox" name="<?php echo APHSH_OPTION?>[smart-tabs]" id="aphsh-smart-tabs" value="1"<?php echo $checked?>/>
				Use smart tabs feature
			</label>
		</p>
		<?php
	}
	
	public function submit_validation($inputs)
	{
		$token = time();
		$inputs['token'] = $token;
		
		if (key_exists('aphsh-defaults', $_POST))
		{
			$inputs = $this->data_options;
		} else {
			foreach ($this->options as $key => $val)
			{
				if (!key_exists($key, $inputs))
				{
					$inputs[$key] = 0;
				}
			}
		}
		return $inputs;
	}
}

?>