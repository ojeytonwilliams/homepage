<?php

	// .54.3: WooCommerce custom login page|form.
	// WooCommerce is Activated check:
	// If not activated then use the standard WP Login processing if enable checkboxes are checked, otherwise someone will not be able to login.
	// This also continues to protect the Standard WP Login page no matter what option settings are chosen.
	// This also prevents someone who does not have WooCommerce installed from not being able to login if they check the Enable WooCommerce options.
	// This also allows someone with WooCommerce installed just to turn LSM on or off without messing with the Enable WooCommerce options.
	// Note: There is no need for an LSM Off condition like BPS Pro has because JTC is not involved in the equation - If LSM is Off then the filter is not processed.
	$BPSoptions = get_option('bulletproof_security_options_login_security');
	$plugin_var = 'woocommerce/woocommerce.php';
	$return_var = in_array( $plugin_var, apply_filters('active_plugins', get_option('active_plugins')));

	if ( $return_var == 1 ) {
		
		if ( $BPSoptions['bps_enable_lsm_woocommerce'] == 1 ) {
		
			if ( $BPSoptions['bps_login_security_OnOff'] == 'On' && isset( $_POST['login'] ) || $BPSoptions['bps_login_security_OnOff'] == 'On' && isset( $_POST['wp-submit'] ) ) {
				add_filter( 'authenticate', 'bpsPro_wp_authenticate_username_password', 20, 3 );
			}		
		
		} elseif ( $BPSoptions['bps_enable_lsm_woocommerce'] != 1 ) {

			if ( $BPSoptions['bps_login_security_OnOff'] == 'On' && isset( $_POST['wp-submit'] ) ) {
				add_filter( 'authenticate', 'bpsPro_wp_authenticate_username_password', 20, 3 );
			}	
		
		} else {
			
			// Standard WP Login: LSM Processing
			if ( $BPSoptions['bps_login_security_OnOff'] == 'On' && isset( $_POST['wp-submit'] ) ) {
				add_filter( 'authenticate', 'bpsPro_wp_authenticate_username_password', 20, 3 );
			}		
		}

	} else {
		
		// Standard WP Login: LSM Processing
		if ( $BPSoptions['bps_login_security_OnOff'] == 'On' && isset( $_POST['wp-submit'] ) ) {
			add_filter( 'authenticate', 'bpsPro_wp_authenticate_username_password', 20, 3 );
		}		
	}

