<?php
/**
 * BuddyPress - Users Notifications
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 2.0.0
 */

$current_order = 'DESC'; 
if ( ! empty( $_REQUEST['order'] ) ) {
	// phpcs:disable
	if ( in_array( $_REQUEST['order'], array( 'DESC', 'ASC' ) ) ) { 
		$current_order = $_REQUEST['order']; 
	}
	// phpcs:enable
}

$inbox_type = bp_current_action();
?>

<h1><?php esc_html_e( 'Notifications', 'jeremy' ); ?></h1>
<nav class="nav-profile" id="subnav" aria-label="<?php esc_attr_e( 'Notifications menu', 'jeremy' ); ?>">
	<ul class="nav__list nav__list-h">
		<?php bp_get_options_nav(); ?>
	</ul>
</nav>

<?php if ( in_array( $inbox_type, array( 'read', 'unread' ) ) ) {
	if ( $inbox_type === 'unread' ) {
		$inbox_title = __( 'Unread', 'jeremy' );
		$inbox_title_none = __( "You're all caught up!", 'jeremy' );
		$inbox_title_none_admin = __( "This member has no unread notifications.", 'jeremy' );
	} else {
		$inbox_title = __( 'Everything else', 'jeremy' );
		$inbox_title_none = __( "You have no saved notifications.", 'jeremy' );
		$inbox_title_none_admin = __( "This member has no saved notifications.", 'jeremy' );
	} ?>
	<?php if ( bp_has_notifications() ) {?>
		<h2 class="inbox__title-<?php echo esc_attr( $inbox_type ); ?>"><?php echo esc_html( $inbox_title ); ?></h2>
		
		<form class="flex" action="" method="get" id="notifications-sort-order" name="sort_order">
			<label for="notifications-sort-order-list"><?php esc_html_e( 'Sort by:', 'jeremy' ); ?></label> 
			<div class="flex">
				<input onchange="this.form.submit();" type="radio" <?php checked( $current_order, 'DESC' ); ?> id="DESC" name="order" value="DESC">
				<label for="DESC"><?php esc_html_e( 'Newest first', 'jeremy' ); ?></label>
			</div>
			<div class="flex">
				<input onchange="this.form.submit();" type="radio" <?php checked( $current_order, 'ASC' ); ?> id="ASC" name="order" value="ASC">
				<label for="ASC"><?php esc_html_e( 'Oldest first', 'jeremy' ); ?></label>
			</div>
			<noscript> 
				<input id="submit" type="submit" name="form-submit" class="submit" value="<?php esc_attr_e( 'Submit', 'jeremy' ); ?>" /> 
			</noscript> 
		</form>

		<form action="" method="post" id="notifications-bulk-management">
			<table class="notifications">
				<thead>
					<tr>
						<th class="bulk-select-all">
							<input id="select-all-notifications" type="checkbox"><label class="screen-reader-text" for="select-all-notifications"><?php esc_html_e( 'Select all', 'jeremy' ); ?></label>
						</th>
						<th class="title">
							<?php esc_html_e( 'Message', 'jeremy' ); ?>
						</th>
						<th class="date">
							<?php esc_html_e( 'Received', 'jeremy' ); ?>
						</th>
						<th class="actions screen-reader-text">
							<?php esc_html_e( 'Actions', 'jeremy' ); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php while ( bp_the_notifications() ) : bp_the_notification(); ?>
						<tr>
							<td class="bulk-select-check">
								<input id="<?php bp_the_notification_id(); ?>" type="checkbox" name="notifications[]" value="<?php bp_the_notification_id(); ?>" class="notification-check"><label class="screen-reader-text " for="<?php bp_the_notification_id(); ?>"><?php esc_html_e( 'Select this notification', 'buddypress' );?></label>
							</td>
							<td class="notification-description">
								<?php bp_the_notification_description(); ?>
							</td>
							<td class="notification-since">
								<?php bp_the_notification_time_since(); ?>
							</td>
							<td class="notification-actions">
								<?php bp_the_notification_action_links(); ?>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		
			<div class="notifications-options-nav">
				<?php bp_notifications_bulk_management_dropdown(); ?>
			</div><!-- .notifications-options-nav -->
		
			<?php wp_nonce_field( 'notifications_bulk_nonce', 'notifications_bulk_nonce' ); ?>
		</form>

		<div id="pag-bottom" class="pagination no-ajax">
			<div class="pag-count" id="notifications-count-bottom">
				<?php bp_notifications_pagination_count(); ?>
			</div>

			<div class="pagination-links" id="notifications-pag-bottom">
				<?php bp_notifications_pagination_links(); ?>
			</div>
		</div>

	<?php } else { ?>
		<div id="message" class="info">
			<p><?php echo bp_is_my_profile() ? esc_html( $inbox_title_none ) : esc_html( $inbox_title_none_admin ); ?></p>
		</div>
	<?php } ?>

<?php } else { // endif "read/unread" ?>
	<?php bp_get_template_part( 'members/single/plugins' ); ?>
<?php }