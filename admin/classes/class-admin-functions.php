<?php

class Wpnat_Admin_Functions {
    public static $instance = null;

    public static function get_instance() {
        if(self::$instance !== null ) {
            return self::$instance;
        }
        self::$instance = new self();
        return self::$instance;
    }

    public function __construct() {
        add_action('admin_menu', [ $this, 'wpnat_add_toplevel_menu' ]);
        add_action('admin_enqueue_scripts', [ $this, 'wpnat_ajax_admin_enqueue_scripts' ]);
        add_action('wp_ajax_admin_hook', [ $this, 'wpnat_ajax_admin_handler' ]);
    }

    /**
    * Enqueue admin styles and scripts
    */

    function wpnat_ajax_admin_enqueue_scripts( $hook ) {
        /**
         * Check our page
         */
        if ('toplevel_page_wpnat-settings' !== $hook ) { return; }


        wp_enqueue_style('ajax-admin-styles', WPNAT_URL . 'admin/css/admin.css');
        wp_enqueue_script('ajax-admin', WPNAT_URL . 'admin/js/admin.js', array(), true);

        /**
        * Creating a nounce for security
        */
        $nonce = wp_create_nonce('wp-nathan');

        $script = array(
        'nonce' => $nonce,
        'enableComments' => get_option('enable_comments'),
        );

        /**
        * Localize script
        */
        wp_localize_script('ajax-admin', 'ajax_admin', $script);

    }

    /**
    * Handles ajax request
    */
    public function wpnat_ajax_admin_handler() {

        // check nonce
        //check_ajax_referer( 'ajax_admin', 'nonce' );

        /**
        * Checks if current user has required permissions 
        */
        if (! current_user_can('manage_options') ) { return;
        }

        $selected_theme = isset($_POST['selected_theme']) ? sanitize_text_field($_POST['selected_theme']) : false;
        $enable_comments = isset($_POST['enable_comments']) ? sanitize_text_field($_POST['enable_comments']) : false;

        update_option('selected_theme', $selected_theme);
        update_option('enable_comments', $enable_comments);

        return true;

        wp_die();

    }

    /**
    * Adding top level menu
    */
    public function wpnat_add_toplevel_menu() {

        $capability = 'manage_options';
        $slug = 'wpnat-settings';

        add_menu_page(
            __(WPNAT_PLUGIN_NAME, WPNAT_TETXDOMAIN),
            __(WPNAT_PLUGIN_NAME, WPNAT_TETXDOMAIN),
            $capability,
            $slug,
            [ $this, 'wpnat_display_settings_page' ],
            "",
        );

    }

    /**
    * Displays settings page in admin
    */
    public function wpnat_display_settings_page() {
        ob_start();
        ?>
        <div class="wrap">
            <h1><?php echo WPNAT_PLUGIN_NAME ?></h1>

            <form class="ajax-form" method="post">
                <table class="form-table" role="presentation">
                <?php
                    $selected_theme = get_option('selected_theme');
                    $enable_comments = get_option('enable_comments');
                    $isChecked = false;
                    if ($enable_comments === 'true') {
                        $isChecked = true;
                    } else {
                        $isChecked = false;
                    }
                ?>
                    <tbody>
                        <tr>
                            <th scope="row"><?php _e('Enable comment styles', WPNAT_TETXDOMAIN); ?></th>
                            <td>
                                <input name="enable_comment_styles"
                                type="checkbox"
                                id="enable_comment_styles"
                                value="1"
                                <?php checked($isChecked, 1); ?>>
                            </td>
                        </tr>

                        <tr class="toggle-comment-settings 
                            <?php if ($enable_comments === 'false') { ?>
                                hide-target 
                            <?php } ?>" >
                            <th scope="row">
                                <label for="default_comments_theme">
                                    <?php _e('Comment theme', WPNAT_TETXDOMAIN); ?>
                                </label>
                            </th>
                            <td>
                            <select name="default_comments_theme" id="default_comments_theme">
                                <option value="regular" <?php selected($selected_theme, 'regular'); ?>><?php _e('Regular theme', WPNAT_TETXDOMAIN); ?></option>
                                <option value="modern" <?php selected($selected_theme, 'modern'); ?>><?php _e('Modern theme', WPNAT_TETXDOMAIN); ?></option>
                            </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                </p>
            </form>
        </div>
        <?php
        ob_get_contents();
    }

}

Wpnat_Admin_Functions::get_instance();