function bpsPro_wp_authenticate_username_password( $user, $username, $password ) {
global $wpdb, $blog_id;
$BPSoptions = get_option('bulletproof_security_options_login_security');
$options = get_option('bulletproof_security_options_email');
$bpspro_login_table = $wpdb->prefix . "bpspro_login_security";
$ip_address = esc_html( $_SERVER['REMOTE_ADDR'] );
$hostname = esc_html( @gethostbyaddr($_SERVER['REMOTE_ADDR'] ) );
$request_uri = esc_html( $_SERVER['REQUEST_URI'] );
$login_time = time();
$lockout_time = time() + (60 * $BPSoptions['bps_lockout_duration']); // default is 1 hour/3600 seconds 
$timeNow = time();
$gmt_offset = get_option( 'gmt_offset' ) * 3600;
$bps_email_to = $options['bps_send_email_to'];
$bps_email_from = $options['bps_send_email_from'];
$bps_email_cc = $options['bps_send_email_cc'];
$bps_email_bcc = $options['bps_send_email_bcc'];
$justUrl = get_site_url();
$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' - ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $bps_email_from, 'Cc: ' . $bps_email_cc, 'Bcc: ' . $bps_email_bcc );
$subject = " BPS Login Security Alert - $timestamp ";

/*
***************************************************************
// Log All Account Logins for valid Users - Good and Bad Logins
***************************************************************
*/
if ( $BPSoptions['bps_login_security_OnOff'] == 'On' && $BPSoptions['bps_login_security_logging'] == 'logAll') {

	$user = get_user_by( 'login', $username );
		
		// .53.8: Login by email address
		if ( ! $user && strpos( $username, '@' ) ) {
			$user = get_user_by( 'email', $username );
		}
		
		$LoginSecurityRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE user_id = %d", $user->ID) );

		foreach ( $LoginSecurityRows as $row ) {
	
			if ( $row->status == 'Locked' && $timeNow < $row->lockout_time && $row->failed_logins >= $BPSoptions['bps_max_logins'] && $BPSoptions['bps_login_security_errors'] != 'genericAll') { 
				$error = new WP_Error();
				$error->add('locked_account', '<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' This user account has been locked until ', 'bulletproof-security').'<strong>'.date_i18n(get_option('date_format').' '.get_option('time_format'), $row->lockout_time + $gmt_offset).'</strong>'.__(' due to too many failed login attempts. You can login again after the Lockout Time above has expired.', 'bulletproof-security') );
		
				return $error;
			}
			
			if ( $row->status == 'Locked' && $timeNow < $row->lockout_time && $row->failed_logins >= $BPSoptions['bps_max_logins'] && $BPSoptions['bps_login_security_errors'] == 'genericAll') { 
				return new WP_Error('incorrect_password', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' Invalid Entry.', 'bulletproof-security').' <a href="%s">'.__('Lost your password?', 'bulletproof-security').'</a>', wp_lostpassword_url()));
			}
		}

		// Good Login - DB Row does NOT Exist - Create it - Email option - Any user logs in
		if ( $wpdb->num_rows == 0 && $user->ID != 0 && wp_check_password($password, $user->user_pass, $user->ID) ) {
			$status = 'Not Locked';
			$lockout_time = '0';		
			$failed_logins ='0';
		
			if ( $insert_rows = $wpdb->insert( $bpspro_login_table, array( 'status' => $status, 'user_id' => $user->ID, 'username' => $user->user_login, 'public_name' => $user->display_name, 'email' => $user->user_email, 'role' => $user->roles[0], 'human_time' => current_time('mysql'), 'login_time' => $login_time, 'lockout_time' => $lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $ip_address, 'hostname' => $hostname, 'request_uri' => $request_uri ) ) ) {
			
			// Network/Multisite subsites - logging is not used/allowed
			if ( is_multisite() && $blog_id != 1 ) {
				// do nothing
			} else {			
			
			if ( $options['bps_login_security_email'] == 'anyUserLoginLock') {
				$message = '<p><font color="blue"><strong>'.__('A User has logged in on website: ', 'bulletproof-security').$justUrl.'</strong></font></p>';
				$message .= '<p>'.__('To take further action go to the Login Security page. If you do not want to receive further email alerts change or turn off Login Security Email Alerts.', 'bulletproof-security').'</p>';
				$message .= '<p><strong>'.__('Username:', 'bulletproof-security').'</strong> '.$user->user_login.'</p>'; 
				$message .= '<p><strong>'.__('Status:', 'bulletproof-security').'</strong> '.$status.'</p>'; 
				$message .= '<p><strong>'.__('User Role:', 'bulletproof-security').'</strong> '.$user->roles[0].'</p>'; 
				$message .= '<p><strong>'.__('Email:', 'bulletproof-security').'</strong> '.$user->user_email.'</p>'; 
				$message .= '<p><strong>'.__('User IP Address:', 'bulletproof-security').'</strong> '.$ip_address.'</p>'; 
				$message .= '<p><strong>'.__('User Hostname:', 'bulletproof-security').'</strong> '.$hostname.'</p>'; 
				$message .= '<p><strong>'.__('Request URI:', 'bulletproof-security').'</strong> '.$request_uri.'</p>'; 
				$message .= '<p><strong>'.__('Website:', 'bulletproof-security').'</strong> '.$justUrl.'</p>';

				wp_mail($bps_email_to, $subject, $message, $headers);
			}
			
			// Option adminLoginOnly - Send Email Alert if an Administrator Logs in
			if ( $options['bps_login_security_email'] == 'adminLoginOnly' || $options['bps_login_security_email'] == 'adminLoginLock' && $user->roles[0] == 'administrator') {
				$message = '<p><font color="blue"><strong>'.__('An Administrator has logged in on website: ', 'bulletproof-security').$justUrl.'</strong></font></p>';
				$message .= '<p>'.__('To take further action go to the Login Security page. If you do not want to receive further email alerts change or turn off Login Security Email Alerts.', 'bulletproof-security').'</p>';
				$message .= '<p><strong>'.__('Username:', 'bulletproof-security').'</strong> '.$user->user_login.'</p>'; 
				$message .= '<p><strong>'.__('Status:', 'bulletproof-security').'</strong> '.$status.'</p>'; 
				$message .= '<p><strong>'.__('User Role:', 'bulletproof-security').'</strong> '.$user->roles[0].'</p>'; 
				$message .= '<p><strong>'.__('Email:', 'bulletproof-security').'</strong> '.$user->user_email.'</p>'; 
				$message .= '<p><strong>'.__('User IP Address:', 'bulletproof-security').'</strong> '.$ip_address.'</p>'; 
				$message .= '<p><strong>'.__('User Hostname:', 'bulletproof-security').'</strong> '.$hostname.'</p>'; 
				$message .= '<p><strong>'.__('Request URI:', 'bulletproof-security').'</strong> '.$request_uri.'</p>'; 
				$message .= '<p><strong>'.__('Website:', 'bulletproof-security').'</strong> '.$justUrl.'</p>';

				wp_mail($bps_email_to, $subject, $message, $headers);
			}
			} // end if ( is_multisite() && $blog_id != 1
			} // end if ( $insert_rows = $wpdb->insert...
		} // end if ( $wpdb->num_rows == 0...
		
		// Good Login - DB Row Exists - Insert another DB Row - Only insert a new DB row if user status is not Locked
		if ( $wpdb->num_rows != 0 && $user->ID != 0 && wp_check_password($password, $user->user_pass, $user->ID) && $row->status != 'Locked') {
			$status = 'Not Locked';
			$lockout_time = '0';		
			$failed_logins ='0';		
			
			if ( $insert_rows = $wpdb->insert( $bpspro_login_table, array( 'status' => $status, 'user_id' => $user->ID, 'username' => $user->user_login, 'public_name' => $user->display_name, 'email' => $user->user_email, 'role' => $user->roles[0], 'human_time' => current_time('mysql'), 'login_time' => $login_time, 'lockout_time' => $lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $ip_address, 'hostname' => $hostname, 'request_uri' => $request_uri ) ) ) {
			
			// Network/Multisite subsites - logging is not used/allowed
			if ( is_multisite() && $blog_id != 1 ) {
				// do nothing
			} else {	

			if ( $options['bps_login_security_email'] == 'anyUserLoginLock') {
				$message = '<p><font color="blue"><strong>'.__('Test A User has logged in on website: ', 'bulletproof-security').$justUrl.'</strong></font></p>';
				$message .= '<p>'.__('To take further action go to the Login Security page. If you do not want to receive further email alerts change or turn off Login Security Email Alerts.', 'bulletproof-security').'</p>';
				$message .= '<p><strong>'.__('Username:', 'bulletproof-security').'</strong> '.$user->user_login.'</p>'; 
				$message .= '<p><strong>'.__('Status:', 'bulletproof-security').'</strong> '.$status.'</p>'; 
				$message .= '<p><strong>'.__('User Role:', 'bulletproof-security').'</strong> '.$user->roles[0].'</p>'; 
				$message .= '<p><strong>'.__('Email:', 'bulletproof-security').'</strong> '.$user->user_email.'</p>'; 
				$message .= '<p><strong>'.__('User IP Address:', 'bulletproof-security').'</strong> '.$ip_address.'</p>'; 
				$message .= '<p><strong>'.__('User Hostname:', 'bulletproof-security').'</strong> '.$hostname.'</p>'; 
				$message .= '<p><strong>'.__('Request URI:', 'bulletproof-security').'</strong> '.$request_uri.'</p>'; 
				$message .= '<p><strong>'.__('Website:', 'bulletproof-security').'</strong> '.$justUrl.'</p>';

				wp_mail($bps_email_to, $subject, $message, $headers);
			}
			
			// Option adminLoginOnly - Send Email Alert if an Administrator Logs in
			if ( $options['bps_login_security_email'] == 'adminLoginOnly' || $options['bps_login_security_email'] == 'adminLoginLock' && $user->roles[0] == 'administrator') {
				$message = '<p><font color="blue"><strong>'.__('An Administrator has logged in on website: ', 'bulletproof-security').$justUrl.'</strong></font></p>';
				$message .= '<p>'.__('To take further action go to the Login Security page. If you do not want to receive further email alerts change or turn off Login Security Email Alerts.', 'bulletproof-security').'</p>';				
				$message .= '<p><strong>'.__('Username:', 'bulletproof-security').'</strong> '.$user->user_login.'</p>'; 
				$message .= '<p><strong>'.__('Status:', 'bulletproof-security').'</strong> '.$status.'</p>'; 
				$message .= '<p><strong>'.__('User Role:', 'bulletproof-security').'</strong> '.$user->roles[0].'</p>'; 
				$message .= '<p><strong>'.__('Email:', 'bulletproof-security').'</strong> '.$user->user_email.'</p>'; 
				$message .= '<p><strong>'.__('User IP Address:', 'bulletproof-security').'</strong> '.$ip_address.'</p>'; 
				$message .= '<p><strong>'.__('User Hostname:', 'bulletproof-security').'</strong> '.$hostname.'</p>'; 
				$message .= '<p><strong>'.__('Request URI:', 'bulletproof-security').'</strong> '.$request_uri.'</p>'; 
				$message .= '<p><strong>'.__('Website:', 'bulletproof-security').'</strong> '.$justUrl.'</p>'; 
				
				wp_mail($bps_email_to, $subject, $message, $headers);
			}
			} // end if ( is_multisite() && $blog_id != 1
			} // end if ( $insert_rows = $wpdb->insert...
		} // end if ( $wpdb->num_rows != 0...

		// Bad Login - DB Row does NOT Exist - First bad login attempt = $failed_logins = '1'; - Insert a new Row with Locked status
		if ( $wpdb->num_rows == 0 && $user->ID != 0 && ! wp_check_password($password, $user->user_pass, $user->ID) ) {
			$failed_logins = '1';

			// Insane, but someone will do this... if max bad retries is set to 1
			if ( $failed_logins >= $BPSoptions['bps_max_logins'] ) {
				$status = 'Locked';

			// Network/Multisite subsites - logging is not used/allowed
			if ( is_multisite() && $blog_id != 1 ) {
				// do nothing
			} else {

			if ( $options['bps_login_security_email'] == 'lockoutOnly' || $options['bps_login_security_email'] == 'anyUserLoginLock' || $options['bps_login_security_email'] == 'adminLoginLock') {
				$message = '<p><font color="#fb0101"><strong>'.__('A User Account has been locked on website: ', 'bulletproof-security').$justUrl.'</strong></font></p>';
				$message .= '<p>'.__('To take further action go to the Login Security page. If no action is taken then the User will be able to try and login again after the Lockout Time has expired. If you do not want to receive further email alerts change or turn off Login Security Email Alerts.', 'bulletproof-security').'</p>';
				$message .= '<p><strong>'.__('What to do if your User Account is locked and you are unable to login to your website:', 'bulletproof-security').'</strong>'.__(' Use FTP or your web host control panel file manager and rename the /bulletproof-security plugin folder name to /_bulletproof-security. Log into your website. Rename the /_bulletproof-security plugin folder name back to /bulletproof-security. Go to the BPS Login Security page and unlock your User Account.', 'bulletproof-security').'</p>';
				$message .= '<p><strong>'.__('What to do if your User Account is being locked repeatedly:', 'bulletproof-security').'</strong>'.__(' Additional things that you can do to protect publicly displayed usernames, not exposing author names/user account names, etc.', 'bulletproof-security').': https://forum.ait-pro.com/forums/topic/user-account-locked/#post-12634</p>';

				$message .= '<p><strong>'.__('Username:', 'bulletproof-security').'</strong> '.$user->user_login.'</p>'; 
				$message .= '<p><strong>'.__('Status:', 'bulletproof-security').'</strong> '.$status.'</p>'; 
				$message .= '<p><strong>'.__('User Role:', 'bulletproof-security').'</strong> '.$user->roles[0].'</p>'; 
				$message .= '<p><strong>'.__('Email:', 'bulletproof-security').'</strong> '.$user->user_email.'</p>'; 
				$message .= '<p><strong>'.__('Lockout Time:', 'bulletproof-security').'</strong> '.date_i18n(get_option('date_format').' '.get_option('time_format'), $login_time + $gmt_offset).'</p>'; 
				$message .= '<p><strong>'.__('Lockout Time Expires:', 'bulletproof-security').'</strong> '.date_i18n(get_option('date_format').' '.get_option('time_format'), $lockout_time + $gmt_offset).'</p>'; 
				$message .= '<p><strong>'.__('User IP Address:', 'bulletproof-security').'</strong> '.$ip_address.'</p>'; 
				$message .= '<p><strong>'.__('User Hostname:', 'bulletproof-security').'</strong> '.$hostname.'</p>'; 
				$message .= '<p><strong>'.__('Request URI:', 'bulletproof-security').'</strong> '.$request_uri.'</p>'; 
				$message .= '<p><strong>'.__('Website:', 'bulletproof-security').'</strong> '.$justUrl.'</p>';

				wp_mail($bps_email_to, $subject, $message, $headers);
			}
			}
			
			} else {		
				$status = 'Not Locked';			
			}

			if ( $insert_rows = $wpdb->insert( $bpspro_login_table, array( 'status' => $status, 'user_id' => $user->ID, 'username' => $user->user_login, 'public_name' => $user->display_name, 'email' => $user->user_email, 'role' => $user->roles[0], 'human_time' => current_time('mysql'), 'login_time' => $login_time, 'lockout_time' => $lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $ip_address, 'hostname' => $hostname, 'request_uri' => $request_uri ) ) ) {	

			} // end $insert_rows = $wpdb->insert...
		} // end if ( $wpdb->num_rows == 0...	

		// Good Login - DB Row Exists - Reset locked out account on good login if it was locked and the lockout has expired
		if ( $wpdb->num_rows != 0 && $user->ID != 0 && wp_check_password($password, $user->user_pass, $user->ID) && $row->status == 'Locked' && $timeNow > $row->lockout_time ) {				
				$status = 'Not Locked';			
				$lockout_time = '0';
				$failed_logins = '0';

			// .51.8: additional WHERE clause added: 'status' => 'Locked' - Update ONLY the Row that has status of Locked.
			// maybe later version keep this row and reset the status and failed login attempts only and create a new row for the new login - not critical
			if ( $update_rows = $wpdb->update( $bpspro_login_table, array( 'status' => $status, 'user_id' => $row->user_id, 'username' => $row->username, 'public_name' => $row->public_name, 'email' => $row->email, 'role' => $row->role, 'human_time' => current_time('mysql'), 'login_time' => $login_time, 'lockout_time' => $lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $row->ip_address, 'hostname' => $row->hostname, 'request_uri' => $row->request_uri ), array( 'user_id' => $row->user_id, 'status' => 'Locked' ) ) ) {	

			// Network/Multisite subsites - logging is not used/allowed
			if ( is_multisite() && $blog_id != 1 ) {
				// do nothing
			} else {

			if ( $options['bps_login_security_email'] == 'anyUserLoginLock') {
				$message = '<p><font color="blue"><strong>'.__('A User has logged in on website: ', 'bulletproof-security').$justUrl.'</strong></font></p>';
				$message .= '<p>'.__('To take further action go to the Login Security page. If you do not want to receive further email alerts change or turn off Login Security Email Alerts.', 'bulletproof-security').'</p>';
				$message .= '<p><strong>'.__('Username:', 'bulletproof-security').'</strong> '.$user->user_login.'</p>'; 
				$message .= '<p><strong>'.__('Status:', 'bulletproof-security').'</strong> '.$status.'</p>'; 
				$message .= '<p><strong>'.__('User Role:', 'bulletproof-security').'</strong> '.$user->roles[0].'</p>'; 
				$message .= '<p><strong>'.__('Email:', 'bulletproof-security').'</strong> '.$user->user_email.'</p>'; 
				$message .= '<p><strong>'.__('User IP Address:', 'bulletproof-security').'</strong> '.$ip_address.'</p>'; 
				$message .= '<p><strong>'.__('User Hostname:', 'bulletproof-security').'</strong> '.$hostname.'</p>'; 
				$message .= '<p><strong>'.__('Request URI:', 'bulletproof-security').'</strong> '.$request_uri.'</p>'; 
				$message .= '<p><strong>'.__('Website:', 'bulletproof-security').'</strong> '.$justUrl.'</p>'; 

				wp_mail($bps_email_to, $subject, $message, $headers);
			}
				
			// Option adminLoginOnly - Send Email Alert if an Administrator Logs in
			if ( $options['bps_login_security_email'] == 'adminLoginOnly' || $options['bps_login_security_email'] == 'adminLoginLock' && $user->roles[0] == 'administrator') {
				$message = '<p><font color="blue"><strong>'.__('An Administrator has logged in on website: ', 'bulletproof-security').$justUrl.'</strong></font></p>';
				$message .= '<p>'.__('To take further action go to the Login Security page. If you do not want to receive further email alerts change or turn off Login Security Email Alerts.', 'bulletproof-security').'</p>';				
				$message .= '<p><strong>'.__('Username:', 'bulletproof-security').'</strong> '.$user->user_login.'</p>'; 
				$message .= '<p><strong>'.__('Status:', 'bulletproof-security').'</strong> '.$status.'</p>'; 
				$message .= '<p><strong>'.__('User Role:', 'bulletproof-security').'</strong> '.$user->roles[0].'</p>'; 
				$message .= '<p><strong>'.__('Email:', 'bulletproof-security').'</strong> '.$user->user_email.'</p>'; 
				$message .= '<p><strong>'.__('User IP Address:', 'bulletproof-security').'</strong> '.$ip_address.'</p>'; 
				$message .= '<p><strong>'.__('User Hostname:', 'bulletproof-security').'</strong> '.$hostname.'</p>'; 
				$message .= '<p><strong>'.__('Request URI:', 'bulletproof-security').'</strong> '.$request_uri.'</p>'; 
				$message .= '<p><strong>'.__('Website:', 'bulletproof-security').'</strong> '.$justUrl.'</p>'; 

				wp_mail($bps_email_to, $subject, $message, $headers);
			}
			} // end if ( is_multisite() && $blog_id != 1
			} // end if ( $update_rows = $wpdb->update...
		} // end if ( $wpdb->num_rows != 0...

		// Bad Login - DB Row Exists - Count bad login attempts and Lock Account
		if ( $wpdb->num_rows != 0 && $user->ID != 0 && ! wp_check_password($password, $user->user_pass, $user->ID) ) {

			foreach ( $LoginSecurityRows as $row ) {

				if ( $row->status == 'Locked' && $timeNow < $row->lockout_time && $row->failed_logins >= $BPSoptions['bps_max_logins'] ) { // greater > for testing
					$error = new WP_Error();
					$error->add('locked_account', '<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' This user account has been locked until ', 'bulletproof-security').'<strong>'.date_i18n(get_option('date_format').' '.get_option('time_format'), $row->lockout_time + $gmt_offset).'</strong>'.__(' due to too many failed login attempts. You can login again after the Lockout Time above has expired.', 'bulletproof-security') );

					return $error;
				}
					$failed_logins = $row->failed_logins;

				if ( $row->failed_logins == 0 ) {
					for ($failed_logins = 0; $failed_logins <= 0; $failed_logins++) {
    					$failed_logins;
						// .51.8: added $remaining variables
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					} 
				} elseif ( $row->failed_logins == 1 ) {
					for ($failed_logins = 1; $failed_logins <= 1; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 2 ) {
					for ($failed_logins = 2; $failed_logins <= 2; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 3 ) {
					for ($failed_logins = 3; $failed_logins <= 3; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 4 ) {
					for ($failed_logins = 4; $failed_logins <= 4; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 5 ) {
					for ($failed_logins = 5; $failed_logins <= 5; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 6 ) {
					for ($failed_logins = 6; $failed_logins <= 6; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 7 ) {
					for ($failed_logins = 7; $failed_logins <= 7; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 8 ) {
					for ($failed_logins = 8; $failed_logins <= 8; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 9 ) {
					for ($failed_logins = 9; $failed_logins <= 9; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				}
			} // end foreach
			
			if ( $failed_logins >= $BPSoptions['bps_max_logins'] ) {
				$status = 'Locked';	

			// Network/Multisite subsites - logging is not used/allowed
			if ( is_multisite() && $blog_id != 1 ) {
				// do nothing
			} else {

			if ( $options['bps_login_security_email'] == 'lockoutOnly' || $options['bps_login_security_email'] == 'anyUserLoginLock' || $options['bps_login_security_email'] == 'adminLoginLock') {
				$message = '<p><font color="#fb0101"><strong>'.__('A User Account has been locked on website: ', 'bulletproof-security').$justUrl.'</strong></font></p>';
				$message .= '<p>'.__('To take further action go to the Login Security page. If no action is taken then the User will be able to try and login again after the Lockout Time has expired. If you do not want to receive further email alerts change or turn off Login Security Email Alerts.', 'bulletproof-security').'</p>';
				$message .= '<p><strong>'.__('What to do if your User Account is locked and you are unable to login to your website:', 'bulletproof-security').'</strong>'.__(' Use FTP or your web host control panel file manager and rename the /bulletproof-security plugin folder name to /_bulletproof-security. Log into your website. Rename the /_bulletproof-security plugin folder name back to /bulletproof-security. Go to the BPS Login Security page and unlock your User Account.', 'bulletproof-security').'</p>';
				$message .= '<p><strong>'.__('What to do if your User Account is being locked repeatedly:', 'bulletproof-security').'</strong>'.__(' Additional things that you can do to protect publicly displayed usernames, not exposing author names/user account names, etc.', 'bulletproof-security').': https://forum.ait-pro.com/forums/topic/user-account-locked/#post-12634</p>';

				$message .= '<p><strong>'.__('Username:', 'bulletproof-security').'</strong> '.$user->user_login.'</p>'; 
				$message .= '<p><strong>'.__('Status:', 'bulletproof-security').'</strong> '.$status.'</p>'; 
				$message .= '<p><strong>'.__('User Role:', 'bulletproof-security').'</strong> '.$user->roles[0].'</p>'; 
				$message .= '<p><strong>'.__('Email:', 'bulletproof-security').'</strong> '.$user->user_email.'</p>'; 
				$message .= '<p><strong>'.__('Lockout Time:', 'bulletproof-security').'</strong> '.date_i18n(get_option('date_format').' '.get_option('time_format'), $login_time + $gmt_offset).'</p>'; 
				$message .= '<p><strong>'.__('Lockout Time Expires:', 'bulletproof-security').'</strong> '.date_i18n(get_option('date_format').' '.get_option('time_format'), $lockout_time + $gmt_offset).'</p>'; 
				$message .= '<p><strong>'.__('User IP Address:', 'bulletproof-security').'</strong> '.$ip_address.'</p>'; 
				$message .= '<p><strong>'.__('User Hostname:', 'bulletproof-security').'</strong> '.$hostname.'</p>'; 
				$message .= '<p><strong>'.__('Request URI:', 'bulletproof-security').'</strong> '.$request_uri.'</p>'; 
				$message .= '<p><strong>'.__('Website:', 'bulletproof-security').'</strong> '.$justUrl.'</p>';

				wp_mail($bps_email_to, $subject, $message, $headers);
			}
			}
			
			} else {	
				$status = 'Not Locked';
			}

			// .51.8: Insert a new row on first bad login attempt. After that update that same row
			if ( $failed_logins == 1 ) {
				
				$insert_rows = $wpdb->insert( $bpspro_login_table, array( 'status' => $status, 'user_id' => $user->ID, 'username' => $user->user_login, 'public_name' => $user->display_name, 'email' => $user->user_email, 'role' => $user->roles[0], 'human_time' => current_time('mysql'), 'login_time' => $login_time, 'lockout_time' => $lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $ip_address, 'hostname' => $hostname, 'request_uri' => $request_uri ) );		
					
			} else {
				
				$no_zeros = '0';
				$LSM_zero_filter = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $bpspro_login_table WHERE user_id = %d AND failed_logins != %d", $user->ID, $no_zeros ) );
				
				$update_rows = $wpdb->update( $bpspro_login_table, array( 'status' => $status, 'user_id' => $row->user_id, 'username' => $row->username, 'public_name' => $row->public_name, 'email' => $row->email, 'role' => $row->role, 'human_time' => current_time('mysql'), 'login_time' => $login_time, 'lockout_time' => $lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $row->ip_address, 'hostname' => $row->hostname, 'request_uri' => $row->request_uri ), array( 'user_id' => $row->user_id, 'failed_logins' => $row->failed_logins ) );

			}
		} // end if ( $wpdb->num_rows != 0...
} // end $BPSoptions['bps_login_security_logging'] == 'logAll') {...

/* 
*******************************************************************************************************************
// Log Only Account Lockouts for valid Users
// X failed attempts in any X amount of time = account is locked period - Duration/threshold is totally unnecessary
*******************************************************************************************************************
*/
if ( $BPSoptions['bps_login_security_OnOff'] == 'On' && $BPSoptions['bps_login_security_logging'] == 'logLockouts') {

	$user = get_user_by( 'login', $username );
	
		// .53.8: Login by email address
		if ( ! $user && strpos( $username, '@' ) ) {
			$user = get_user_by( 'email', $username );
		}	
	
		$LoginSecurityRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE user_id = %d", $user->ID) );

		foreach ( $LoginSecurityRows as $row ) {
	
			if ( $row->status == 'Locked' && $timeNow < $row->lockout_time && $row->failed_logins >= $BPSoptions['bps_max_logins'] && $BPSoptions['bps_login_security_errors'] != 'genericAll') { 
				$error = new WP_Error();
				$error->add('locked_account', '<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' This user account has been locked until ', 'bulletproof-security').'<strong>'.date_i18n(get_option('date_format').' '.get_option('time_format'), $row->lockout_time + $gmt_offset).'</strong>'.__(' due to too many failed login attempts. You can login again after the Lockout Time above has expired.', 'bulletproof-security') );

				return $error;
			}
			
			if ( $row->status == 'Locked' && $timeNow < $row->lockout_time && $row->failed_logins >= $BPSoptions['bps_max_logins'] && $BPSoptions['bps_login_security_errors'] == 'genericAll') { 
				return new WP_Error('incorrect_password', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' Invalid Entry.', 'bulletproof-security').' <a href="%s">'.__('Lost your password?', 'bulletproof-security').'</a>', wp_lostpassword_url()));			
			}
		}

		// Bad Login - DB Row does NOT Exist - First bad login attempt = $failed_logins = '1';
		if ( $wpdb->num_rows == 0 && $user->ID != 0 && ! wp_check_password($password, $user->user_pass, $user->ID) ) {
			$failed_logins = '1';

			// Insane, but someone will do this... if max bad retries is set to 1
			if ( $failed_logins >= $BPSoptions['bps_max_logins'] ) {
				$status = 'Locked';

			// Network/Multisite subsites - logging is not used/allowed
			if ( is_multisite() && $blog_id != 1 ) {
				// do nothing
			} else {

			if ( $options['bps_login_security_email'] == 'lockoutOnly' || $options['bps_login_security_email'] == 'anyUserLoginLock' || $options['bps_login_security_email'] == 'adminLoginLock') {
				$message = '<p><font color="#fb0101"><strong>'.__('A User Account has been locked on website: ', 'bulletproof-security').$justUrl.'</strong></font></p>';
				$message .= '<p>'.__('To take further action go to the Login Security page. If no action is taken then the User will be able to try and login again after the Lockout Time has expired. If you do not want to receive further email alerts change or turn off Login Security Email Alerts.', 'bulletproof-security').'</p>';
				$message .= '<p><strong>'.__('What to do if your User Account is locked and you are unable to login to your website:', 'bulletproof-security').'</strong>'.__(' Use FTP or your web host control panel file manager and rename the /bulletproof-security plugin folder name to /_bulletproof-security. Log into your website. Rename the /_bulletproof-security plugin folder name back to /bulletproof-security. Go to the BPS Login Security page and unlock your User Account.', 'bulletproof-security').'</p>';
				$message .= '<p><strong>'.__('What to do if your User Account is being locked repeatedly:', 'bulletproof-security').'</strong>'.__(' Additional things that you can do to protect publicly displayed usernames, not exposing author names/user account names, etc.', 'bulletproof-security').': https://forum.ait-pro.com/forums/topic/user-account-locked/#post-12634</p>';

				$message .= '<p><strong>'.__('Username:', 'bulletproof-security').'</strong> '.$user->user_login.'</p>'; 
				$message .= '<p><strong>'.__('Status:', 'bulletproof-security').'</strong> '.$status.'</p>'; 
				$message .= '<p><strong>'.__('User Role:', 'bulletproof-security').'</strong> '.$user->roles[0].'</p>'; 
				$message .= '<p><strong>'.__('Email:', 'bulletproof-security').'</strong> '.$user->user_email.'</p>'; 
				$message .= '<p><strong>'.__('Lockout Time:', 'bulletproof-security').'</strong> '.date_i18n(get_option('date_format').' '.get_option('time_format'), $login_time + $gmt_offset).'</p>'; 
				$message .= '<p><strong>'.__('Lockout Time Expires:', 'bulletproof-security').'</strong> '.date_i18n(get_option('date_format').' '.get_option('time_format'), $lockout_time + $gmt_offset).'</p>'; 
				$message .= '<p><strong>'.__('User IP Address:', 'bulletproof-security').'</strong> '.$ip_address.'</p>'; 
				$message .= '<p><strong>'.__('User Hostname:', 'bulletproof-security').'</strong> '.$hostname.'</p>'; 
				$message .= '<p><strong>'.__('Request URI:', 'bulletproof-security').'</strong> '.$request_uri.'</p>'; 
				$message .= '<p><strong>'.__('Website:', 'bulletproof-security').'</strong> '.$justUrl.'</p>';

				wp_mail($bps_email_to, $subject, $message, $headers);
			}
			}
			
			} else {		
				$status = 'Not Locked';			
			}

			if ( $insert_rows = $wpdb->insert( $bpspro_login_table, array( 'status' => $status, 'user_id' => $user->ID, 'username' => $user->user_login, 'public_name' => $user->display_name, 'email' => $user->user_email, 'role' => $user->roles[0], 'human_time' => current_time('mysql'), 'login_time' => $login_time, 'lockout_time' => $lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $ip_address, 'hostname' => $hostname, 'request_uri' => $request_uri ) ) ) {	

			} // end if ( $insert_rows = $wpdb->insert...
		} // end if ( $wpdb->num_rows == 0...	

			// .51.8: Good Login - DB Row Exists - Status == Not Locked - Reset lockout time and failed logins to 0
			// Update all rows for the user id on good login
			if ( $wpdb->num_rows != 0 && $user->ID != 0 && wp_check_password($password, $user->user_pass, $user->ID) && $row->status == 'Not Locked' ) {				

				$update_rows = $wpdb->update( $bpspro_login_table, array( 'lockout_time' => '0', 'failed_logins' => '0' ), array( 'user_id' => $row->user_id ) );		
			}

			// Good Login - DB Row Exists & status is Locked - Reset Only a locked out account on good login if it was locked and the lockout time has expired
			if ( $wpdb->num_rows != 0 && $user->ID != 0 && wp_check_password($password, $user->user_pass, $user->ID) && $row->status == 'Locked' && $timeNow > $row->lockout_time) {				
				$status = 'Not Locked';			
				$lockout_time = '0';
				$failed_logins = '0';

			if ( $update_rows = $wpdb->update( $bpspro_login_table, array( 'status' => $status, 'user_id' => $row->user_id, 'username' => $row->username, 'public_name' => $row->public_name, 'email' => $row->email, 'role' => $row->role, 'human_time' => current_time('mysql'), 'login_time' => $login_time, 'lockout_time' => $lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $row->ip_address, 'hostname' => $row->hostname, 'request_uri' => $row->request_uri ), array( 'user_id' => $row->user_id, 'status' => 'Locked' ) ) ) {	

			// Network/Multisite subsites - logging is not used/allowed
			if ( is_multisite() && $blog_id != 1 ) {
				// do nothing
			} else {

			if ( $options['bps_login_security_email'] == 'anyUserLoginLock') {
				$message = '<p><font color="blue"><strong>'.__('A User has logged in on website: ', 'bulletproof-security').$justUrl.'</strong></font></p>';
				$message .= '<p>'.__('To take further action go to the Login Security page. If you do not want to receive further email alerts change or turn off Login Security Email Alerts.', 'bulletproof-security').'</p>';

				$message .= '<p><strong>'.__('Username:', 'bulletproof-security').'</strong> '.$user->user_login.'</p>'; 
				$message .= '<p><strong>'.__('Status:', 'bulletproof-security').'</strong> '.$status.'</p>'; 
				$message .= '<p><strong>'.__('User Role:', 'bulletproof-security').'</strong> '.$user->roles[0].'</p>'; 
				$message .= '<p><strong>'.__('Email:', 'bulletproof-security').'</strong> '.$user->user_email.'</p>'; 
				$message .= '<p><strong>'.__('User IP Address:', 'bulletproof-security').'</strong> '.$ip_address.'</p>'; 
				$message .= '<p><strong>'.__('User Hostname:', 'bulletproof-security').'</strong> '.$hostname.'</p>'; 
				$message .= '<p><strong>'.__('Request URI:', 'bulletproof-security').'</strong> '.$request_uri.'</p>'; 
				$message .= '<p><strong>'.__('Website:', 'bulletproof-security').'</strong> '.$justUrl.'</p>';

				wp_mail($bps_email_to, $subject, $message, $headers);
			}

			// Option adminLoginOnly - Send Email Alert if an Administrator Logs in
			if ( $options['bps_login_security_email'] == 'adminLoginOnly' || $options['bps_login_security_email'] == 'adminLoginLock' && $user->roles[0] == 'administrator') {
				$message = '<p><font color="blue"><strong>'.__('An Administrator has logged in on website: ', 'bulletproof-security').$justUrl.'</strong></font></p>';
				$message .= '<p>'.__('To take further action go to the Login Security page. If you do not want to receive further email alerts change or turn off Login Security Email Alerts.', 'bulletproof-security').'</p>';

				$message .= '<p><strong>'.__('Username:', 'bulletproof-security').'</strong> '.$user->user_login.'</p>'; 
				$message .= '<p><strong>'.__('Status:', 'bulletproof-security').'</strong> '.$status.'</p>'; 
				$message .= '<p><strong>'.__('User Role:', 'bulletproof-security').'</strong> '.$user->roles[0].'</p>'; 
				$message .= '<p><strong>'.__('Email:', 'bulletproof-security').'</strong> '.$user->user_email.'</p>'; 
				$message .= '<p><strong>'.__('User IP Address:', 'bulletproof-security').'</strong> '.$ip_address.'</p>'; 
				$message .= '<p><strong>'.__('User Hostname:', 'bulletproof-security').'</strong> '.$hostname.'</p>'; 
				$message .= '<p><strong>'.__('Request URI:', 'bulletproof-security').'</strong> '.$request_uri.'</p>'; 
				$message .= '<p><strong>'.__('Website:', 'bulletproof-security').'</strong> '.$justUrl.'</p>';

				wp_mail($bps_email_to, $subject, $message, $headers);
			}			
			} // end if ( is_multisite() && $blog_id != 1
			} // end if ( $update_rows = $wpdb->update...
		} // end if ( $wpdb->num_rows != 0...

		// Bad Login - DB Row Exists - Count bad login attempts and Lock Account
		if ( $wpdb->num_rows != 0 && $user->ID != 0 && ! wp_check_password($password, $user->user_pass, $user->ID) ) {

			foreach ( $LoginSecurityRows as $row ) {

				if ( $row->status == 'Locked' && $timeNow < $row->lockout_time && $row->failed_logins >= $BPSoptions['bps_max_logins'] ) { // greater > for testing
					$error = new WP_Error();
					$error->add('locked_account', '<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' This user account has been locked until ', 'bulletproof-security').'<strong>'.date_i18n(get_option('date_format').' '.get_option('time_format'), $row->lockout_time + $gmt_offset).'</strong>'.__(' due to too many failed login attempts. You can login again after the Lockout Time above has expired.', 'bulletproof-security') );

					return $error;
				}
					$failed_logins = $row->failed_logins;

				if ( $row->failed_logins == 0 ) {
					for ($failed_logins = 0; $failed_logins <= 0; $failed_logins++) {
    					$failed_logins;
						// .51.8: added $remaining variables
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					} 
				} elseif ( $row->failed_logins == 1 ) {
					for ($failed_logins = 1; $failed_logins <= 1; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 2 ) {
					for ($failed_logins = 2; $failed_logins <= 2; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 3 ) {
					for ($failed_logins = 3; $failed_logins <= 3; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 4 ) {
					for ($failed_logins = 4; $failed_logins <= 4; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 5 ) {
					for ($failed_logins = 5; $failed_logins <= 5; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 6 ) {
					for ($failed_logins = 6; $failed_logins <= 6; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 7 ) {
					for ($failed_logins = 7; $failed_logins <= 7; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 8 ) {
					for ($failed_logins = 8; $failed_logins <= 8; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				} elseif ( $row->failed_logins == 9 ) {
					for ($failed_logins = 9; $failed_logins <= 9; $failed_logins++) {
    					$failed_logins;
						$remaining = $BPSoptions['bps_max_logins'] - $failed_logins - 1;
					}
				}
			} // end foreach
			
			if ( $failed_logins >= $BPSoptions['bps_max_logins'] ) {
				$status = 'Locked';

			// Network/Multisite subsites - logging is not used/allowed
			if ( is_multisite() && $blog_id != 1 ) {
				// do nothing
			} else {

			if ( $options['bps_login_security_email'] == 'lockoutOnly' || $options['bps_login_security_email'] == 'anyUserLoginLock' || $options['bps_login_security_email'] == 'adminLoginLock') {
				$message = '<p><font color="#fb0101"><strong>'.__('A User Account has been locked on website: ', 'bulletproof-security').$justUrl.'</strong></font></p>';
				$message .= '<p>'.__('To take further action go to the Login Security page. If no action is taken then the User will be able to try and login again after the Lockout Time has expired. If you do not want to receive further email alerts change or turn off Login Security Email Alerts.', 'bulletproof-security').'</p>';
				$message .= '<p><strong>'.__('What to do if your User Account is locked and you are unable to login to your website:', 'bulletproof-security').'</strong>'.__(' Use FTP or your web host control panel file manager and rename the /bulletproof-security plugin folder name to /_bulletproof-security. Log into your website. Rename the /_bulletproof-security plugin folder name back to /bulletproof-security. Go to the BPS Login Security page and unlock your User Account.', 'bulletproof-security').'</p>';
				$message .= '<p><strong>'.__('What to do if your User Account is being locked repeatedly:', 'bulletproof-security').'</strong>'.__(' Additional things that you can do to protect publicly displayed usernames, not exposing author names/user account names, etc.', 'bulletproof-security').': https://forum.ait-pro.com/forums/topic/user-account-locked/#post-12634</p>';

				$message .= '<p><strong>'.__('Username:', 'bulletproof-security').'</strong> '.$user->user_login.'</p>'; 
				$message .= '<p><strong>'.__('Status:', 'bulletproof-security').'</strong> '.$status.'</p>'; 
				$message .= '<p><strong>'.__('User Role:', 'bulletproof-security').'</strong> '.$user->roles[0].'</p>'; 
				$message .= '<p><strong>'.__('Email:', 'bulletproof-security').'</strong> '.$user->user_email.'</p>'; 
				$message .= '<p><strong>'.__('Lockout Time:', 'bulletproof-security').'</strong> '.date_i18n(get_option('date_format').' '.get_option('time_format'), $login_time + $gmt_offset).'</p>'; 
				$message .= '<p><strong>'.__('Lockout Time Expires:', 'bulletproof-security').'</strong> '.date_i18n(get_option('date_format').' '.get_option('time_format'), $lockout_time + $gmt_offset).'</p>'; 
				$message .= '<p><strong>'.__('User IP Address:', 'bulletproof-security').'</strong> '.$ip_address.'</p>'; 
				$message .= '<p><strong>'.__('User Hostname:', 'bulletproof-security').'</strong> '.$hostname.'</p>'; 
				$message .= '<p><strong>'.__('Request URI:', 'bulletproof-security').'</strong> '.$request_uri.'</p>'; 
				$message .= '<p><strong>'.__('Website:', 'bulletproof-security').'</strong> '.$justUrl.'</p>';

				wp_mail($bps_email_to, $subject, $message, $headers);
			}
			}
			
			} else {	
				$status = 'Not Locked';
			}
			
			// .51.8: Insert a new row on first bad login attempt. After that update that same row
			if ( $failed_logins == 1 ) {
				
				$insert_rows = $wpdb->insert( $bpspro_login_table, array( 'status' => $status, 'user_id' => $user->ID, 'username' => $user->user_login, 'public_name' => $user->display_name, 'email' => $user->user_email, 'role' => $user->roles[0], 'human_time' => current_time('mysql'), 'login_time' => $login_time, 'lockout_time' => $lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $ip_address, 'hostname' => $hostname, 'request_uri' => $request_uri ) );		
					
			} else {
				
				$no_zeros = '0';
				$LSM_zero_filter = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $bpspro_login_table WHERE user_id = %d AND failed_logins != %d", $user->ID, $no_zeros ) );
				
				$update_rows = $wpdb->update( $bpspro_login_table, array( 'status' => $status, 'user_id' => $row->user_id, 'username' => $row->username, 'public_name' => $row->public_name, 'email' => $row->email, 'role' => $row->role, 'human_time' => current_time('mysql'), 'login_time' => $login_time, 'lockout_time' => $lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $row->ip_address, 'hostname' => $row->hostname, 'request_uri' => $row->request_uri ), array( 'user_id' => $row->user_id, 'failed_logins' => $row->failed_logins ) );

			}
		} // end if ( $wpdb->num_rows != 0...
} // end $BPSoptions['bps_login_security_logging'] == 'logLockouts') {...

/*
****************************************************
// Login Security & Monitoring
// WordPress Standard Authentication Processing Code
// with Generic Error Message display options
// .54.3: WooCommerce custom login page/form condition added
****************************************************
*/

if ( $BPSoptions['bps_login_security_OnOff'] == 'On' && isset( $_POST['wp-submit'] ) || $BPSoptions['bps_login_security_OnOff'] == 'On' && $BPSoptions['bps_enable_lsm_woocommerce'] == 1 && isset( $_POST['login'] ) ) {

	// if a user does not set/save this option then default to WP Errors
	// .53.8: added email address login error checking + messages
	if ( ! $user && ! $BPSoptions['bps_login_security_errors'] ) {
		return new WP_Error('invalid_username', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' Invalid username.', 'bulletproof-security').' <a href="%s">'.__('Lost your password?', 'bulletproof-security').'</a>', wp_lostpassword_url()));	
	}

	if ( ! $user && $BPSoptions['bps_login_security_errors'] == 'wpErrors' ) {
		
		if ( strpos( $username, '@' ) ) {
			return new WP_Error('invalid_email', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' Invalid email address.', 'bulletproof-security').' <a href="%s">'.__('Lost your password?', 'bulletproof-security').'</a>', wp_lostpassword_url()));

		} else {
		
			return new WP_Error('invalid_username', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' Invalid username.', 'bulletproof-security').' <a href="%s">'.__('Lost your password?', 'bulletproof-security').'</a>', wp_lostpassword_url()));		

		}
	}

	if ( ! $user && $BPSoptions['bps_login_security_errors'] == 'generic') {
		return new WP_Error('invalid_username', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' Invalid Entry.', 'bulletproof-security').' <a href="%s">'.__('Lost your password?', 'bulletproof-security').'</a>', wp_lostpassword_url()));
	}
	
	if ( ! $user && $BPSoptions['bps_login_security_errors'] == 'genericAll') {
		return new WP_Error('invalid_username', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' Invalid Entry.', 'bulletproof-security').' <a href="%s">'.__('Lost your password?', 'bulletproof-security').'</a>', wp_lostpassword_url()));

	}

	$user = apply_filters('wp_authenticate_user', $user, $password);
	if ( is_wp_error($user) ) 
		return $user;

	// if a user does not set/save this option then default to WP Errors
	if ( ! wp_check_password($password, $user->user_pass, $user->ID) && ! $BPSoptions['bps_login_security_errors'] ) {
		return new WP_Error('incorrect_password', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' The password you entered for the username ', 'bulletproof-security').'<strong>%1$s</strong>'.__(' is incorrect. ', 'bulletproof-security').' <a href="%2$s">'.__('Lost your password?', 'bulletproof-security').'</a>', $username, wp_lostpassword_url()));
	}

	if ( ! wp_check_password($password, $user->user_pass, $user->ID) && $BPSoptions['bps_login_security_errors'] == 'wpErrors' ) {
		
		if ( $BPSoptions['bps_login_security_remaining'] == 'On' ) {
		
			if ( strpos( $username, '@' ) ) {
				return new WP_Error('incorrect_password', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' The password you entered for the email address ', 'bulletproof-security').'<strong>%1$s</strong>'.__(' is incorrect. ', 'bulletproof-security').' <a href="%2$s">'.__('Lost your password?', 'bulletproof-security').'</a>'.__(' Login Attempts Remaining ', 'bulletproof-security').'<strong>%3$d</strong>', $username, wp_lostpassword_url(), $remaining ) );

			} else {
			
				return new WP_Error('incorrect_password', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' The password you entered for the username ', 'bulletproof-security').'<strong>%1$s</strong>'.__(' is incorrect. ', 'bulletproof-security').' <a href="%2$s">'.__('Lost your password?', 'bulletproof-security').'</a>'.__(' Login Attempts Remaining ', 'bulletproof-security').'<strong>%3$d</strong>', $username, wp_lostpassword_url(), $remaining ) );
			}
		
		} else {

			if ( strpos( $username, '@' ) ) {

				return new WP_Error('incorrect_password', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' The password you entered for the email address ', 'bulletproof-security').'<strong>%1$s</strong>'.__(' is incorrect. ', 'bulletproof-security').' <a href="%2$s">'.__('Lost your password?', 'bulletproof-security').'</a>', $username, wp_lostpassword_url()));

			} else {
				
				return new WP_Error('incorrect_password', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' The password you entered for the username ', 'bulletproof-security').'<strong>%1$s</strong>'.__(' is incorrect. ', 'bulletproof-security').' <a href="%2$s">'.__('Lost your password?', 'bulletproof-security').'</a>', $username, wp_lostpassword_url()));

			}
		}
	}
	
	if ( ! wp_check_password($password, $user->user_pass, $user->ID) && $BPSoptions['bps_login_security_errors'] == 'generic' ) {	

		if ( $BPSoptions['bps_login_security_remaining'] == 'On' ) {

			return new WP_Error('incorrect_password', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' Invalid Entry.', 'bulletproof-security').' <a href="%2$s">'.__('Lost your password?', 'bulletproof-security').'</a>'.__(' Login Attempts Remaining ', 'bulletproof-security').'<strong>%3$d</strong>', $username, wp_lostpassword_url(), $remaining ) );

		} else {	
		
			return new WP_Error('incorrect_password', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' Invalid Entry.', 'bulletproof-security').' <a href="%2$s">'.__('Lost your password?', 'bulletproof-security').'</a>', $username, wp_lostpassword_url() ) );

		}
	}
	
	if ( ! wp_check_password($password, $user->user_pass, $user->ID) && $BPSoptions['bps_login_security_errors'] == 'genericAll' ) {	

		if ( $BPSoptions['bps_login_security_remaining'] == 'On' ) {

			return new WP_Error('incorrect_password', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' Invalid Entry.', 'bulletproof-security').' <a href="%2$s">'.__('Lost your password?', 'bulletproof-security').'</a>'.__(' Login Attempts Remaining ', 'bulletproof-security').'<strong>%3$d</strong>', $username, wp_lostpassword_url(), $remaining ) );

		} else {

			return new WP_Error('incorrect_password', sprintf('<strong>'.__('ERROR:', 'bulletproof-security').'</strong>'.__(' Invalid Entry.', 'bulletproof-security').' <a href="%2$s">'.__('Lost your password?', 'bulletproof-security').'</a>', $username, wp_lostpassword_url() ) );

		}
	}
	return $user;
	}
}

