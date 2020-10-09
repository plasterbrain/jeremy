<?php
/**
 * BuddyPress - Users Plugins Template
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 2.0.0
 */

do_action( 'bp_before_member_plugin_template' ); ?>

<?php if ( ! bp_is_current_component_core() && has_action( 'bp_member_plugin_options_nav' ) ) : ?>

<div class="item-list-tabs no-ajax" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation">
	<ul>
		<?php bp_get_options_nav(); ?>

		<?php

		/**
		 * Fires inside the member plugin template nav <ul> tag.
		 *
		 * @since 1.2.2
		 */
		do_action( 'bp_member_plugin_options_nav' ); ?>
	</ul>
</div><!-- .item-list-tabs -->

<?php endif; ?>

<?php if ( has_action( 'bp_template_title' ) ) { ?>
	<h2 class="profile__content__h2 profile__content-plugin__h2"><?php do_action( 'bp_template_title' ); ?></h2>
<?php } ?>

<?php
do_action( 'bp_template_content' ); ?>

<?php
do_action( 'bp_after_member_plugin_template' );