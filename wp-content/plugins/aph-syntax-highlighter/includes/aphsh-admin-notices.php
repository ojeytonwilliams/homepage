<?php
class Aphsh_Admin_Notices
{
	public $admin_notice;
	
    public function __construct()
    {
		$this->admin_notice = get_option(APHSH_OPTION_NOTICE, array());
		// register_activation_hook ( APHPH_PLUGIN_PATH . APHPH_DS . APHPH_PLUGIN_FILE_NAME, array($this, 'activate_plugin') );
		$this->activate_plugin();
		add_action('admin_notices', array($this, 'display_notices'));
    }
	
	public function activate_plugin()
	{
		if (!$this->admin_notice) {
			update_option(APHSH_OPTION_NOTICE, array());
		}
	}
	
	/**
		Store notice to database
		@$type success, error, info
	*/
	public function add_notice($msg, $type = 'success', $display_once = true, $dismissible = false)
	{
		$new_notice = array('msg' => $msg,
							'type' => $type,
							'display_once' => $display_once,
							'dismissible' => $dismissible
						);
		
		if (!in_array($new_notice, $this->admin_notice)) {
			$this->admin_notice[] = $new_notice;
			$this->update_option();
		}
	}
	
	
	public function display_notices()	
	{
		foreach ($this->admin_notice as $key => $notice)
		{
			$dismissible = $notice['dismissible'] ? ' is-dismissible' : '';
			$always_display = !$notice['display_once'] ? ' aphsh-always-display-notice' : '';
			echo '<div class="notice notice-' . $notice['type'] . $dismissible. $always_display . '">
					<p>' . $notice['msg'] . '</p>
				</div>';
					
			if ($notice['display_once'])
				unset($this->admin_notice[$key]);
		}
		
		$this->update_option();
	}
	
	public function delete_notice($msg)
	{
		foreach ($this->admin_notice as $key => $notice)
		{
			if ($notice['msg'] == $msg)
				unset($this->admin_notice[$key]);
		}
		
		$this->update_option();
	}
	
	private function update_option()
	{
		update_option(APHSH_OPTION_NOTICE, $this->admin_notice);
	}
}
?>