/************************************************/
// Disable/Enable Password Reset Frontend/Backend
// Independent Password Reset Option added BPS .50.5
// Removes a lot of Cool WP features, but
// if Stealth Mode is desired then oh well
/************************************************/

if ( $BPSoptions['bps_login_security_OnOff'] != 'Off' ) {

if ( $BPSoptions['bps_login_security_OnOff'] == 'pwreset' || $BPSoptions['bps_login_security_OnOff'] == 'On' ) {

	$pw_reset = '1';

} else {

	$pw_reset = '0';
}

switch ( $pw_reset ) {

    case ( $pw_reset == '1' && $BPSoptions['bps_login_security_pw_reset'] == 'disableFrontend' ):
		
		if ( ! is_admin() ) {
		
		function bpspro_disable_password_reset() { 
			return false; 
		}
		add_filter( 'allow_password_reset', 'bpspro_disable_password_reset' );

		function bpspro_show_password_fields() { 
			return false; 
		}
		add_filter( 'show_password_fields', 'bpspro_show_password_fields' );

		function bpspro_remove_pw_text($text) {
			return str_replace( array('Lost your password?', 'Lost your password'), '', trim($text, '?') ); 
		}
		add_filter( 'gettext', 'bpspro_remove_pw_text' ); 

		// Replace invalidcombo error - valid user account/invalid user account same exact result 
		function bpspro_login_error_invalidcombo($text) { 
			return str_replace( '<strong>ERROR</strong>: Invalid username or e-mail.', 'Password reset is not allowed for this user', $text ); 
		}
		add_filter ( 'login_errors', 'bpspro_login_error_invalidcombo');

		// Replace invalid_email error - valid email/invalid email same exact result
		function bpspro_login_error_invalid_email($text) { 
			return str_replace( '<strong>ERROR</strong>: There is no user registered with that email address.', 'Password reset is not allowed for this user', $text );
		}
		add_filter ( 'login_errors', 'bpspro_login_error_invalid_email');

		// Removes WP Shake It so that no indication is given of good/bad value/entry
		function bpspro_remove_shake() {
			remove_action( 'login_head', 'wp_shake_js', 12 );	
		}
		add_filter ( 'shake_error_codes', 'bpspro_remove_shake');	
		}	
		break;
    case ( $pw_reset == '1' && $BPSoptions['bps_login_security_pw_reset'] == 'disable' ):
		
		function bpspro_disable_password_reset() { 
			return false; 
		}
		add_filter( 'allow_password_reset', 'bpspro_disable_password_reset' );

		function bpspro_show_password_fields() { 
			return false; 
		}
		add_filter( 'show_password_fields', 'bpspro_show_password_fields' );

		function bpspro_remove_pw_text($text) {
			return str_replace( array('Lost your password?', 'Lost your password'), '', trim($text, '?') ); 
		}
		add_filter( 'gettext', 'bpspro_remove_pw_text' ); 

		// Replace invalidcombo error - valid user account/invalid user account same exact result 
		function bpspro_login_error_invalidcombo($text) { 
			return str_replace( '<strong>ERROR</strong>: Invalid username or e-mail.', 'Password reset is not allowed for this user', $text ); 
		}
		add_filter ( 'login_errors', 'bpspro_login_error_invalidcombo');

		// Replace invalid_email error - valid email/invalid email same exact result
		function bpspro_login_error_invalid_email($text) { 
			return str_replace( '<strong>ERROR</strong>: There is no user registered with that email address.', 'Password reset is not allowed for this user', $text );
		}
		add_filter ( 'login_errors', 'bpspro_login_error_invalid_email');

		// Removes WP Shake It so that no indication is given of good/bad value/entry
		function bpspro_remove_shake() {
			remove_action( 'login_head', 'wp_shake_js', 12 );	
		}
		add_filter ( 'shake_error_codes', 'bpspro_remove_shake');
		break;
 	}
}

