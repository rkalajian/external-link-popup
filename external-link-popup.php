<?php
/**
 * Plugin Name: External Link Popup
 * Plugin URI:  https://github.com/robkalajian/external-link-popup
 * Description: Displays a confirmation modal when visitors click external links. Fully customizable from Settings > External Link Manager.
 * Version:     2.1
 * Author:      Rob Kalajian
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: external-link-popup
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'ELP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ELP_VERSION', '2.1' );

// Register settings with sanitization
function elp_register_settings() {
    register_setting( 'elp_settings_group', 'elp_excluded_domains', [
        'sanitize_callback' => 'elp_sanitize_domains',
    ] );
    register_setting( 'elp_settings_group', 'elp_popup_heading', [
        'sanitize_callback' => 'sanitize_text_field',
        'default' => '',
    ] );
    register_setting( 'elp_settings_group', 'elp_popup_message', [
        'sanitize_callback' => 'wp_kses_post',
        'default' => '',
    ] );
    register_setting( 'elp_settings_group', 'elp_popup_footer', [
        'sanitize_callback' => 'sanitize_text_field',
        'default' => '',
    ] );
    register_setting( 'elp_settings_group', 'elp_popup_logo', [
        'sanitize_callback' => 'esc_url_raw',
        'default' => '',
    ] );
    register_setting( 'elp_settings_group', 'elp_color_primary', [
        'sanitize_callback' => 'sanitize_hex_color',
        'default' => '#1a5276',
    ] );
    register_setting( 'elp_settings_group', 'elp_color_primary_hover', [
        'sanitize_callback' => 'sanitize_hex_color',
        'default' => '#154360',
    ] );
    register_setting( 'elp_settings_group', 'elp_color_heading', [
        'sanitize_callback' => 'sanitize_hex_color',
        'default' => '#000000',
    ] );
}

function elp_sanitize_domains( $input ) {
    $lines = explode( "\n", $input );
    $clean = array_map( function( $line ) {
        return sanitize_text_field( trim( $line ) );
    }, $lines );
    return implode( "\n", array_filter( $clean ) );
}

/**
 * Validate a hex color for safe output in a CSS context.
 * Returns the color if valid, otherwise the fallback.
 */
function elp_esc_css_color( $color, $fallback = '#000000' ) {
    return preg_match( '/^#[0-9a-fA-F]{3}(?:[0-9a-fA-F]{3})?$/', $color ) ? $color : $fallback;
}
add_action( 'admin_init', 'elp_register_settings' );

// Admin menu
function elp_create_menu() {
    add_options_page(
        'External Link Popup Settings',
        'External Link Manager',
        'manage_options',
        'external-link-popup',
        'elp_settings_page'
    );
}
add_action( 'admin_menu', 'elp_create_menu' );

