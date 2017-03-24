<?php
class Aphsh_Admin_Editor 
{
	private $options;
	private $aphsh_data;
	
	public function __construct()
	{
		add_action( 'edit_form_after_title', array($this, 'add_modal_dialog') );
				
		/** 
			ADD icon button to tinyMCE toolbar and add functionality
			to be able to show pop up window of APH Syntax Highlighter
		*/
		add_action( 'init', array($this, 'aphsh_button') );
		
		/**
			EMBED style to tinymce textarea-iframe editor (visual editor)
			we can not add it using wp_enqueue_style
		*/
		add_filter( 'mce_css', array($this, 'aphsh_tinymce_editor_css') );
		
		/**
			Add style to APH Syntax Highlighter pop up windows
		*/
		add_action( 'admin_enqueue_scripts', array($this, 'register_scripts_post') );
		
		
		/**
		 * Add dialog and necessari scripts to admin edit comments page
		*/
		add_action( 'current_screen', array($this, 'register_scripts_comments'), 10, 2 );		
		add_action( 'current_screen', array($this, 'admin_comments_form') );
		
		/**
		 * When the cursor inside the <pre> tag, then the tag will be highlighted using a class named aphsh-pretag-focused
		 * so we need to remove it before save into database
		*/
		add_action( 'content_save_pre', array($this, 'clean_tag'), 10, 2 );
		
		$this->options = get_option(APHSH_OPTION);
		$this->aphsh_data = get_option(APHSH_OPTION_SHDATA);
	}
	
	public function admin_comments_form ($screen)
	{
		if ($screen->id == 'edit-comments')
		{
			$this->add_modal_dialog();
		}
	}
	
	public function add_modal_dialog() 
	{
		$lang_list = $this->aphsh_data['lang-list'][$this->options['lang-pack']];
		foreach ($lang_list as $lang => $lang_name)
		{
			$selected = $lang == $this->options['default-lang'] ? ' selected="selected"' : '';
			$lang_options .= '<option value="' . $lang . '"' . $selected . '>' . $lang_name . '</option>';
		}
		
		echo '
		<div class="aphsh-overlay" id="aphsh-editor-overlay"></div>
		<div class="aphsh-editor-wrap" id="aphsh-editor-wrap">
			<div class="aphsh-editor-title" id="aphsh-editor-title">
				APH Syntax Highlighter
				<button type="button" class="aphsh-editor-closebtn">
			</div>
			<div class="aphsh-editor-body" id="aphsh-editor-body">
				<div class="aphsh-inline-options aphsh-clearfix">
					<span>Language</span>
					<select name="aphsh-language" id="aphsh-language">'. 
						$lang_options . '
					</select>
					<span class="aphsh-te-section">Highlight Line</span>
					<input type="text" class="aphsh-small-text" name="aphsh_highlight_lines" id="aphsh-highlight-lines"/><span class="description">e.q: 1,2,3-6</span>
					
				</div>
				<textarea placeholder="Code..." class="aphsh-editor-code" id="aphsh-editor-code"></textarea>
				<h2 id="aphsh-other-options"><i class="aphsh-icon-circle-down"></i>Other Options</h2>
				<div id="aphsh-other-options-container" style="display:none">
					<table class="aphsh-options-bottom">
						<tr>
							<td>Title</td>
							<td><input type="text" name="aphsh-title" id="aphsh-title"></td>
						</tr>
						<tr>
							<td>Add Class</td>
							<td><input type="text" class="medium-text" name="aphsh_input_class_name" id="aphsh-input-class-name"/><span class="description">*) Without space</span></td>
						</tr>
						<tr>
							<td>HTMLScript</td>
							<td>
								<select name="aphsh_html_script" id="aphsh-html-script">
									<option value="false">No</option>
									<option value="true">Yes</option>
								</select><span class="description">Mixed HTML with Other Code
							</td>
						</tr>
					</table>
					<h2 class="aphsh-small-title">Override Default Options</h2>
					<table class="aphsh-override-options">
						<tr>
							<th>Override</th>
							<th>Option</th>
						</tr>
						<tr>
							<td>
								<input type="checkbox" name="aphsh_overr_showln" id="aphsh-overr-showln"/>
							</td>
							<td>
								<span>Show Line Numbers:</span> 
								<select name="aphsh_opt_showln" id="aphsh-opt-showln">
									<option value="false">No</option>
									<option value="true">Yes</option>
								</select>
								Start Number: <input type="text" class="aphsh-small-text" name="aphsh_start_number" id="aphsh-start-number" value="1"/></td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" name="aphsh_overr_auto_links" id="aphsh-overr-auto-links"/>
							</td>
							<td>
								<span>Auto Links</span> 
								<select name="aphsh_opt_auto_link" id="aphsh-opt-auto-links">
									<option value="false">No</option>
									<option value="true">Yes</option>
								</select>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="aphsh-editor-submitbox">
				<div class="aphsh-cancel-btn">
					<input type="button" class="button" id="aphsh-cancel" value="Cancel"/>
				</div>
				<div class="aphsh-insert-btn">
					<input type="button" value="Insert Code" class="button button-primary" id="aphsh-submit">
				</div>
			</div>
		</div>';
		
		/* Options, used to change the language dropdown menu to default value, 
		 * we don,t use global variable, so we use this
		*/	
		echo '<span id="aphsh-json-user-options" style="display:none">'.json_encode($this->options).'</span>';
	}

	public function aphsh_button() 
	{
		add_filter( 'mce_external_plugins', array($this, 'aphsh_add_buttons') );
		add_filter( 'mce_buttons', array($this, 'aphsh_register_buttons') );
	}
	
	public function aphsh_add_buttons( $plugin_array ) {
		$plugin_array['aphsh_tinymce_btn'] = APHSH_PLUGIN_URL . '/js/aphsh-tinymce.js?r='.time();
		return $plugin_array;
	}
	
	public function aphsh_register_buttons( $buttons ) {
		array_push( $buttons, 'aphsh');
		return $buttons;
	}
	
	private function register_scripts() {
		wp_enqueue_style('aphsh-code-editor', APHSH_PLUGIN_URL . '/css/aphsh-code-editor.css');
		wp_enqueue_style('aphsh-icomoon', APHSH_PLUGIN_URL . '/css/icomoon/style.css');
		wp_enqueue_script('aphsh-taboverride', APHSH_PLUGIN_URL . '/js/taboverride/taboverride.min.js');
		wp_enqueue_script('aphsh-admin-editor', APHSH_PLUGIN_URL . '/js/aphsh-admin-editor.js', 'jquery', '');
	}
	
	// Add editor to admin comment
	public function register_scripts_comments($screen) {
		if ($screen->id == 'edit-comments') {
			$this->register_scripts();
		}		
	}
	
	// Add editor to add or edit post / page
	public function register_scripts_post($hook) {
		if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
			$this->register_scripts();
		}
	}
	
	public function aphsh_tinymce_editor_css($wp) {
		$wp .= ',' . APHSH_PLUGIN_URL . '/css/aphsh-tinymce-editor.css';
		return $wp;
	}

	public function clean_tag($content)
	{
		return preg_replace('/\s*aphsh-pretag-focused\s*/', '', $content);
	}
}