/**************************************************************/
// WordPress Authentication Cookie Expiration (ACE)
// When Cookie Expiration Time Expires User is logged out
// Username Exceptions default to WP Cookie Defaults
// User Role Cookie Expiration set by User
// SSL uses the same $expiration variable value
// 2880 * 60 = 172800 = 2 days | 20160 * 60 = 1209600 = 14 days
/**************************************************************/

// .54.1: switched to Roles instead of using user_level
// .54.2: Added Custom User Roles
function bpsPro_ACE_cookie_expiration( $expiration, $user_id, $remember ) {
$BPS_ACE_options = get_option('bulletproof_security_options_auth_cookie');	

	if ( $BPS_ACE_options['bps_ace'] == 'On' ) {

		global $current_user;
		$user = get_userdata($user_id);
		$user_roles = $user->roles;
		$user_role = array_shift($user_roles);

		if ( $remember ) {

			if ( $BPS_ACE_options['bps_ace_rememberme_expiration'] == '' ) {
				
				$expiration = 1209600;
				return $expiration;		
			}
			
			if ( preg_match( '/'.$user->user_login.'/i', $BPS_ACE_options['bps_ace_user_account_exceptions'], $matches ) ) {

				$expiration = 1209600;
				return $expiration;	
		
			// If Role checkbox is not checked cookie expiration defaults to wp default cookie expiration
			} elseif ( $user_role == 'administrator' && $BPS_ACE_options['bps_ace_administrator'] == '1' || $user_role == 'editor' && $BPS_ACE_options['bps_ace_editor'] == '1' || $user_role == 'author' && $BPS_ACE_options['bps_ace_author'] == '1' || $user_role == 'contributor' && $BPS_ACE_options['bps_ace_contributor'] == '1' || $user_role == 'subscriber' && $BPS_ACE_options['bps_ace_subscriber'] == '1' ) {

				$expiration = $BPS_ACE_options['bps_ace_rememberme_expiration'] * 60;
				return $expiration;
		
			} elseif ( $user_role != 'administrator' && $user_role != 'editor' && $user_role != 'author' && $user_role != 'contributor' && $user_role != 'subscriber' ) {
				
				foreach ( $BPS_ACE_options as $key => $value ) {
		
					if ( $key == 'bps_ace_custom_roles' && is_array($value) ) {
			
						foreach ( $value as $ckey => $cvalue ) {
				
							if ( $user_role == $ckey && $cvalue == '1' ) {
							
								$expiration = $BPS_ACE_options['bps_ace_expiration'] * 60;
								return $expiration;
							
							} else {
								$expiration = 1209600;
								return $expiration;								
							}
						}
					}
				}

			} else {
			
				$expiration = 1209600;
				return $expiration;					
			}
	
		} else {
		
			if ( $BPS_ACE_options['bps_ace_expiration'] == '' ) {
				
				$expiration = 172800;
				return $expiration;	
			}
			
			if ( preg_match( '/'.$user->user_login.'/i', $BPS_ACE_options['bps_ace_user_account_exceptions'], $matches ) ) {

				$expiration = 172800;
				return $expiration;	
		
			// If Role checkbox is not checked cookie expiration defaults to wp default cookie expiration
			} elseif ( $user_role == 'administrator' && $BPS_ACE_options['bps_ace_administrator'] == '1' || $user_role == 'editor' && $BPS_ACE_options['bps_ace_editor'] == '1' || $user_role == 'author' && $BPS_ACE_options['bps_ace_author'] == '1' || $user_role == 'contributor' && $BPS_ACE_options['bps_ace_contributor'] == '1' || $user_role == 'subscriber' && $BPS_ACE_options['bps_ace_subscriber'] == '1' ) {

				$expiration = $BPS_ACE_options['bps_ace_expiration'] * 60;
				return $expiration;
				
			} elseif ( $user_role != 'administrator' && $user_role != 'editor' && $user_role != 'author' && $user_role != 'contributor' && $user_role != 'subscriber' ) {
				
				foreach ( $BPS_ACE_options as $key => $value ) {
		
					if ( $key == 'bps_ace_custom_roles' && is_array($value) ) {
			
						foreach ( $value as $ckey => $cvalue ) {
				
							if ( $user_role == $ckey && $cvalue == '1' ) {
							
								$expiration = $BPS_ACE_options['bps_ace_expiration'] * 60;
								return $expiration;
							
							} else {
								$expiration = 172800;
								return $expiration;								
							}
						}
					}
				}
		
			} else {
			
				$expiration = 172800;
				return $expiration;
			}
		}
	}
}

