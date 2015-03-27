<?PHP
require_once ABSPATH . '/wp-admin/includes/file.php';
/*
Plugin Name: ChatUP
Plugin URI: http://chatup.it/wordpress
Description: Chat with operator integration: see http://chatup.it
Tags: operator chat, online operator, chat with operator, online support, live support, live operator, chat con operatore, supporto online
Text Domain: chatup
Author: hyppoCom
Author URI: http://hyppo.com/
Version: 1.1
License: GPL2

 * 
 * Copyright 2015 - hyppoCom
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
class ChatUP {

    private $__options;

    public function __construct() {
        $this->__setDefaultOptions();

        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Init hooks
        add_action('plugins_loaded', array($this, 'init'));
        add_action('admin_init', array($this, 'adminInit'));
        add_action('admin_menu', array($this, 'menuInit'));
	// add_shortcode('chatup',array($this, 'shortcodeChatup'));
        add_action('wp_print_footer_scripts', array($this, 'wpFooterScript'));
	add_action('widgets_init',array($this,'loadWidget'));
    }

    /**
     * Called once any activated plugins have been loaded.
     */
    public function init() {
        // Load textdomain for i18n
        load_plugin_textdomain('chatup', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * Called when a user accesses the admin area.
     */
    public function adminInit() {
        // Register plugin settings
        $this->__registerOptions();
    }

    /**
     * Called after the basic admin panel menu structure is in place.
     * 
     * @return void
     */
    public function menuInit() {
        // Add settings page to the Settings menu
        add_options_page(__('ChatUP Settings', 'chatup'), 'ChatUP', 'manage_options', 'chatup', array($this, 'showSettingsPage'));
    }

    /**
     * Called When the plugin is activated
     * 
     * @return boolean
     */
    public function activate() {
    }

    /**
     * Called When the plugin is deactivated
     * 
     * @return boolean
     */
    public function deactivate() {
	$this->__deleteOptions();
    }

    // Register and load the widget
    public function loadWidget() {
	register_widget('chatup_widget');
    }

    /**
     * Shows settings page and handles user actions.
     * 
     * @return void
     */
    public function showSettingsPage() {
	// echo "<pre>".print_r($_GET,1)."</pre>";
        $this->__fillOptions();
        include 'settings.php';
    }

    public function shortcodeChatup($arg) {
	return("<span id='chatup_but'></span>");
    }
    /*
     * Hook wp_footer
     */
    public function wpFooterScript($arg) {
	$cid=$this->__fillOption('campaign');
	$hide_na=$this->__fillOption('hide_na');
	$online_img=$this->__fillOption('online_img');
	$offline_img=$this->__fillOption('offline_img');
	$s="<script>\n".
	   "var _chatup=_chatup||[];\n".
	   "document.addEventListener('DOMContentLoaded',function(e) {\n".
	   "	var x=document.getElementById('chatup_but');\n".
	   "	if(x) {\n".
	   "		_chatup.push('setCampaign','$cid');\n".
	   ($hide_na?"		_chatup.push('setHideUnavailable',true);":"").
	   ($online_img?"	_chatup.push('setOnlineImage','$online_img');":"").
	   ((!$hide_na && $offline_img)?"	_chatup.push('setOfflineImage','$offline_img');":"").
	   "		_chatup.push('showButton','chatup_but');\n".
	   "		(function(c,h,a,t) { t=h.createElement(c); t.async=1; t.src='//chatup.it/chatup.js'; a=h.getElementsByTagName(c)[0]; a.parentNode.insertBefore(t,a) })('script',document);\n".
	   "	}\n".
	   "},false);\n".
	   "</script>";
	echo $s;
    }

    /**
     * Settings validation functions
     */

    public function validateCampaign($input) {
        if(is_numeric($input) && ($input >= 1 && $input <= 9999)) {
            return $input;
        } else {
            add_settings_error('chatup_campaign', 'chatup_campaign', __('Campaign ID must be between 0001 and 9999', 'chatup'));
            return($this->__fillOption('campaign'));
        }
    }

    /**
     * Private functions
     */

    /**
     * Sets default options into $__options
     * 
     * @return void
     */
    private function __setDefaultOptions() {
        $this->__options = array(
            'campaign'		=> 0,	// Campaign ID
            'hide_na'		=> 0,	// Hide if no operators available
            'online_img'	=> "",	// Image URL
            'offline_img'	=> "",	// Image URL
        );
    }

    /**
     * Registers options(settings).
     * 
     * @return void
     */
    private function __registerOptions() {
        // register_setting('chatup','chatup_campaign',array($this,'validateCampaign'));
        register_setting('chatup','chatup_campaign');
        register_setting('chatup','chatup_hide_na');
        register_setting('chatup','chatup_online_img');
        register_setting('chatup','chatup_offline_img');
    }

    /**
     * Deletes options from database.
     * 
     * @return void
     */
    private function __deleteOptions() {
        delete_option('chatup_campaign');
        delete_option('chatup_hide_na');
        delete_option('chatup_online_img');
        delete_option('chatup_offline_img');
    }

    /**
     * Fills options with value (from database).
     * 
     * @return void
     */
    private function __fillOptions() {
        $this->__fillOption('campaign');
        $this->__fillOption('hide_na');
        $this->__fillOption('online_img');
        $this->__fillOption('offline_img');
    }

    /**
     * Fills single option with value (from database).
     * 
     * @param string $name
     * @return void
     */
    private function __fillOption($name) {
        $this->__options[$name] = get_option('chatup_' . $name, $this->__options[$name]);
	return($this->__options[$name]);
    }

    /**
     * Echoes message with class 'updated'.
     * 
     * @param string $message
     * @return void
     */
    private function __showMessage($message) {
        echo '<div class="updated"><p>' . $message . '</p></div>';
    }

    /**
     * Echoes message with class 'error'.
     * 
     * @param string $message
     * @return void
     */
    private function __showError($message) {
        echo '<div class="error"><p>' . $message . '</p></div>';
    }

}
// Creating the widget 
class chatup_widget extends WP_Widget {

    function __construct() {
	parent::__construct(
		// Base ID of your widget
		'chatup_widget', 
		// Widget name will appear in UI
		__('ChatUP button', 'chatup'), 
		// Widget description
		array(
		'description' => __('Display ChatUP button', 'chatup'),
	  	) 
	);
    }

    // Creating widget front-end
    // This is where the action happens
    public function widget($args, $instance) {
	$title=apply_filters('widget_title', $instance['title']);
	// before and after widget arguments are defined by themes
	echo $args['before_widget'];
	if(!empty($title)) {
	    echo $args['before_title'] . $title . $args['after_title'];
	}
	// This is where you run the code and display the output
	// echo __('Parla con un operatore', 'chatup');
	echo "<span id='chatup_but'></span>";
	echo $args['after_widget'];
    }
		
    // Widget Backend 
    public function form($instance) {
	if(isset($instance[ 'title' ])) {
	    $title = $instance[ 'title' ];
	} else {
	    $title = __('', 'chatup');
	}
	// Widget admin form
	?>
	<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	</p>
	<?php 
    }
	
    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance) {
	$instance = array();
	$instance['title'] = (! empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
	return $instance;
    }
} // Class chatup_widget ends here

//Instantiate ChatUP class
new ChatUP();
