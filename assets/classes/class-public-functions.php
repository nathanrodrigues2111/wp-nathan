<?php

class WpnatPublicFunctions
{

    public static $instance = null;

    public static function get_instance()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }
        self::$instance = new self();
        return self::$instance;
    }

    public function __construct()
    {
        
        add_filter('comments_template', [ $this, 'wpnat_comment_template' ]);
        add_filter("wp_enqueue_scripts", [ $this, 'wpnat_enque_public_styles_and_scripts' ]);
    }

    /**
     * Enqueue public styles and scripts
    */

    public function wpnat_enque_public_styles_and_scripts($hook)
    {
        if (!is_singular()) {
            return;
        }

            /**
         * Enqueue public styles and scripts
         */
        wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
        wp_enqueue_style('public-styles', WPNAT_URL . 'assets/css/public-style.css');
        wp_enqueue_script('public-js', WPNAT_URL . 'assets/js/public-js.js', array(), true);
    }

    /**
    * Editing comments section
    */

    public function wpnat_comment_template($comment_template)
    {
        if (get_option('enable_comments') == 'false' || get_option('selected_theme') == 'regular') {
            return;
        }

        global $post;
        if (!( is_singular() && ( have_comments() || 'open' == $post->comment_status ) )) {
            return;
        }

        return WPNAT_PATH . 'includes/comments.php';
    }
}

WpnatPublicFunctions::get_instance();
