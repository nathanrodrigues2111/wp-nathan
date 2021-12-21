<?php

/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password,
 * return early without loading the comments.
 */
if (post_password_required()) {
    return;
}

$wpnat_one_comment_count = get_comments_number();
$comment_body = __('Add a comment');
$comment_author = __('Add your name');
$comment_email = __('Add your email');
$comment_url = __('Add your website url');
?>

<div id="comments" class="wpnat-comments-area comments-area <?php if (get_option('selected_theme')) {
    echo get_option('selected_theme');
                                                            } ?> default-max-width <?php echo get_option('show_avatars') ? 'show-avatars' : ''; ?>">

    <?php
    if (have_comments()) :
        comment_form(
            array(
            'logged_in_as'       => null,
            'title_reply'        => esc_html__('', WPNAT_TETXDOMAIN),
            'title_reply_before' => '',
            'title_reply_after'  => '',
            'label_submit' => 'Comment',
            'comment_field' => '<p class="wpnat-comment-form-comment wpnat-common-input"><textarea id="comment" name="comment" aria-required="true" placeholder="' . $comment_body . '"></textarea></p>',
            'fields' => array(
                //Author field
                'author' => '<div class="wpnat-row"><p class="comment-form-author wpnat-common-input"><input id="author" name="author" aria-required="true" placeholder="' . $comment_author . '"></input></p>',
                //Email Field
                'email' => '<p class="comment-form-email wpnat-common-input"><input id="email" name="email" placeholder="' . $comment_email . '"></input></p></div>',
                //URL Field
                'url' => '<p class="comment-form-url wpnat-common-input"><input id="url" name="url" placeholder="' . $comment_url . '"></input></p>',
            ),
            )
        );
        ?>

        <ol class="wpnat-comment-list">
        <?php
        if (! class_exists('WpnatWalkerComment')) {
            include_once WPNAT_PATH . 'includes/class-comments-walker.php';

            wp_list_comments(
                array(
                'style'         => 'ol',
                'max_depth'     => '',
                'short_ping'    => true,
                'avatar_size'   => '46',
                'walker'        => new WpnatWalkerComment(),
                )
            );
        }
        ?>
        </ol><!-- .comment-list -->

        <?php if (! comments_open()) : ?>
            <p class="wpnat-no-comments"><?php esc_html_e('Comments are closed.', WPNAT_TETXDOMAIN); ?></p>
        <?php endif; ?>
    <?php endif; ?>


</div><!-- #comments -->