$BPS_ACE_options = get_option('bulletproof_security_options_auth_cookie');
if ( $BPS_ACE_options && $BPS_ACE_options['bps_ace'] != 'Off' ) {	
	
	add_filter('auth_cookie_expiration', 'bpsPro_ACE_cookie_expiration', 10, 3);
	add_action('login_form', 'bpsPro_remove_rememberme_login_form_field');
	add_action('login_head', 'bpsPro_unset_rememberme_login_form_field');
}

// .54.2: ACE hide/remove the Remember Me checkbox for everyone.
function bpsPro_remove_rememberme_login_form_field() { 
$BPS_ACE_options = get_option('bulletproof_security_options_auth_cookie');

	if ( $BPS_ACE_options['bps_ace'] == 'On' && $BPS_ACE_options['bps_ace_rememberme_disable'] == '1' ) {
?>
	
<style>
<!--
.forgetmenot{display:none;}
-->
</style>

<?php } }

// .54.2: ACE unset/disable the Remember Me checkbox/POST value for everyone. Cookie expiration will be Session.
function bpsPro_unset_rememberme_login_form_field() {
$BPS_ACE_options = get_option('bulletproof_security_options_auth_cookie');	

	if ( $BPS_ACE_options['bps_ace'] == 'On' && $BPS_ACE_options['bps_ace_rememberme_disable'] == '1' && isset( $_POST['wp-submit'] ) && isset( $_POST['rememberme'] ) ) {
		unset( $_POST['rememberme'] );
	}	
}

?>