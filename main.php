<?php
/**
 * Plugin Name: Mashery Developer Portal Integration
 * Plugin URI: https://github.com/lgomez/mashery-developer-portal
 * Description: Mashery's Wordpress-powered Developer Portal Integration
 * Version: 1.0.0
 * Author: Mashery, Inc.
 * Author URI: http://mashery.com
 * License: Copyright 2015 Mashery, Inc. - All rights reserved.
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Mashery {
    private $options;

    public function __construct() {

        add_shortcode( 'mashery:account', array(__CLASS__, 'account') );
        add_shortcode( 'mashery:apis', array(__CLASS__, 'apis') );
        add_shortcode( 'mashery:applications', array(__CLASS__, 'applications') );
        add_shortcode( 'mashery:keys', array(__CLASS__, 'keys') );

        register_activation_hook(__FILE__, array(__CLASS__, 'activation'));
        register_deactivation_hook(__FILE__, array(__CLASS__, 'deactivation'));

        if ( is_admin() ) {
            // add_filter('plugin_action_links', array( $this, 'settings_link' ));
            add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
            add_action( 'admin_init', array( $this, 'page_init' ) );
        }

    }

    function activation() {
        add_role( 'developer', 'Developer', array( 'level_1' => true ) );
        $role = get_role( 'developer' );
        $role->add_cap( 'manage_developer_data' );
        // update_option($this->option_name, $this->data);
        $parent = self::generate_page("Account", "account", "[mashery:account]");
        self::generate_page("APIs", "apis", "[mashery:apis]", $parent);
        self::generate_page("Applications", "applications", "[mashery:applications]", $parent);
        self::generate_page("Keys", "keys", "[mashery:keys]", $parent);
    }

    function deactivation() {
        $role = get_role( 'developer' );
        $role->remove_cap( 'manage_developer_data' );
        remove_role( 'developer' );

        self::trash_page("account");
        self::trash_page("apis");
        self::trash_page("applications");
        self::trash_page("keys");
    }

    public function generate_page($title, $name, $content, $parent=0){
        // https://wordpress.org/support/topic/how-do-i-create-a-new-page-with-the-plugin-im-building
        // delete_option("mashery_" . $name . "_page_title");
        // delete_option("mashery_" . $name . "_page_name");
        // delete_option("mashery_" . $name . "_page_id");
        // add_option("mashery_" . $name . "_page_title", $title, '', 'yes');
        // add_option("mashery_" . $name . "_page_name", $name, '', 'yes');
        // add_option("mashery_" . $name . "_page_id", '0', '', 'yes');
        $page = get_page_by_title( $title );
        if (!$page) {
            $page = array(
                'post_title'     => $title,
                'post_content'   => $content,
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'comment_status' => 'closed',
                'ping_status'    => 'closed',
                'post_parent'    => $parent
            );
            $id = wp_insert_post( $page );
            add_option("mashery_" . $name . "_page_id", $id );
        } else {
            $id = $page->ID;
            $page->post_status = 'publish';
            $id = wp_update_post( $page );
        }
        return $id;
    }

    public function trash_page($name){
        // https://wordpress.org/support/topic/how-do-i-create-a-new-page-with-the-plugin-im-building
        $id = get_option( "mashery_" . $name . "_page_id" );
        if( $id ) {
            wp_delete_post( $id );
        }
        // delete_option("mashery_" . $name . "_page_title");
        // delete_option("mashery_" . $name . "_page_name");
        delete_option("mashery_" . $name . "_page_id");
    }

    public function shortcode($shortcode, $data){
        $templatefile = dirname(__FILE__) . "/templates/" . $shortcode . ".php";
        $data = $data;
        if(file_exists($templatefile)){
            ob_start();
            include($templatefile);
            return ob_get_clean();
        } else {
            return "template not implemented please add one to plugin/templates/";
        }
    }

    public function account(){
        return self::shortcode(__FUNCTION__, array(
            "first_name" => "Stephen",
            "last_name" => "Colbert",
            "email" => "scolbert@mashery.com",
            "twitter" => "@scolbert",
            "phone" => "(415) 555-1212"
        ));
    }

    public function apis(){
        return self::shortcode(__FUNCTION__, array(
            array("name" => "Application 1", "key" => "765rfgi8765rdfg8765rtdfgh76rdtcf"),
            array("name" => "Application 2", "key" => "hrydht84g6bdr4t85rd41tg6rs4g56r"),
            array("name" => "Application 3", "key" => "87946t4hdr8y6h4td5y4dt8y4dyt6yh84d")
        ));
    }

    public function applications(){
        return self::shortcode(__FUNCTION__, array(
            array("name" => "Application 1", "key" => "765rfgi8765rdfg8765rtdfgh76rdtcf"),
            array("name" => "Application 2", "key" => "hrydht84g6bdr4t85rd41tg6rs4g56r"),
            array("name" => "Application 3", "key" => "87946t4hdr8y6h4td5y4dt8y4dyt6yh84d")
        ));
    }

    public function keys(){
        return self::shortcode(__FUNCTION__, array(
            array("name" => "Key 1", "key" => "765rfgi8765rdfg8765rtdfgh76rdtcf"),
            array("name" => "Key 2", "key" => "hrydht84g6bdr4t85rd41tg6rs4g56r"),
            array("name" => "Key 3", "key" => "87946t4hdr8y6h4td5y4dt8y4dyt6yh84d")
        ));
    }

    function settings_link($links) {
        $mylinks = array('<a href="' . admin_url( 'options-general.php?page=mashery' ) . '">Settings</a>');
        return array_merge( $links, $mylinks );
    }

    public function add_plugin_page() {
        add_options_page('Mashery', 'Mashery', 'manage_options', 'mashery', array( $this, 'create_admin_page' ) );
    }

    public function create_admin_page() {
        $this->options = get_option( 'my_option_name' );
        ?>
        <div class="wrap">
            <h2>Mashery</h2>
            <form method="post" action="options.php">
            <?php
                settings_fields( 'my_option_group' );
                do_settings_sections( 'mashery' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting( 'my_option_group', 'my_option_name', array( $this, 'sanitize' ) );
        add_settings_section( 'mashery-settings-form', 'Developer Portal Integration Settings', array( $this, 'print_section_info' ), 'mashery' );
        add_settings_field( 'mashery_customer_id', 'Area ID', array( $this, 'mashery_customer_id_callback' ), 'mashery', 'mashery-settings-form' );
        add_settings_field( 'mashery_customer_uuid', 'Area UUID', array( $this, 'mashery_customer_uuid_callback' ), 'mashery', 'mashery-settings-form' );
        add_settings_field( 'mashery_access_key', 'Admin API Key', array( $this, 'mashery_access_key_callback' ), 'mashery', 'mashery-settings-form' );
        add_settings_field( 'mashery_access_secret', 'Admin API Secret', array( $this, 'mashery_access_secret_callback' ), 'mashery', 'mashery-settings-form' );
        add_settings_field( 'mashery_access_username', 'Admin Username', array( $this, 'mashery_access_username_callback' ), 'mashery', 'mashery-settings-form' );
        add_settings_field( 'mashery_access_password', 'Admin Password', array( $this, 'mashery_access_password_callback' ), 'mashery', 'mashery-settings-form' );
    }

    public function sanitize( $input ) {
        $new_input = array();
        if( isset( $input['mashery_customer_id'] ) ) {
            $new_input['mashery_customer_id'] = sanitize_text_field( $input['mashery_customer_id'] );
        }
        if( isset( $input['mashery_customer_uuid'] ) ) {
            $new_input['mashery_customer_uuid'] = sanitize_text_field( $input['mashery_customer_uuid'] );
        }
        if( isset( $input['mashery_access_key'] ) ) {
            $new_input['mashery_access_key'] = sanitize_text_field( $input['mashery_access_key'] );
        }
        if( isset( $input['mashery_access_secret'] ) ) {
            $new_input['mashery_access_secret'] = sanitize_text_field( $input['mashery_access_secret'] );
        }
        if( isset( $input['mashery_access_username'] ) ) {
            $new_input['mashery_access_username'] = sanitize_text_field( $input['mashery_access_username'] );
        }
        if( isset( $input['mashery_access_password'] ) ) {
            $new_input['mashery_access_password'] = sanitize_text_field( $input['mashery_access_password'] );
        }
        return $new_input;
    }

    public function print_section_info() {
        print 'Enter your Mashery-provided authentication information here:';
    }

    public function mashery_customer_id_callback() {
        printf(
            '<input type="text" id="mashery_customer_id" name="my_option_name[mashery_customer_id]" value="%s" />',
            isset( $this->options['mashery_customer_id'] ) ? esc_attr( $this->options['mashery_customer_id']) : ''
        );
    }

    public function mashery_customer_uuid_callback() {
        printf(
            '<input type="text" id="mashery_customer_uuid" name="my_option_name[mashery_customer_uuid]" value="%s" />',
            isset( $this->options['mashery_customer_uuid'] ) ? esc_attr( $this->options['mashery_customer_uuid']) : ''
        );
    }

    public function mashery_access_key_callback() {
        printf(
            '<input type="text" id="mashery_access_key" name="my_option_name[mashery_access_key]" value="%s" />',
            isset( $this->options['mashery_access_key'] ) ? esc_attr( $this->options['mashery_access_key']) : ''
        );
    }

    public function mashery_access_secret_callback() {
        printf(
            '<input type="text" id="mashery_access_secret" name="my_option_name[mashery_access_secret]" value="%s" />',
            isset( $this->options['mashery_access_secret'] ) ? esc_attr( $this->options['mashery_access_secret']) : ''
        );
    }

    public function mashery_access_username_callback() {
        printf(
            '<input type="text" id="mashery_access_username" name="my_option_name[mashery_access_username]" value="%s" />',
            isset( $this->options['mashery_access_username'] ) ? esc_attr( $this->options['mashery_access_username']) : ''
        );
    }

    public function mashery_access_password_callback() {
        printf(
            '<input type="text" id="mashery_access_password" name="my_option_name[mashery_access_password]" value="%s" />',
            isset( $this->options['mashery_access_password'] ) ? esc_attr( $this->options['mashery_access_password']) : ''
        );
    }

}

new Mashery();
