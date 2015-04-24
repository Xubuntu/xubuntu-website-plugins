<?php
/*
 *  Plugin Name: Xubuntu Team Members
 *  Description: Adds the role "Xubuntu Team member"
 *  Author: Pasi Lallinaho
 *  Version: 1.0
 *  Author URI: http://open.knome.fi/
 *  Plugin URI: http://xubuntu.org/
 *
 *  License: GNU General Public License v2 or later
 *  License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

add_role(
	'xubuntu_team_member',
	'Xubuntu Team member',
	array(
		'read' => true,
		'read_private_posts' => true,
		'read_private_pages' => true,
		'edit_posts' => true,
		'edit_others_posts' => true,
		'edit_pages' => true,
		'edit_others_pages' => true,
		'upload_files' => true
	)
);

?>
