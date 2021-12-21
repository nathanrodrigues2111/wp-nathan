<?php

class WpnatWalkerComment extends Walker_Comment
{
    /**
     * Output a comment in the HTML5 format.
     *
     * @access protected
     * @since  1.0.0
     *
     * @see wp_list_comments()
     *
     * @param object $comment Comment to display.
     * @param int    $depth   Depth of comment.
     * @param array  $args    An array of arguments.
     */

    private function wpnat_time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
    
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
    
        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }
    
        if (!$full) {
            $string = array_slice($string, 0, 1);
        }
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    protected function html5_comment($comment, $depth, $args)
    {
        $count = 0;
        $childcomments = get_comments(array(
            'post_id'   => get_the_ID(),
            'status'    => 'approve',
            'parent'    => $comment->comment_ID,
            'count'     => true
        ));

        $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';

        $commenter          = wp_get_current_commenter();
        $show_pending_links = ! empty($commenter['comment_author']);

        if ($commenter['comment_author_email']) {
             $moderation_note = __('Your comment is awaiting moderation.');
        } else {
            $moderation_note = __('Your comment is awaiting moderation. This is a preview; your comment will be visible after it has been approved.');
        }
        ?>
        <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class($this->has_children ? 'parent' : 'last', $comment); ?>>
            <article id="div-comment-<?php comment_ID(); ?>" class="wpnat-comment-body">
                <?php if ($this->has_children && $depth === 1) { ?>
                    <div class="wpnat-comment-arrow-trigger">
                        <div class="wpnat-comments-count">0</div>
                        <svg class="wpnat-angle-arrow" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" class="">
                            <path fill="#BBC7D5" d="M0.146154,2.18291378 L0.146154,2.18291378 C-0.048718,2.38535378 -0.048718,2.71309378 0.146154,2.91501878 L4.2779365,7.19633378 C4.66818,7.60121878 5.301264,7.60121878 5.691507,7.19633378 L9.85377001,2.88395378 C10.046643,2.68357878 10.0491415,2.35998378 9.85876651,2.15702378 C9.66439451,1.94992378 9.34410501,1.94733378 9.14673501,2.15132878 L5.3382395,6.09817878 C5.142868,6.30061878 4.826576,6.30061878 4.6312045,6.09817878 L0.852689501,2.18291378 C0.657817501,1.98046878 0.3415255,1.98046878 0.146154,2.18291378"></path>
                        </svg>
                    </div>
                <?php } ?>
                <div class="wpnat-count" hidden><?php echo $depth - 1 ?></div>
                <footer class="wpnat-comment-meta">
                    <div class="wpnat-comment-author vcard">
        <?php
        if (0 != $args['avatar_size']) {
            echo get_avatar($comment, $args['avatar_size']);
        }
        ?>
        <?php
        $comment_author = get_comment_author_link($comment);

        if ('0' == $comment->comment_approved && ! $show_pending_links) {
            $comment_author = get_comment_author($comment);
        }

        printf(
            /* translators: %s: Comment author link. */
            __('%s'),
            sprintf('<b class="fn">%s</b>', $comment_author)
        );
        ?>
        
            <div class="wpnat-comment-metadata">
            <?php
            printf(
                '<a href="%s"><time datetime="%s">%s</time></a>',
                esc_url(get_comment_link($comment, $args)),
                get_comment_time('c'),
                sprintf(
                    /* translators: 1: Comment date, 2: Comment time. */
                    $this->wpnat_time_elapsed_string(get_comment_date('', $comment) . ' ' . get_comment_time())
                )
            );

                            edit_comment_link(__('Edit'), ' <span class="edit-link">', '</span>');
            ?>
            </div><!-- .comment-metadata -->
                    </div><!-- .comment-author -->


        <?php if ('0' == $comment->comment_approved) : ?>
                    <em class="wpnat-comment-awaiting-moderation"><?php echo $moderation_note; ?></em>
        <?php endif; ?>
                </footer><!-- .comment-meta -->

                <div class="wpnat-comment-content">
        <?php comment_text(); ?>
                </div><!-- .comment-content -->

        <?php
        if ('1' == $comment->comment_approved || $show_pending_links) {
            comment_reply_link(
                array_merge(
                    $args,
                    array(
                    'add_below' => 'div-comment',
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                    'before'    => '<div class="reply">',
                    'after'     => '</div>',
                    )
                )
            );
        }
        ?>
            </article><!-- .comment-body -->
        <?php
    }
}
