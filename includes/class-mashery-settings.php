<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Mashery_Settings {

    /**
     * The single instance of Mashery_Settings.
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static $_instance = null;

    /**
     * The main plugin object.
     * @var 	object
     * @access  public
     * @since 	1.0.0
     */
    public $parent = null;

    /**
     * Prefix for plugin settings.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $base = '';

    /**
     * Available settings for plugin.
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $settings = array();

    public function __construct ( $parent ) {
        $this->parent = $parent;

        $this->base = 'mashery_';

        // Initialise settings
        add_action( 'init', array( $this, 'init_settings' ), 11 );

        // Register plugin settings
        add_action( 'admin_init' , array( $this, 'register_settings' ) );

        // Add settings page to menu
        add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

        // Add settings link to plugins page
        add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
    }

    /**
     * Initialise settings
     * @return void
     */
    public function init_settings () {
        $this->settings = $this->settings_fields();
    }

    /**
     * Add settings page to admin menu
     * @return void
     */
    public function add_menu_item () {
        $page = add_options_page( __( 'Mashery Settings', 'mashery' ) , __( 'Mashery Settings', 'mashery' ) , 'manage_options' , $this->parent->_token . '_settings' ,  array( $this, 'settings_page' ) );
        add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
    }

    /**
     * Load settings JS & CSS
     * @return void
     */
    public function settings_assets () {

        // We're including the farbtastic script & styles here because they're needed for the colour picker
        // If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
        wp_enqueue_style( 'farbtastic' );
        wp_enqueue_script( 'farbtastic' );

        // We're including the WP media scripts here because they're needed for the image upload field
        // If you're not including an image upload then you can leave this function call out
        wp_enqueue_media();

        wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
        wp_enqueue_script( $this->parent->_token . '-settings-js' );
    }

    /**
     * Add settings link to plugin list table
     * @param  array $links Existing links
     * @return array 		Modified links
     */
    public function add_settings_link ( $links ) {
        $settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'mashery' ) . '</a>';
          array_push( $links, $settings_link );
          return $links;
    }

    /**
     * Build settings fields
     * @return array Fields to be displayed on settings page
     */
    private function settings_fields () {

        $settings['standard'] = array(
            'title'					=> __( 'Account', 'mashery' ),
            'description'			=> __( 'These are fairly standard form input fields.', 'mashery' ),
            'fields'				=> array(
                array(
                    'id' 			=> 'areaid',
                    'label'			=> __( 'Area ID' , 'mashery' ),
                    'description'	=> __( 'Area ID.', 'mashery' ),
                    'type'			=> 'text',
                    'default'		=> '',
                    'placeholder'	=> __( 'Area ID', 'mashery' )
                ),
                array(
                    'id' 			=> 'areauuid',
                    'label'			=> __( 'Area UUID' , 'mashery' ),
                    'description'	=> __( 'Area UUID.', 'mashery' ),
                    'type'			=> 'text',
                    'default'		=> '',
                    'placeholder'	=> __( 'Area UUID', 'mashery' )
                ),
                array(
                    'id' 			=> 'apikey',
                    'label'			=> __( 'Admin API Key' , 'mashery' ),
                    'description'	=> __( 'Admin API Key.', 'mashery' ),
                    'type'			=> 'text',
                    'default'		=> '',
                    'placeholder'	=> __( 'Admin API Key', 'mashery' )
                ),
                array(
                    'id' 			=> 'apisecret',
                    'label'			=> __( 'Admin API Secret' , 'mashery' ),
                    'description'	=> __( 'Admin API Secret.', 'mashery' ),
                    'type'			=> 'text',
                    'default'		=> '',
                    'placeholder'	=> __( 'Admin API Secret', 'mashery' )
                ),
                array(
                    'id' 			=> 'username',
                    'label'			=> __( 'Admin Username' , 'mashery' ),
                    'description'	=> __( 'Admin Username.', 'mashery' ),
                    'type'			=> 'text',
                    'default'		=> '',
                    'placeholder'	=> __( 'Admin Username', 'mashery' )
                ),
                array(
                    'id' 			=> 'password',
                    'label'			=> __( 'Admin Password' , 'mashery' ),
                    'description'	=> __( 'Admin Password.', 'mashery' ),
                    'type'			=> 'password',
                    'default'		=> '',
                    'placeholder'	=> __( 'Admin Password', 'mashery' )
                )
                // array(
                //     'id' 			=> 'password_field',
                //     'label'			=> __( 'A Password' , 'mashery' ),
                //     'description'	=> __( 'This is a standard password field.', 'mashery' ),
                //     'type'			=> 'password',
                //     'default'		=> '',
                //     'placeholder'	=> __( 'Placeholder text', 'mashery' )
                // ),
                // array(
                //     'id' 			=> 'secret_text_field',
                //     'label'			=> __( 'Some Secret Text' , 'mashery' ),
                //     'description'	=> __( 'This is a secret text field - any data saved here will not be displayed after the page has reloaded, but it will be saved.', 'mashery' ),
                //     'type'			=> 'text_secret',
                //     'default'		=> '',
                //     'placeholder'	=> __( 'Placeholder text', 'mashery' )
                // ),
                // array(
                //     'id' 			=> 'text_block',
                //     'label'			=> __( 'A Text Block' , 'mashery' ),
                //     'description'	=> __( 'This is a standard text area.', 'mashery' ),
                //     'type'			=> 'textarea',
                //     'default'		=> '',
                //     'placeholder'	=> __( 'Placeholder text for this textarea', 'mashery' )
                // ),
                // array(
                //     'id' 			=> 'single_checkbox',
                //     'label'			=> __( 'An Option', 'mashery' ),
                //     'description'	=> __( 'A standard checkbox - if you save this option as checked then it will store the option as \'on\', otherwise it will be an empty string.', 'mashery' ),
                //     'type'			=> 'checkbox',
                //     'default'		=> ''
                // ),
                // array(
                //     'id' 			=> 'select_box',
                //     'label'			=> __( 'A Select Box', 'mashery' ),
                //     'description'	=> __( 'A standard select box.', 'mashery' ),
                //     'type'			=> 'select',
                //     'options'		=> array( 'drupal' => 'Drupal', 'joomla' => 'Joomla', 'wordpress' => 'WordPress' ),
                //     'default'		=> 'wordpress'
                // ),
                // array(
                //     'id' 			=> 'radio_buttons',
                //     'label'			=> __( 'Some Options', 'mashery' ),
                //     'description'	=> __( 'A standard set of radio buttons.', 'mashery' ),
                //     'type'			=> 'radio',
                //     'options'		=> array( 'superman' => 'Superman', 'batman' => 'Batman', 'ironman' => 'Iron Man' ),
                //     'default'		=> 'batman'
                // ),
                // array(
                //     'id' 			=> 'multiple_checkboxes',
                //     'label'			=> __( 'Some Items', 'mashery' ),
                //     'description'	=> __( 'You can select multiple items and they will be stored as an array.', 'mashery' ),
                //     'type'			=> 'checkbox_multi',
                //     'options'		=> array( 'square' => 'Square', 'circle' => 'Circle', 'rectangle' => 'Rectangle', 'triangle' => 'Triangle' ),
                //     'default'		=> array( 'circle', 'triangle' )
                // )
            )
        );

        $settings['pages'] = array(
            'title'					=> __( 'Pages', 'mashery' ),
            'description'			=> __( 'Automatically generate a number of recommended pages.', 'mashery' ),
            'fields'				=> array(
                array(
                    'id' 			=> 'account_page',
                    'label'			=> __( 'Account', 'mashery' ),
                    'description'	=> __( 'User\'s account details page.', 'mashery' ),
                    'type'			=> 'checkbox',
                    'default'		=> ''
                ),
                array(
                    'id' 			=> 'apis_page',
                    'label'			=> __( 'APIs', 'mashery' ),
                    'description'	=> __( 'User\'s APIs page.', 'mashery' ),
                    'type'			=> 'checkbox',
                    'default'		=> ''
                ),
                array(
                    'id' 			=> 'apis_request_page',
                    'label'			=> __( 'APIs Request', 'mashery' ),
                    'description'	=> __( 'APIs Request page.', 'mashery' ),
                    'type'			=> 'checkbox',
                    'default'		=> ''
                ),
                array(
                    'id' 			=> 'applications_page',
                    'label'			=> __( 'Applications', 'mashery' ),
                    'description'	=> __( 'User\'s applications page.', 'mashery' ),
                    'type'			=> 'checkbox',
                    'default'		=> ''
                ),
                array(
                    'id' 			=> 'applications_new_page',
                    'label'			=> __( 'New Application', 'mashery' ),
                    'description'	=> __( 'User\'s New Application page.', 'mashery' ),
                    'type'			=> 'checkbox',
                    'default'		=> ''
                ),
                array(
                    'id' 			=> 'keys_page',
                    'label'			=> __( 'Keys', 'mashery' ),
                    'description'	=> __( 'User\'s Keys page.', 'mashery' ),
                    'type'			=> 'checkbox',
                    'default'		=> ''
                )
                // array(
                //     'id' 			=> 'number_field',
                //     'label'			=> __( 'A Number' , 'mashery' ),
                //     'description'	=> __( 'This is a standard number field - if this field contains anything other than numbers then the form will not be submitted.', 'mashery' ),
                //     'type'			=> 'text',
                //     'default'		=> '',
                //     'placeholder'	=> __( '42', 'mashery' )
                // ),
                // array(
                //     'id' 			=> 'colour_picker',
                //     'label'			=> __( 'Pick a colour', 'mashery' ),
                //     'description'	=> __( 'This uses WordPress\' built-in colour picker - the option is stored as the colour\'s hex code.', 'mashery' ),
                //     'type'			=> 'color',
                //     'default'		=> '#21759B'
                // ),
                // array(
                //     'id' 			=> 'an_image',
                //     'label'			=> __( 'An Image' , 'mashery' ),
                //     'description'	=> __( 'This will upload an image to your media library and store the attachment ID in the option field. Once you have uploaded an imge the thumbnail will display above these buttons.', 'mashery' ),
                //     'type'			=> 'image',
                //     'default'		=> '',
                //     'placeholder'	=> ''
                // ),
                // array(
                //     'id' 			=> 'multi_select_box',
                //     'label'			=> __( 'A Multi-Select Box', 'mashery' ),
                //     'description'	=> __( 'A standard multi-select box - the saved data is stored as an array.', 'mashery' ),
                //     'type'			=> 'select_multi',
                //     'options'		=> array( 'linux' => 'Linux', 'mac' => 'Mac', 'windows' => 'Windows' ),
                //     'default'		=> array( 'linux' )
                // )
            )
        );

        $settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

        return $settings;
    }

    /**
     * Register plugin settings
     * @return void
     */
    public function register_settings () {
        if ( is_array( $this->settings ) ) {

            // Check posted/selected tab
            $current_section = '';
            if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
                $current_section = $_POST['tab'];
            } else {
                if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
                    $current_section = $_GET['tab'];
                }
            }

            foreach ( $this->settings as $section => $data ) {

                if ( $current_section && $current_section != $section ) continue;

                // Add section to page
                add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

                foreach ( $data['fields'] as $field ) {

                    // Validation callback for field
                    $validation = '';
                    if ( isset( $field['callback'] ) ) {
                        $validation = $field['callback'];
                    }

                    // Register field
                    $option_name = $this->base . $field['id'];
                    register_setting( $this->parent->_token . '_settings', $option_name, $validation );

                    // Add field to page
                    add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'display_field' ), $this->parent->_token . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base ) );
                }

                if ( ! $current_section ) break;
            }
        }
    }

    public function settings_section ( $section ) {
        $html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
        echo $html;
    }

    /**
     * Load settings page content
     * @return void
     */
    public function settings_page () {

        // Build page HTML
        $html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
            $html .= '<h2>' . __( 'Mashery Settings' , 'mashery' ) . '</h2>' . "\n";

            $tab = '';
            if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
                $tab .= $_GET['tab'];
            }

            // Show page tabs
            if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

                $html .= '<h2 class="nav-tab-wrapper">' . "\n";

                $c = 0;
                foreach ( $this->settings as $section => $data ) {

                    // Set tab class
                    $class = 'nav-tab';
                    if ( ! isset( $_GET['tab'] ) ) {
                        if ( 0 == $c ) {
                            $class .= ' nav-tab-active';
                        }
                    } else {
                        if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
                            $class .= ' nav-tab-active';
                        }
                    }

                    // Set tab link
                    $tab_link = add_query_arg( array( 'tab' => $section ) );
                    if ( isset( $_GET['settings-updated'] ) ) {
                        $tab_link = remove_query_arg( 'settings-updated', $tab_link );
                    }

                    // Output tab
                    $html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

                    ++$c;
                }

                $html .= '</h2>' . "\n";
            }

            $html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

                // Get settings fields
                ob_start();
                settings_fields( $this->parent->_token . '_settings' );
                do_settings_sections( $this->parent->_token . '_settings' );
                $html .= ob_get_clean();

                $html .= '<p class="submit">' . "\n";
                    $html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
                    $html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'mashery' ) ) . '" />' . "\n";
                $html .= '</p>' . "\n";
            $html .= '</form>' . "\n";
        $html .= '</div>' . "\n";

        echo $html;
    }

    /**
     * Main Mashery_Settings Instance
     *
     * Ensures only one instance of Mashery_Settings is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see Mashery()
     * @return Main Mashery_Settings instance
     */
    public static function instance ( $parent ) {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $parent );
        }
        return self::$_instance;
    } // End instance()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone () {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
    } // End __clone()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup () {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
    } // End __wakeup()

}
