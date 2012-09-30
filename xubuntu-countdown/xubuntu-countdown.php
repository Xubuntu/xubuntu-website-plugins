<?php
/*
 *  Plugin Name: Xubuntu Countdown
 *  Description: Manage Xubuntu countdown banners
 *  Author: Pasi Lallinaho
 *  Version: quantal-1
 *  Author URI: http://open.knome.fi/
 *  Plugin URI: https://xubuntu.org/
 *
 */

/*
 *  Hook our options page to admin menu
 *
 */

add_action( 'admin_menu', 'XubuntuCountdownMenu' );

function XubuntuCountdownMenu( ) {
	add_menu_page( 'Manage countdown banners', 'Countdown', 'manage_options', 'countdown', 'XubuntuCountdownPage' );
}

/*
 *  Show our options page
 *
 */

function XubuntuCountdownPage( ) {
	if( !empty( $_POST ) && check_admin_referer( 'update_countdown', 'countdown_nonce' ) ) {
		/* Update options */
		$releasename = preg_replace( '/[^a-z]/', '', strtolower( $_POST['countdown_releasename'] ) );
		update_option( 'countdown_releasename', $releasename );

		$releasedate = preg_replace( '/[^0-9]/', '', $_POST['countdown_releasedate'] );
		$releasestamp = mktime( 0, 0, 0, intval( substr( $releasedate, 4, 2 ) ), intval( substr( $releasedate, 6, 2 ) ), substr( $releasedate, 0, 4 ) );
		update_option( 'countdown_releasedate', $releasedate );

		if( isset( $_POST['countdown_releaseswitch'] ) ) {
			$releaseswitch = 1;
		} else {
			$releaseswitch = 0;
		}

		update_option( 'countdown_releaseswitch', $releaseswitch );

		/* Write changes to file */
		$upload = wp_upload_dir( );
		$data = $releasename . "\n" . $releasedate . "\n" . $releasestamp . "\n" . $releaseswitch;

		file_put_contents( $upload['basedir'] . "/xubuntu_countdown.txt", $data );
	}

	?>
	<div class="wrap">
		<h2>Manage countdown banners</h2>
		<form method="post" action="admin.php?page=countdown">
			<table class="form-table">
				<tr>
					<th scope="row"><label for="releasename">Release name</label></th>
					<td>
						<input name="countdown_releasename" type="text" id="countdown_releasename" value="<?php echo get_option( 'countdown_releasename' ); ?>" class="regular-text" />
						<span class="description">This name defines the subdirectory where to look for countdown banners.</span>
					</td>	
				</tr>
				<tr>
					<th scope="row"><label for="releasedate">Release date</label></th>
					<td>
						<input name="countdown_releasedate" type="text" id="countdown_releasedate" value="<?php echo get_option( 'countdown_releasedate' ); ?>" class="regular-text" />
						<span class="description">Enter the release date in YYYYMMDD format.</span>
					</td>
				</tr>
				<tr>
					<th scope="row">Release switch</th>
					<td>
						<label for="countdown_switch">
							<input name="countdown_releaseswitch" id="countdown_releaseswitch" type="checkbox" <?php checked( get_option( 'countdown_releaseswitch' ) ); ?>" />
							<b>It's out!</b>
						</label>
					</td>
				</tr>
			</table>
			<?php wp_nonce_field( 'update_countdown', 'countdown_nonce' ); ?>
			<p class="submit"><input name="submit" id="submit" class="button-primary" value="Save Changes" type="submit" /></p>
		</form>
	</div>
	<?php
}

?>