// Admin settings page
function elp_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'external-link-popup' ) );
    }

    $domains  = esc_textarea( get_option( 'elp_excluded_domains', '' ) );
    $heading  = esc_attr( get_option( 'elp_popup_heading', '' ) );
    $message  = wp_kses_post( get_option( 'elp_popup_message', '' ) );
    $footer   = esc_attr( get_option( 'elp_popup_footer', '' ) );
    $logo_url       = esc_url( get_option( 'elp_popup_logo', '' ) );
    $color_primary  = esc_attr( get_option( 'elp_color_primary', '#1a5276' ) );
    $color_hover    = esc_attr( get_option( 'elp_color_primary_hover', '#154360' ) );
    $color_heading  = esc_attr( get_option( 'elp_color_heading', '#000000' ) );
    $domain_count = count( array_filter( array_map( 'trim', explode( "\n", get_option( 'elp_excluded_domains', '' ) ) ) ) );
    ?>
    <div class="wrap elp-admin">
        <div class="elp-admin-header">
            <h1><span class="dashicons dashicons-external" style="font-size:28px;margin-right:8px;vertical-align:middle;"></span>External Link Popup Settings</h1>
            <p class="elp-admin-desc">Configure which external links trigger a confirmation popup and customize the message shown to visitors.</p>
        </div>

        <form method="post" action="options.php">
            <?php settings_fields( 'elp_settings_group' ); ?>

            <div class="elp-admin-card">
                <h2><span class="dashicons dashicons-admin-site-alt3"></span> Excluded Domains</h2>
                <p class="description">Links to these domains will <strong>not</strong> trigger the popup. Your site domain is automatically excluded. Enter one domain per line (e.g. <code>example.com</code>).</p>
                <textarea name="elp_excluded_domains" id="elp_excluded_domains" rows="10" class="large-text code" placeholder="example.com&#10;trusted-partner.org"><?php echo $domains; ?></textarea>
                <p class="elp-domain-count"><?php echo esc_html( $domain_count ); ?> domain(s) excluded</p>
            </div>

            <div class="elp-admin-card">
                <h2><span class="dashicons dashicons-format-image"></span> Logo</h2>
                <p class="description">Upload a logo to display in the popup. If empty, the site name will be shown instead.</p>
                <div class="elp-logo-field">
                    <input type="text" name="elp_popup_logo" id="elp_popup_logo" class="large-text" value="<?php echo $logo_url; ?>" placeholder="https://...">
                    <button type="button" class="button elp-upload-btn" id="elp_upload_logo">Choose Image</button>
                </div>
                <div class="elp-logo-preview" id="elp_logo_preview"<?php echo $logo_url ? '' : ' style="display:none"'; ?>>
                    <img src="<?php echo $logo_url; ?>" alt="Logo preview" style="max-width:220px;max-height:80px;margin-top:12px;">
                    <button type="button" class="button-link elp-remove-logo" id="elp_remove_logo" style="color:#b32d2e;margin-left:10px;">Remove</button>
                </div>
            </div>

            <div class="elp-admin-card">
                <h2><span class="dashicons dashicons-art"></span> Colors</h2>
                <p class="description">Customize the popup button and heading colors.</p>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="elp_color_primary">Button Color</label></th>
                        <td><input type="text" name="elp_color_primary" id="elp_color_primary" class="elp-color-field" value="<?php echo $color_primary; ?>" data-default-color="#1a5276"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="elp_color_primary_hover">Button Hover</label></th>
                        <td><input type="text" name="elp_color_primary_hover" id="elp_color_primary_hover" class="elp-color-field" value="<?php echo $color_hover; ?>" data-default-color="#154360"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="elp_color_heading">Heading Color</label></th>
                        <td><input type="text" name="elp_color_heading" id="elp_color_heading" class="elp-color-field" value="<?php echo $color_heading; ?>" data-default-color="#000000"></td>
                    </tr>
                </table>
            </div>

            <div class="elp-admin-card">
                <h2><span class="dashicons dashicons-edit"></span> Popup Content</h2>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="elp_popup_heading">Heading</label></th>
                        <td><input type="text" name="elp_popup_heading" id="elp_popup_heading" class="large-text" value="<?php echo $heading; ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="elp_popup_message">Message</label></th>
                        <td>
                            <textarea name="elp_popup_message" id="elp_popup_message" rows="5" class="large-text"><?php echo esc_textarea( $message ); ?></textarea>
                            <p class="description">HTML allowed. Use <code>&lt;strong&gt;</code> for bold text.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="elp_popup_footer">Footer Text</label></th>
                        <td><input type="text" name="elp_popup_footer" id="elp_popup_footer" class="large-text" value="<?php echo $footer; ?>"></td>
                    </tr>
                </table>
            </div>

            <?php submit_button( 'Save Settings' ); ?>
        </form>
    </div>

    <style>
        .elp-admin-header {
            background: linear-gradient(135deg, #1d2327 0%, #2c3338 100%);
            color: #fff;
            padding: 24px 28px;
            border-radius: 8px;
            margin: 16px 0 24px;
        }
        .elp-admin-header h1 { color: #fff; margin: 0 0 6px; font-size: 24px; }
        .elp-admin-desc { color: rgba(255,255,255,.75); margin: 0; font-size: 14px; }
        .elp-admin .notice { color: #1d2327; }
        .elp-admin-card {
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 8px;
            padding: 24px 28px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,.04);
        }
        .elp-admin-card h2 {
            font-size: 16px;
            margin: 0 0 10px;
            padding: 0 0 10px;
            border-bottom: 1px solid #f0f0f1;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .elp-admin-card h2 .dashicons { color: #2271b1; }
        .elp-admin-card .form-table th { padding-left: 0; width: 140px; }
        .elp-domain-count { color: #787c82; font-style: italic; margin-top: 6px; }
        .elp-admin textarea.code { font-family: Consolas, Monaco, monospace; font-size: 13px; }
        .elp-logo-field { display: flex; gap: 8px; align-items: center; }
        .elp-logo-field .large-text { flex: 1; }
        .elp-admin-card .wp-picker-container { display: inline-block; }
    </style>
    <script>
    jQuery(function($){
        $('.elp-color-field').wpColorPicker();
        var frame;
        $('#elp_upload_logo').on('click',function(e){
            e.preventDefault();
            if(frame){frame.open();return;}
            frame=wp.media({title:'Choose Logo',button:{text:'Use this image'},multiple:false});
            frame.on('select',function(){
                var url=frame.state().get('selection').first().toJSON().url;
                $('#elp_popup_logo').val(url);
                $('#elp_logo_preview').show().find('img').attr('src',url);
            });
            frame.open();
        });
        $('#elp_remove_logo').on('click',function(e){
            e.preventDefault();
            $('#elp_popup_logo').val('');
            $('#elp_logo_preview').hide();
        });
    });
    </script>
    <?php
}

// Enqueue media uploader and color picker on admin settings page
function elp_admin_enqueue( $hook ) {
    if ( $hook !== 'settings_page_external-link-popup' ) return;
    wp_enqueue_media();
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
}
add_action( 'admin_enqueue_scripts', 'elp_admin_enqueue' );

// Enqueue frontend assets
function elp_enqueue_scripts() {
    if ( is_admin() ) return;

    wp_enqueue_style( 'elp-style', ELP_PLUGIN_URL . 'elp-style.css', [], ELP_VERSION );
    wp_enqueue_script( 'elp-script', ELP_PLUGIN_URL . 'elp-script.js', [], ELP_VERSION, true );

    $excluded_domains = get_option( 'elp_excluded_domains', '' );
    $domains_array = array_filter( array_map( 'trim', explode( "\n", $excluded_domains ) ) );

    wp_localize_script( 'elp-script', 'elpData', [
        'excludedDomains' => array_values( $domains_array ),
        'siteDomain'      => wp_parse_url( home_url(), PHP_URL_HOST ),
    ] );
}
add_action( 'wp_enqueue_scripts', 'elp_enqueue_scripts' );

// Modal HTML in footer
function elp_add_modal_html() {
    $heading = esc_html( get_option( 'elp_popup_heading', '' ) );
    $message = wp_kses_post( get_option( 'elp_popup_message', '' ) );
    $footer   = esc_html( get_option( 'elp_popup_footer', '' ) );
    $logo_url = esc_url( get_option( 'elp_popup_logo', '' ) );
    $color_primary = elp_esc_css_color( get_option( 'elp_color_primary', '#1a5276' ), '#1a5276' );
    $color_hover   = elp_esc_css_color( get_option( 'elp_color_primary_hover', '#154360' ), '#154360' );
    $color_heading = elp_esc_css_color( get_option( 'elp_color_heading', '#000000' ), '#000000' );
    ?>
    <style>
        .elp-modal { --elp-primary: <?php echo $color_primary; ?>; --elp-primary-hover: <?php echo $color_hover; ?>; --elp-heading: <?php echo $color_heading; ?>; }
    </style>
    <div class="elp-overlay" id="elpOverlay" aria-hidden="true"></div>
    <div class="elp-modal" id="exitPopup" role="dialog" aria-labelledby="elpHeading" aria-modal="true" aria-hidden="true">
        <div class="elp-modal-inner">
            <button class="elp-close" id="elpClose" aria-label="Close">&times;</button>
            <div class="elp-modal-body">
                <?php if ( $logo_url ) : ?>
                    <img class="elp-logo" src="<?php echo $logo_url; ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="220" height="48" loading="lazy">
                <?php else : ?>
                    <p class="elp-site-name"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
                <?php endif; ?>
                <h2 class="elp-heading" id="elpHeading"><?php echo $heading; ?></h2>
                <p class="elp-message"><?php echo $message; ?></p>
                <div class="elp-actions">
                    <a class="elp-btn elp-btn-primary" id="exitPopupContinue" href="#">Continue</a>
                    <button class="elp-btn elp-btn-secondary" id="elpReturn" type="button">Return</button>
                </div>
                <?php if ( $footer ) : ?>
                    <p class="elp-footer"><?php echo $footer; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}
add_action( 'wp_footer', 'elp_add_modal_html' );

// Add settings link on Plugins page
function elp_plugin_action_links( $links ) {
    $settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=external-link-popup' ) ) . '">Settings</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'elp_plugin_action_links' );
