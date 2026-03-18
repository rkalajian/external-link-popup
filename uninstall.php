<?php
/**
 * Uninstall handler for External Link Popup.
 * Removes all plugin options from the database.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

$options = [
    'elp_excluded_domains',
    'elp_popup_heading',
    'elp_popup_message',
    'elp_popup_footer',
    'elp_popup_logo',
    'elp_color_primary',
    'elp_color_primary_hover',
    'elp_color_heading',
];

foreach ( $options as $option ) {
    delete_option( $option );
}
