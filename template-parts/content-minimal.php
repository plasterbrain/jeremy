<?php
/**
 * Template part for displaying only page content, no bells or whistles.
 * Good for pages that allow the user to edit only a little bit, like login forms.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @package Jeremy
 */

the_content();
if ( get_edit_post_link() ) : ?>
    <footer>
        <?php
            edit_post_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: Name of current post. Only visible to screen readers */
                        __( 'Edit <span class="screen-reader-text">%s</span>', 'jeremy' ),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    get_the_title()
                ),
                '<span class="edit-link">',
                '</span>'
            );
        ?>
    </footer>
<?php endif; ?>