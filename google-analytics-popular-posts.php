<?php
/*
Plugin Name: Google Analytics Popular Posts
Plugin URI: http://wordpress.org/extend/plugins/google-analytics-popular-posts/
Description: This plugin uses Google Analytics API to fetch data from your analytics account and displays popular posts in the widget.
Version: 1.1.8
Author: koichiyaima
Author URI: http://yaima.sakuraweb.com/
*/
/*  Copyright 2011 Yaima surf (email : yaima-surf@yaima.sakuraweb.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public Licen se as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

// GoogleAnalyticsPopularPosts Class
class GoogleAnalyticsPopularPosts extends WP_Widget {

//**************************************************************************************
// Constructor
//**************************************************************************************
	function GoogleAnalyticsPopularPosts() {
		//$GAPP_url = get_bloginfo('siteurl'); 
		// CF edit to prevent deprectaed warning
		$GAPP_url = get_bloginfo('url');
		$lang = dirname( plugin_basename(__FILE__)) . "/languages";
		load_plugin_textdomain('google-analytics-popular-posts', false, $lang);
		$widget_ops = array('description' => __('This plugin uses Google Analytics API to fetch data from your analytics account and displays popular posts in the widget.', 'google-analytics-popular-posts'));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::WP_Widget(false, $name = __('Google Analytics Popular Posts', 'google-analytics-popular-posts'), $widget_ops, $control_ops);
	}

//**************************************************************************************
// WP_Widget::form
//**************************************************************************************
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array('title' => ''));
		$title = strip_tags($instance['title']);
?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('<b>Title:</b>', 'google-analytics-popular-posts'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
<p><?php _e('Thank you for using this plugin! I hope you enjoy it!<br />Author: <a href="http://yaima.sakuraweb.com" target="_blank">http://yaima.sakuraweb.com</a>', 'google-analytics-popular-posts'); ?></p>
<?php
	}

//**************************************************************************************
// WP_Widget::update
//**************************************************************************************
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

//**************************************************************************************
//  WP_Widget::widget
//**************************************************************************************
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);
		echo $before_widget;
		if (!empty($title)) {
			echo $before_title . $title . $after_title;
		}
		GoogleAnalyticsPopularPosts_view();
		echo $after_widget;
	}
} // GoogleAnalyticsPopularPosts Class - END

//**************************************************************************************
//  Register widget
//**************************************************************************************
function GoogleAnalyticsPopularPostsInit() {
    register_widget('GoogleAnalyticsPopularPosts');
}
add_action('widgets_init', 'GoogleAnalyticsPopularPostsInit');

//**************************************************************************************
// Activate and Deactivate
//**************************************************************************************
function GoogleAnalyticsPopularPosts_activate() {
	if(!get_option('GoogleAnalyticsPopularPosts_maxResults'))
		add_option('GoogleAnalyticsPopularPosts_maxResults', '10');
	if(!get_option('GoogleAnalyticsPopularPosts_dateDispEnable'))
		add_option('GoogleAnalyticsPopularPosts_dateDispEnable', 'yes');
	if(!get_option('GoogleAnalyticsPopularPosts_postDateEnable'))
		add_option('GoogleAnalyticsPopularPosts_postDateEnable', 'no');
	if(!get_option('GoogleAnalyticsPopularPosts_contentsViewEnable'))
		add_option('GoogleAnalyticsPopularPosts_contentsViewEnable', 'no');
	if(!get_option('GoogleAnalyticsPopularPosts_cssEnable'))
		add_option('GoogleAnalyticsPopularPosts_cssEnable', 'yes');
	if(!get_option('GoogleAnalyticsPopularPosts_cacheEnable'))
		add_option('GoogleAnalyticsPopularPosts_cacheEnable', 'no');
	if(!get_option('GoogleAnalyticsPopularPosts_cacheExpiresMinutes'))
		add_option('GoogleAnalyticsPopularPosts_cacheExpiresMinutes', '60');
}
function GoogleAnalyticsPopularPosts_deactivate() {
}
register_activation_hook(__FILE__, 'GoogleAnalyticsPopularPosts_activate');
register_deactivation_hook(__FILE__, 'GoogleAnalyticsPopularPosts_deactivate' );

//**************************************************************************************
//  Options
//**************************************************************************************
function GoogleAnalyticsPopularPosts_options() {
	if($_POST['Submit'] == "Save") {
		$expire = mktime(date("H"), date("i"), date("s"), date("m")  , date("d"), date("Y")) + (60 * $_POST['GoogleAnalyticsPopularPosts_cacheExpiresMinutes']);
		$now = mktime(date("H"), date("i"), date("s"), date("m")  , date("d"), date("Y"));
		update_option('GoogleAnalyticsPopularPosts_username', $_POST['GoogleAnalyticsPopularPosts_username']);
		if($_POST['GoogleAnalyticsPopularPosts_password'])
			update_option('GoogleAnalyticsPopularPosts_password', $_POST['GoogleAnalyticsPopularPosts_password']);
		update_option('GoogleAnalyticsPopularPosts_profileID' , $_POST['GoogleAnalyticsPopularPosts_profileID']);
		update_option('GoogleAnalyticsPopularPosts_maxResults', $_POST['GoogleAnalyticsPopularPosts_maxResults']?$_POST['GoogleAnalyticsPopularPosts_maxResults']:10);
		update_option('GoogleAnalyticsPopularPosts_statsSinceDays' ,  is_numeric($_POST['GoogleAnalyticsPopularPosts_statsSinceDays'])?$_POST['GoogleAnalyticsPopularPosts_statsSinceDays']:"");
// magic_quotes_gpc = On
		if (get_magic_quotes_gpc()) {
			$_POST['GoogleAnalyticsPopularPosts_filter'] = stripslashes($_POST['GoogleAnalyticsPopularPosts_filter']);
		}
		update_option('GoogleAnalyticsPopularPosts_filter' , $_POST['GoogleAnalyticsPopularPosts_filter']);
		update_option('GoogleAnalyticsPopularPosts_dateDispEnable', $_POST['GoogleAnalyticsPopularPosts_dateDispEnable']);
		update_option('GoogleAnalyticsPopularPosts_postDateEnable', $_POST['GoogleAnalyticsPopularPosts_postDateEnable']);
		update_option('GoogleAnalyticsPopularPosts_contentsViewEnable', $_POST['GoogleAnalyticsPopularPosts_contentsViewEnable']);
		update_option('GoogleAnalyticsPopularPosts_cssEnable', $_POST['GoogleAnalyticsPopularPosts_cssEnable']);
		update_option('GoogleAnalyticsPopularPosts_cacheEnable', $_POST['GoogleAnalyticsPopularPosts_cacheEnable']);
		update_option('GoogleAnalyticsPopularPosts_cacheExpiresMinutes', $_POST['GoogleAnalyticsPopularPosts_cacheExpiresMinutes']);
		update_option('GoogleAnalyticsPopularPosts_cacheExpires', $expire);
	}
?>
		<div class="wrap">
			<div class="icon32" id="icon-options-general"><br/></div>
			<h2 style="margin-top:0px">Google Analytics Popular Posts <?php echo GoogleAnalyticsPopularPosts_plugin_get_version(); ?> <?php _e('Options', 'google-analytics-popular-posts'); ?></h2>
			<p><?php _e('by', 'google-analytics-popular-posts'); ?> <strong><a href="http://yaima.sakuraweb.com/" target="_blank">Yaima surf</a></strong></p>
			<form method="post">
				<table class="form-table">
					<tbody>
					<tr valign="top">
						<th scope="row"><label for="GoogleAnalyticsPopularPosts_username"><?php _e('Google Account Email:', 'google-analytics-popular-posts'); ?></label></th>
						<td>
							<input type="text" class="regular-text" value="<?php echo get_option('GoogleAnalyticsPopularPosts_username'); ?>" name="GoogleAnalyticsPopularPosts_username"/><br />
							<span class="setting-description"><?php _e('Please fill the email address you use to login to your Google Analytics.', 'google-analytics-popular-posts'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="GoogleAnalyticsPopularPosts_password"><?php _e('Google Account Password:', 'google-analytics-popular-posts'); ?></label></th>
						<td>
							<input type="password" class="regular-text" value="<?php echo get_option('GoogleAnalyticsPopularPosts_password'); ?>" name="GoogleAnalyticsPopularPosts_password"/><br />
							<span class="setting-description"><?php _e('Please fill password correct too again.', 'google-analytics-popular-posts'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="GoogleAnalyticsPopularPosts_profileID"><?php _e('Google Analytics Profile ID:', 'google-analytics-popular-posts'); ?></label></th>
						<td>
							<input type="text" class="regular-text" value="<?php echo get_option('GoogleAnalyticsPopularPosts_profileID'); ?>" name="GoogleAnalyticsPopularPosts_profileID"/><br />
							<span class="setting-description"><?php _e('Check for your Profile ID. <br />(UA-xxxxxxx-xx is not the Profile ID, The profile ID of your account can be found in the URL of your reports. For example, if you select a profile from an account and view your reports, you may see a URL string that looks like this:<br />https://www.google.com/analytics/reporting/?reset=1&id=123456&pdr=00000000-00000000<br />The profile ID is the number that comes right after the &id parameter. So, in this case, your profile ID would be 123456.)', 'google-analytics-popular-posts'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="GoogleAnalyticsPopularPosts_maxResults"><?php _e('Popular Posts Max Results:', 'google-analytics-popular-posts'); ?></label></th>
						<td>
							<input type="text" class="small-text" value="<?php echo get_option('GoogleAnalyticsPopularPosts_maxResults'); ?>" name="GoogleAnalyticsPopularPosts_maxResults"/><br />
							<span class="setting-description"><?php _e('<strong>Example: 10 or 15 or 20 etc.</strong>', 'google-analytics-popular-posts'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="GoogleAnalyticsPopularPosts_statsSinceDays"><?php _e('Stats Since Days :', 'google-analytics-popular-posts'); ?></label></th>
						<td>
							<input type="text" class="small-text" value="<?php echo get_option('GoogleAnalyticsPopularPosts_statsSinceDays'); ?>" name="GoogleAnalyticsPopularPosts_statsSinceDays"/> <?php _e('<strong>Days</strong>', 'google-analytics-popular-posts'); ?><br />
							<span class="setting-description"><?php _e('Define the number of days you want the analytics Popular Posts.<br /><strong>Example: 7 or 30 or 90 etc.</strong>', 'google-analytics-popular-posts'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="GoogleAnalyticsPopularPosts_filter"><?php _e('GAPI Filter:', 'google-analytics-popular-posts'); ?></label></th>
						<td>
							<?php _e('<strong>Filter expressions: filters=ga:pagePath=~^/</strong>', 'google-analytics-popular-posts'); ?> <input type="text" class="regular-text" value="<?php echo get_option('GoogleAnalyticsPopularPosts_filter'); ?>" name="GoogleAnalyticsPopularPosts_filter"/><br />
							<span class="setting-description"><?php _e('You can also use regular expressions.<br />If home page and other pages do not get, only get the permalink blog begins, just typing blog.<br /><strong>Example: blog</strong>', 'google-analytics-popular-posts'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="GoogleAnalyticsPopularPosts_dateDispEnable"><?php _e('Enable the display of stats date?', 'google-analytics-popular-posts'); ?></label></th>
						<td>
							<fieldset><legend class="hidden">Enable the display of stats date?</legend>
								<input type="radio" <?php echo get_option('GoogleAnalyticsPopularPosts_dateDispEnable')=='yes'   ?'checked="checked"':''; ?> value="yes" name="GoogleAnalyticsPopularPosts_dateDispEnable"/> <?php _e('Yes', 'google-analytics-popular-posts'); ?>
								<input type="radio" <?php echo get_option('GoogleAnalyticsPopularPosts_dateDispEnable')=='no'    ?'checked="checked"':''; ?> value="no"  name="GoogleAnalyticsPopularPosts_dateDispEnable"/> <?php _e('No', 'google-analytics-popular-posts'); ?>
							</fieldset>
							<span class="setting-description"><?php _e('Can enable / disable the display of stats date.', 'google-analytics-popular-posts'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="GoogleAnalyticsPopularPosts_postDateEnable"><?php _e('Enable the display of posted date?', 'google-analytics-popular-posts'); ?></label></th>
						<td>
							<fieldset><legend class="hidden">Enable the display of posted date?</legend>
								<input type="radio" <?php echo get_option('GoogleAnalyticsPopularPosts_postDateEnable')=='yes'   ?'checked="checked"':''; ?> value="yes" name="GoogleAnalyticsPopularPosts_postDateEnable"/> <?php _e('Yes', 'google-analytics-popular-posts'); ?>
								<input type="radio" <?php echo get_option('GoogleAnalyticsPopularPosts_postDateEnable')=='no'    ?'checked="checked"':''; ?> value="no"  name="GoogleAnalyticsPopularPosts_postDateEnable"/> <?php _e('No', 'google-analytics-popular-posts'); ?>
							</fieldset>
							<span class="setting-description"><?php _e('Can enable / disable the display of posted date.', 'google-analytics-popular-posts'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="GoogleAnalyticsPopularPosts_contentsViewEnable"><?php _e('Enable the display of excerpt?', 'google-analytics-popular-posts'); ?></label></th>
						<td>
							<fieldset><legend class="hidden">Enable the display of contents?</legend>
								<input type="radio" <?php echo get_option('GoogleAnalyticsPopularPosts_contentsViewEnable')=='yes'   ?'checked="checked"':''; ?> value="yes" name="GoogleAnalyticsPopularPosts_contentsViewEnable"/> <?php _e('Yes', 'google-analytics-popular-posts'); ?>
								<input type="radio" <?php echo get_option('GoogleAnalyticsPopularPosts_contentsViewEnable')=='no'    ?'checked="checked"':''; ?> value="no"  name="GoogleAnalyticsPopularPosts_contentsViewEnable"/> <?php _e('No', 'google-analytics-popular-posts'); ?>
							</fieldset>
							<span class="setting-description"><?php _e('Can enable / disable the display of excerpt.', 'google-analytics-popular-posts'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="GoogleAnalyticsPopularPosts_cssEnable"><?php _e('Enable Custom Style Sheet?', 'google-analytics-popular-posts'); ?></label></th>
						<td>
							<fieldset><legend class="hidden">Enable Custom Style Sheet?</legend>
								<input type="radio" <?php echo get_option('GoogleAnalyticsPopularPosts_cssEnable')=='yes'   ?'checked="checked"':''; ?> value="yes" name="GoogleAnalyticsPopularPosts_cssEnable"/> <?php _e('Yes', 'google-analytics-popular-posts'); ?>
								<input type="radio" <?php echo get_option('GoogleAnalyticsPopularPosts_cssEnable')=='no'    ?'checked="checked"':''; ?> value="no"  name="GoogleAnalyticsPopularPosts_cssEnable"/> <?php _e('No', 'google-analytics-popular-posts'); ?>
							</fieldset>
							<span class="setting-description"><?php _e('Can enable / disable the custom style sheet.', 'google-analytics-popular-posts'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="GoogleAnalyticsPopularPosts_cacheEnable"><?php _e('Enable Cache?', 'google-analytics-popular-posts'); ?></label></th>
						<td>
							<fieldset><legend class="hidden">Enable Cache?</legend>
								<input type="radio" <?php echo get_option('GoogleAnalyticsPopularPosts_cacheEnable')=='yes'   ?'checked="checked"':''; ?> value="yes" name="GoogleAnalyticsPopularPosts_cacheEnable"/> <?php _e('Yes', 'google-analytics-popular-posts'); ?>
								<input type="radio" <?php echo get_option('GoogleAnalyticsPopularPosts_cacheEnable')=='no'    ?'checked="checked"':''; ?> value="no"  name="GoogleAnalyticsPopularPosts_cacheEnable"/> <?php _e('No', 'google-analytics-popular-posts'); ?>
							</fieldset>
							<span class="setting-description"><?php _e('Can enable / disable the cache.', 'google-analytics-popular-posts'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="GoogleAnalyticsPopularPosts_cacheExpiresMinutes"><?php _e('If Cache Enabled, Cache Expires in:', 'google-analytics-popular-posts'); ?></label></th>
						<td>
							<input type="text" class="small-text" value="<?php echo get_option('GoogleAnalyticsPopularPosts_cacheExpiresMinutes'); ?>" name="GoogleAnalyticsPopularPosts_cacheExpiresMinutes"/> <?php _e('<strong>Minutes</strong>', 'google-analytics-popular-posts'); ?><br />
							<span class="setting-description"><?php _e('<strong>Example: 30 or 60 (for 1 hour) or 1440 (60 minutes x 24 hours = 1440 minutes for 1 day) etc</strong>', 'google-analytics-popular-posts'); ?></span>
						</td>
					</tr>
					</tbody>
				</table>
				<div style="float:right;">
					<p><?php _e('Thank you for using this plugin! I hope you enjoy it!<br />Author: <a href="http://yaima.sakuraweb.com" target="_blank">http://yaima.sakuraweb.com</a>', 'google-analytics-popular-posts'); ?></p>
				</div>
				<p class="submit">
					<input type="submit" value="Save" class="button-primary" name="Submit"/>
				</p>
			</form>
<?php
	$GAPP_css = get_option('GoogleAnalyticsPopularPosts_cssEnable');
	if ($GAPP_css == "yes") {
		echo '<div style="width:300px; float:left; list-style-type: none;"> <style type="text/css"> .popular_post { background: #ffffff; border: 1px solid #cccccc; -moz-border-radius: 8px; -webkit-border-radius: 8px; border-radius: 8px; -moz-box-shadow: inset 0 0 5px 5px #f5f5f5; -webkit-box-shadow: inset 0 0 5px 5px #f5f5f5; box-shadow: inset 0 0 5px 5px #f5f5f5; padding: 10px; margin: 1em 0; padding-bottom: 10px; margin-bottom: 10px; } </style>';
		echo '<h3>';
		echo __('Preview', 'google-analytics-popular-posts');
		echo '</h3>';
		GoogleAnalyticsPopularPosts_view(true);
		echo '</div>';
	}
	else {
		echo '<div style="float:left; list-style-type: none;">';
		echo '<h3>';
		echo __('Preview', 'google-analytics-popular-posts');
		echo '</h3>';
		GoogleAnalyticsPopularPosts_view(true);
		echo '</div>';
	}
?>
		</div>
<?php
}

//**************************************************************************************
//  Add admin menu
//**************************************************************************************
function GoogleAnalyticsPopularPosts_menu() {
    add_options_page('Google Analytics Popular Posts', 'Google Analytics Popular Posts', 8, __FILE__, 'GoogleAnalyticsPopularPosts_options');
}
add_action('admin_menu', 'GoogleAnalyticsPopularPosts_menu');

//**************************************************************************************
//  Output with debug mode
//**************************************************************************************
function GoogleAnalyticsPopularPosts_view($debug = false) {
	$GAPP_cEn = get_option('GoogleAnalyticsPopularPosts_cacheEnable');
	$GAPP_cEM = get_option('GoogleAnalyticsPopularPosts_cacheExpiresMinutes');
	$GAPP_cEx = get_option('GoogleAnalyticsPopularPosts_cacheExpires');
	$GAPP_che = get_option('GoogleAnalyticsPopularPosts_cache');
	try {
		if($GAPP_cEn == 'yes') {
			$now = mktime(date("H"), date("i"), date("s"), date("m")  , date("d"), date("Y"));
			if($now > $GAPP_cEx or strlen($GAPP_che) == '') {
				$expire = $now + (60 * $GAPP_cEM);
				update_option('GoogleAnalyticsPopularPosts_cacheExpires', $expire);
				update_option('GoogleAnalyticsPopularPosts_cache', GoogleAnalyticsPopularPosts_widget_output());
				$output = get_option('GoogleAnalyticsPopularPosts_cache');
			}
			else
				$output = get_option('GoogleAnalyticsPopularPosts_cache');
			}
		else {
			update_option('GoogleAnalyticsPopularPosts_cache', '');
			$output = GoogleAnalyticsPopularPosts_widget_output();
		}
	}
	catch(Exception $e) {
		if($debug == true) {
			$output = __('<br /><strong>Debug Report :<br /></strong><small>( In-case you see this you have some problem kindly used this data to report me or fix things yourself remember in always here to help. )</small><br />', 'google-analytics-popular-posts');
			$output .= "<pre>$e</pre>";
		}
		else {
			if(stristr($e, "Invalid value for ids parameter"))
				$output = __('<b>Google Analytics Popular Posts Alert :</b><br />Please check/recheck/enter your Google Analytics Profile ID.', 'google-analytics-popular-posts');
			elseif(stristr($e, "Failed to request report data"))
				$output = __('<b>Google Analytics Popular Posts Alert :</b><br />Please check/recheck/enter your Google Analytics Profile ID.', 'google-analytics-popular-posts');
			elseif(stristr($e, "Failed to authenticate user"))
				$output = __('<b>Google Analytics Popular Posts Alert :</b><br />Please check/recheck/enter your Google Analytics account details (username and password).', 'google-analytics-popular-posts');
			else
				$output = __('<b>Google Analytics Popular Posts Alert :</b><br />Unknown error please contact me at <a href=\"http://yaima.sakuraweb.com/plug-ins/google-analytics-popular-posts/#comments\">Google Analytics Popular Posts plugin page</a> if you find this error/message.', 'google-analytics-popular-posts');
		}
	}
	echo $output;
}

//**************************************************************************************
//  Widget output
//**************************************************************************************
function GoogleAnalyticsPopularPosts_widget_output() {
	$GAPP_usr = get_option('GoogleAnalyticsPopularPosts_username');
	$GAPP_pwd = get_option('GoogleAnalyticsPopularPosts_password');
	$GAPP_pID = get_option('GoogleAnalyticsPopularPosts_profileID');
	$GAPP_mRs = get_option('GoogleAnalyticsPopularPosts_maxResults');
	$GAPP_SDs = get_option('GoogleAnalyticsPopularPosts_statsSinceDays');
	$GAPP_filter = get_option('GoogleAnalyticsPopularPosts_filter');
	$GAPP_dDisp = get_option('GoogleAnalyticsPopularPosts_dateDispEnable');
	$GAPP_pDisp = get_option('GoogleAnalyticsPopularPosts_postDateEnable');
	$GAPP_cView = get_option('GoogleAnalyticsPopularPosts_contentsViewEnable');
	if(is_numeric($GAPP_SDs)) {
		$todays_year = date("Y");
		$todays_month = date("m");
		$todays_day = date("d");
		$date = "$todays_year-$todays_month-$todays_day";
		$newdate = strtotime ( "-$GAPP_SDs day" , strtotime ( $date ) ) ;
		$newdate = date ( 'Y-m-d' , $newdate );
		$From = $newdate;
	}
	define('ga_email', $GAPP_usr);
	define('ga_password', $GAPP_pwd);
	define('ga_profile_id', $GAPP_pID);
	if(!ga_email || !ga_password || !ga_profile_id) {
		$output = __('<b>Google Analytics Popular Posts Error :</b><br />Please enter your account details in the options page.', 'google-analytics-popular-posts');
		return $output;
	}
	$GAPP_filter_fixed = 'ga:pagePath=~^/';
	require 'gapi.class.php';
	$ga = new gapi(ga_email, ga_password);
	$ga->requestReportData(ga_profile_id, array('hostname', 'pagePath'), array('visits'), array('-visits'), $filter=$GAPP_filter_fixed.$GAPP_filter, $start_date=$From, $end_date=$date, $start_index=1, $max_results=$GAPP_mRs);
	if ($GAPP_dDisp == "yes") {
		$output = '<p class="popular_stats_date">'.$From.' ～ '.$date.'</p>'."\n";
	}
	foreach($ga->getResults() as $result) :
		$getHostname = $result->getHostname();
		$getPagepath = $result->getPagepath();
		$postPagepath = 'http://'.$getHostname.$getPagepath;
		$getPostID = url_to_postid($postPagepath);
		if ($getPostID <= 0) {
			$titleStr = $postPagepath;
			$output .= '<ul>'."\n";
			$output .= '<li>'."\n";
			$output .= '<div class="popular_post"><a href='.$postPagepath.'>'.$titleStr.'</a></div>'."\n";
			$output .= '</li>'."\n";
			$output .= '</ul>'."\n";
		}
		else {
			$titleStr = get_the_title($getPostID);
			$post = get_post($getPostID);
			$dateStr = mysql2date('Y-m-d', $post->post_date);
			$contentStr = strip_tags(mb_substr($post->post_content, 0, 60));
			$output .= '<ul>'."\n";
			$output .= '<li>'."\n";
			$output .= '<div class="popular_post"><a href='.$postPagepath.'>'.$titleStr.'</a><br />'."\n";
			if ($GAPP_pDisp == "yes" and $GAPP_cView == "yes") {
				$output .= '<div class="popular_post_date">'.$dateStr.'<br /></div>'."\n";
				$output .= '<div class="popular_post_contents">'.$contentStr.' ...'.'</div>'."\n";
			}
			elseif ($GAPP_pDisp == "yes" and $GAPP_cView == "no") {
				$output .= '<div class="popular_post_date">'.$dateStr.'<br /></div>'."\n";
			}
			elseif ($GAPP_pDisp == "no" and $GAPP_cView == "yes") {
				$output .= '<div class="popular_post_contents">'.$contentStr.' ...'.'</div>'."\n";
			}
			else {
			}
			$output .= '</div>'."\n";
			$output .= '</li>'."\n";
			$output .= '</ul>'."\n";
		}
	endforeach
?>
<?php
	return $output;
}

//**************************************************************************************
// Gets the present plugin version
//**************************************************************************************
 function GoogleAnalyticsPopularPosts_plugin_get_version() {
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}

//**************************************************************************************
// Load  or unload custom style sheet
//**************************************************************************************
function loadCSS() {
	$style = get_template_directory().'/google-analytics-popular-posts.css';
	 if (is_file($style)) {
		$style = get_bloginfo('stylesheet_directory').'/google-analytics-popular-posts.css';
	} else {
		$url = WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__));
		$style = $url.'/google-analytics-popular-posts.css';
	}
	echo "<!--Google Analytics Popular Posts plugin-->\n";
	echo '<link rel="stylesheet" type="text/css" media="all" href="'.$style.'">';
}
$GAPP_css = get_option('GoogleAnalyticsPopularPosts_cssEnable');
if ($GAPP_css == "yes") {
	add_action('wp_head', 'loadCSS');
} else {
	remove_action('wp_head', 'loadCSS');
}

//**************************************************************************************
// Implementation of the short code
//**************************************************************************************
function GAPP_shortcode() {
	$output = GoogleAnalyticsPopularPosts_view(true);
	return $output;
}
add_shortcode('GAPP_VIEW', 'GAPP_shortcode');

?>