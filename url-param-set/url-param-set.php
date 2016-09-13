<?php
	/*
	Plugin Name: URL Params To Sessions
	Plugin URI: http://www.fullmotionstudio.com
	Description: Set defined URL parameters in sessions.
	Version: 0.1
	Author: Jon Houston
	Author URI: http://www.fullmotionstudio.com
	License: Private
	*/
	
	//upts_get_parameters();
	
	define( 'UPTS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	
	/* -- Enqueue Scripts
	*/
	
	add_action ( 'wp_footer', 'upts_load_js');
	function upts_load_js() {
		wp_register_script('ups-js', plugins_url('/js/upts.ajax.js', __FILE__), array('jquery'),'1.1', true);
		wp_enqueue_script('ups-js');
	}
	
	/* -- Admin Dashboard
	*/
	
	add_action('admin_menu', 'add_upts_menu');
	function add_upts_menu(){
     add_menu_page( 'URL Param Set', 'URL Param Set', 'manage_options', 'ups-options', 'upts_menu_main');
	}
	
	function upts_menu_main() {
		
		$existing_params = '';
		
		if(!empty($_POST['ups-update-settings'])):
			$action = $_POST['ups-update-settings'];
		endif;
		
		if(!empty($_POST['ups-url-params'])):
			$upts_url_params_post = $_POST['ups-url-params'];
		endif;
		
		
		if ($action == 'true'):
			upts_update_parameters_settings($upts_url_params_post);
		endif;
		
		$existing_results = upts_get_parameters_settings();
		$existing_params = $existing_results[0]->option_value;
		
		echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div><h2>URL Param Set Settings</h2></div>';
		echo '<p>Enter the URL parameteres you\'d like to accept on the website. Please use a comma serated list (Example: param1, param2, etc.).</p>';
		echo '<p><b>*note -</b> Parameter names must use URL safe characters, contain no spaces or special symbols such as $, #, &, or ?.</p>';
		echo '<form action="" method="post">
						<input type="text" name="ups-url-params" cols="75" rows="10" value="'.$existing_params.'"><br><br>
						<input type="hidden" name="ups-update-settings" value="true" />
						<input type="submit" value="Update Settings" />
					</form>';
					
	}
	
	/**
	 * Function: upts_get_parameters_settings | returns string
	 * Description: Retrives option_value of option_name ups-accepted-url-parameteres from the options table
	 */
	
	function upts_get_parameters_settings() {
		
		global $wpdb;
		$query = 'SELECT option_value FROM '.$wpdb->prefix.'options WHERE option_name = "ups-accepted-url-parameteres"';

		$results = $wpdb->get_results( $query );
		
		return $results;
		
	}
	
	/**
	 * Function: upts_get_parameters_settings_array | returns array
	 * Description: Calls upts_get_parameters_settings, converts and returns comma seperated string to array
	 */
	function upts_get_parameters_settings_array() {
		
		$parameter_settings_string = upts_get_parameters_settings();
		$parameter_settings_raw_array = explode(',', $parameter_settings_string[0]->option_value);
		$parameter_settings_clean_array;
		
		foreach($parameter_settings_raw_array as $param):
			$parameter_settings_clean_array[] = trim($param);
		endforeach;
		
		return $parameter_settings_clean_array;
		
	}
	
	/**
	 * Function: upts_update_parameters_settings | returns null
	 * Description: UPDATES or INSERTS option_value of option_name ups-accepted-url-parameteres from/for the options table
	 */
	function upts_update_parameters_settings($upts_url_params_post) {

		$existing_results = upts_get_parameters_settings();
		
		if ($existing_results[0]->option_value != ''):
			update_option( 'ups-accepted-url-parameteres', $upts_url_params_post, 'yes' );
		else:
			add_option( 'ups-accepted-url-parameteres', $upts_url_params_post, '', 'yes' );
		endif;
		
	}
	
	/**
	 * Function: upts_validate_and_set_data | returns null
	 * Description: Compares associative key names in $post_data to array values returned from upts_get_parameters_settings_array(). 
	 * Sets $_SESSION value to key name if it exists
	 */
	function upts_validate_and_set_data($post_data) {

		$post_data_array = upts_convert_url_string_to_array($post_data);
		$allowed_url_params_from_settings =  upts_get_parameters_settings_array();
		$data;

		foreach($post_data_array as $data_key=>$data_value):

			if( in_array($data_key, $allowed_url_params_from_settings) ):
				setcookie ($data_key, $data_value, time()+60*60*24);
				
				$data[$data_key] = $data_value;
			endif;

		endforeach;
		
		return $data;

	}
	
	function upts_validate_and_get_data() {

		$allowed_url_params_from_settings =  upts_get_parameters_settings_array();
		$data;

		foreach($_COOKIE as $data_key=>$data_value):

			if( in_array($data_key, $allowed_url_params_from_settings) ):
				
				$data[$data_key] = $data_value;
				$data['debug_string'] .= 'Key: '.$data_key.' Value: '.$data_value;
			endif;

		endforeach;
		
		return $data;

	}
	
	/**
	 * Function: upts_convert_url_string_to_array | returns array
	 * Description: Converts URL string parameters to an associative array
	 */
	function upts_convert_url_string_to_array($post_data) {
		
		$data = array();
		
		$post_data_array_raw = explode('&', $post_data['urlparams']);
		
		foreach($post_data_array_raw as $d):
			$key_and_value = explode('=', $d);
			$data[$key_and_value[0]] = $key_and_value[1];
		endforeach;
		
		return $data;
		
	}
	
	// -- Begin Ajax Functionality
	add_action( 'wp_ajax_upts_set_params_to_session', 'upts_set_params_to_session' );
	add_action( 'wp_ajax_nopriv_upts_set_params_to_session', 'upts_set_params_to_session' );
	
	function upts_set_params_to_session() {
		
		$post_data = $_POST;
		
		upts_validate_and_set_data($post_data);
		
		//echo json_encode(upts_convert_url_string_to_array($post_data));
		echo json_encode(upts_validate_and_get_data());
		
		wp_die();
		
	}