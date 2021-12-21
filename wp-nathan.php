<?php
/**
 * Plugin Name:  WP Nathan
 * Description:  Small description
 * Plugin URI:   ""
 * Author:       Nathan Rodrigues
 * Version:      1.0
 * Text Domain:  wp-nathan
 * Domain Path:  /languages
 * License:      GPL v2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.txt
*/


// exit if file is called directly
if (! defined('ABSPATH') ) {

    exit;

}

define('WPNAT_PATH', trailingslashit(plugin_dir_path(__FILE__)));
define('WPNAT_URL', trailingslashit(plugins_url('/', __FILE__)));
define('WPNAT_TETXDOMAIN', 'wp-nathan');
define('WPNAT_PLUGIN_NAME', 'WP Nathan');

/**
 * Class Wpnat_Core_Functions.
 */

if (! class_exists('Wpnat_Core_Functions') ) {

    class Wpnat_Core_Functions {

        public static $instance = null;

        public static function get_instance() {
            if (self::$instance !== null ) {
                return self::$instance;
            }
            self::$instance = new self();
            return self::$instance;
        }

        public function __construct() {
            add_action('admin_menu', [ $this, 'wpnat_add_toplevel_menu' ]);
            add_action('admin_enqueue_scripts', [ $this, 'wpnat_ajax_admin_enqueue_scripts' ]);
            add_action('wp_ajax_admin_hook', [ $this, 'wpnat_ajax_admin_handler' ]);
            add_filter('comments_template', [ $this, 'wpnat_comment_template' ]);
            add_filter("wp_enqueue_scripts", [ $this, 'wpnat_enque_public_styles_and_scripts' ]);
        }

        function wpnat_ajax_admin_enqueue_scripts( $hook ) {
            // check if our page
            if ('toplevel_page_wpnat-settings' !== $hook ) { return; }

            // define script url
            $style_url = WPNAT_URL . 'admin/css/admin.css';

            // define style url
            $script_url = WPNAT_URL . 'admin/js/ajax-admin.js';

            $font_url = 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap';

            // enqueue styles
            wp_enqueue_style('google-fonts', $font_url);
            wp_enqueue_style('ajax-admin-styles', $style_url);

            // enqueue script
            wp_enqueue_script('ajax-admin', $script_url, array(), true);

            // create nonce
            $nonce = wp_create_nonce('wp-nathan');

            // define script
            $script = array(
            'nonce' => $nonce,
            'enableComments' => get_option('enable_comments'),
            );

            // localize script
            wp_localize_script('ajax-admin', 'ajax_admin', $script);

        }

        function wpnat_enque_public_styles_and_scripts( $hook ) {
            if (!is_singular()) { return; }

            wp_enqueue_style('public-styles', WPNAT_URL . 'assets/css/public-style.css');
            wp_enqueue_script('public-js', WPNAT_URL . 'assets/js/public-js.js', array(), true);
        }

        // process ajax request
        public function wpnat_ajax_admin_handler() {

            // check nonce
            //check_ajax_referer( 'ajax_admin', 'nonce' );

            // check user
            if (! current_user_can('manage_options') ) { return;
            }

            // define the url
            $selected_theme = isset($_POST['selected_theme']) ? sanitize_text_field($_POST['selected_theme']) : false;
            $enable_comments = isset($_POST['enable_comments']) ? sanitize_text_field($_POST['enable_comments']) : false;

            update_option('selected_theme', $selected_theme);

            update_option('enable_comments', $enable_comments);

            return true;

            // end processing
            wp_die();

        }

        function wpnat_comment_template( $comment_template )
        {
            if (get_option('enable_comments') == 'false' || get_option('selected_theme') == 'regular' ) { return;
            }

            global $post;
            if (!( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) ) {
                return;
            }

            return WPNAT_PATH . 'includes/comments.php';
        }


        // add top-level administrative menu
        public function wpnat_add_toplevel_menu() {

            /*
            add_menu_page(
            string   $page_title,
            string   $menu_title,
            string   $capability,
            string   $menu_slug,
            callable $function = '',
            string   $icon_url = '',
            int      $position = null
            )
            */

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
                                <th scope="row">Enable comment styles</th>
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
                                        Comment theme
                                    </label>
                                </th>
                                <td>
                                <select name="default_comments_theme" id="default_comments_theme">
                                    <option value="regular" <?php selected($selected_theme, 'regular'); ?>>Regular theme</option>
                                    <option value="modern" <?php selected($selected_theme, 'modern'); ?>>Modern theme</option>
                                    <option value="classic" <?php selected($selected_theme, 'classic'); ?>>Classic theme</option>
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

    Wpnat_Core_Functions::get_instance();
}
