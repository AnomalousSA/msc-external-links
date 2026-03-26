<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MSC_External_Links_Settings {
    /** @var MSC_External_Links */
    private $plugin;

    public function __construct( $plugin ) {
        $this->plugin = $plugin;
        add_action( 'admin_menu', array( $this, 'register_menu' ), 5 );
        add_action( 'admin_init', array( $this, 'handle_save' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        if ( ! has_action( 'admin_menu', 'msc_site_care_reorder_priority_items' ) ) {
            add_action( 'admin_menu', 'msc_site_care_reorder_priority_items', 999 );
        }
        if ( ! has_action( 'admin_head', 'msc_site_care_menu_highlight_styles' ) ) {
            add_action( 'admin_head', 'msc_site_care_menu_highlight_styles' );
        }
    }

    public function register_menu() {
        global $admin_page_hooks;

        if ( ! isset( $admin_page_hooks['msc-site-care'] ) ) {
            add_menu_page(
                __( 'Site Care', 'msc-external-links' ),
                __( 'Site Care', 'msc-external-links' ),
                'manage_options',
                'msc-site-care',
                array( __CLASS__, 'render_landing_page' ),
                'dashicons-shield-alt',
                65
            );
        }

        if ( $this->plugin->is_pro_active() ) {
            return;
        }

        add_submenu_page(
            'msc-site-care',
            __( 'External Links', 'msc-external-links' ),
            __( 'External Links', 'msc-external-links' ),
            'manage_options',
            'mscel-settings',
            array( $this, 'render_page' )
        );

        add_filter( 'msc_upgrade_sections', array( $this, 'add_upgrade_section' ) );

        global $submenu;
        $upgrade_registered = false;
        if ( ! empty( $submenu['msc-site-care'] ) ) {
            foreach ( $submenu['msc-site-care'] as $item ) {
                if ( isset( $item[2] ) && 'msc-site-care-upgrade' === $item[2] ) {
                    $upgrade_registered = true;
                    break;
                }
            }
        }
        if ( ! $upgrade_registered ) {
            add_submenu_page(
                'msc-site-care',
                __( 'Upgrade to Pro', 'msc-external-links' ),
                __( 'Upgrade to Pro', 'msc-external-links' ),
                'manage_options',
                'msc-site-care-upgrade',
                'msc_render_combined_upgrade_page'
            );
        }
    }

    public static function render_landing_page() {
        echo '<div class="wrap msc-admin-wrap">';
        echo '<div class="msc-admin-header"><h1>' . esc_html__( 'Site Care', 'msc-external-links' ) . '</h1></div>';
        echo '<div class="msc-admin-card">';
        echo '<p>' . esc_html__( 'Welcome to Site Care by Anomalous Developers. Use the submenu items to manage each installed module.', 'msc-external-links' ) . '</p>';
        echo '</div>';
        echo '</div>';
    }

    public function enqueue_admin_styles() {
        $page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
        $allowed_pages = array( 'msc-site-care', 'mscel-settings', 'msc-site-care-upgrade' );

        if ( ! in_array( $page, $allowed_pages, true ) ) {
            return;
        }

        wp_enqueue_style(
            'mscel-admin-tokens',
            MSCEL_PLUGIN_URL . 'assets/css/admin-tokens.css',
            array(),
            MSCEL_PLUGIN_VERSION
        );

        wp_enqueue_style(
            'mscel-admin-components',
            MSCEL_PLUGIN_URL . 'assets/css/admin-components.css',
            array( 'mscel-admin-tokens' ),
            MSCEL_PLUGIN_VERSION
        );
    }

    public function add_upgrade_section( $sections ) {
        $sections[] = array(
            'title'    => __( 'External Links Pro', 'msc-external-links' ),
            'features' => __( 'Advanced icon styles, click analytics, and Gutenberg toolbar overrides.', 'msc-external-links' ),
            'url'      => 'https://anomalous.co.za',
        );
        return $sections;
    }

    public function handle_save() {
        if ( ! is_admin() || ! isset( $_POST['mscel_settings_submit'] ) ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        check_admin_referer( 'mscel_save_settings', 'mscel_nonce' );

        $defaults = MSC_External_Links::default_options();
        $incoming = isset( $_POST['mscel'] ) ? (array) wp_unslash( $_POST['mscel'] ) : array();
        $clean    = $defaults;

        $clean['module_enabled'] = isset( $incoming['module_enabled'] ) ? 1 : 0;
        $clean['post_types']     = $this->sanitize_post_types( $incoming, 'post_types' );
        $clean['new_tab']        = isset( $incoming['new_tab'] ) ? 1 : 0;
        $clean['icon']           = isset( $incoming['icon'] ) ? 1 : 0;
        $clean['excluded']       = isset( $incoming['excluded'] ) ? sanitize_textarea_field( $incoming['excluded'] ) : '';

        $this->plugin->update_options( $clean );

        wp_safe_redirect(
            add_query_arg(
                array( 'page' => 'mscel-settings', 'updated' => 1 ),
                admin_url( 'admin.php' )
            )
        );
        exit;
    }

    private function sanitize_post_types( $incoming, $key ) {
        $types = isset( $incoming[ $key ] ) ? (array) $incoming[ $key ] : array();
        $types = array_map( 'sanitize_key', $types );
        return array_values( array_filter( $types ) );
    }

    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $options    = $this->plugin->get_options();
        $post_types = get_post_types( array( 'public' => true ), 'objects' );
        ?>
        <div class="wrap msc-admin-wrap">
            <div class="msc-admin-header">
                <h1><?php esc_html_e( 'External Links', 'msc-external-links' ); ?></h1>
            </div>

            <?php if ( isset( $_GET['updated'] ) ) : ?>
                <div class="msc-admin-notice"><p><?php esc_html_e( 'Settings saved.', 'msc-external-links' ); ?></p></div>
            <?php endif; ?>

            <div class="msc-admin-card">
                <form method="post" action="">
                    <?php wp_nonce_field( 'mscel_save_settings', 'mscel_nonce' ); ?>

                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Enable module', 'msc-external-links' ); ?></th>
                            <td><label><input type="checkbox" name="mscel[module_enabled]" value="1" <?php checked( 1, (int) $options['module_enabled'] ); ?> /> <?php esc_html_e( 'Enabled', 'msc-external-links' ); ?></label></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Apply to post types', 'msc-external-links' ); ?></th>
                            <td>
                                <?php foreach ( $post_types as $pt ) : ?>
                                    <label style="display:block;"><input type="checkbox" name="mscel[post_types][]" value="<?php echo esc_attr( $pt->name ); ?>" <?php checked( in_array( $pt->name, (array) $options['post_types'], true ) ); ?> /> <?php echo esc_html( $pt->labels->singular_name ); ?></label>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Open in new tab', 'msc-external-links' ); ?></th>
                            <td><label><input type="checkbox" name="mscel[new_tab]" value="1" <?php checked( 1, (int) $options['new_tab'] ); ?> /> <?php esc_html_e( 'Enabled', 'msc-external-links' ); ?></label></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Show external link icon', 'msc-external-links' ); ?></th>
                            <td><label><input type="checkbox" name="mscel[icon]" value="1" <?php checked( 1, (int) $options['icon'] ); ?> /> <?php esc_html_e( 'Enabled', 'msc-external-links' ); ?></label></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Exclude domains', 'msc-external-links' ); ?></th>
                            <td>
                                <textarea name="mscel[excluded]" rows="6" class="large-text msc-admin-textarea" placeholder="example.com&#10;subdomain.example.org"><?php echo esc_textarea( $options['excluded'] ); ?></textarea>
                                <p class="description"><?php esc_html_e( 'One domain per line. Matching links will not be treated as external.', 'msc-external-links' ); ?></p>
                            </td>
                        </tr>
                    </table>

                    <?php submit_button( __( 'Save Settings', 'msc-external-links' ), 'primary msc-admin-button msc-admin-button-primary', 'mscel_settings_submit' ); ?>
                </form>
            </div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'msc_render_combined_upgrade_page' ) ) {
    function msc_render_combined_upgrade_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $sections = apply_filters( 'msc_upgrade_sections', array() );
        echo '<div class="wrap msc-admin-wrap">';
        echo '<div class="msc-admin-header"><h1>' . esc_html__( 'Upgrade to Pro', 'msc-external-links' ) . '</h1></div>';
        echo '<div class="msc-admin-card">';
        echo '<p>' . esc_html__( 'Upgrade individual modules to unlock more features for each plugin.', 'msc-external-links' ) . '</p>';
        echo '</div>';
        if ( empty( $sections ) ) {
            echo '<div class="msc-admin-card"><p>' . esc_html__( 'No upgrades available.', 'msc-external-links' ) . '</p></div>';
        } else {
            echo '<div class="msc-admin-grid" style="margin-top:20px;">';
            foreach ( $sections as $section ) {
                echo '<div class="msc-admin-card">';
                echo '<h2>' . esc_html( $section['title'] ) . '</h2>';
                echo '<p>' . esc_html( $section['features'] ) . '</p>';
                echo '<a href="' . esc_url( $section['url'] ) . '" target="_blank" rel="noopener noreferrer" class="button button-primary msc-admin-button msc-admin-button-primary">';
                echo esc_html__( 'Learn more', 'msc-external-links' );
                echo '</a>';
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>';
    }
}

if ( ! function_exists( 'msc_site_care_reorder_priority_items' ) ) {
    function msc_site_care_reorder_priority_items() {
        global $submenu;

        if ( empty( $submenu['msc-site-care'] ) || ! is_array( $submenu['msc-site-care'] ) ) {
            return;
        }

        $items   = $submenu['msc-site-care'];
        $regular = array();
        $support = null;
        $upgrade = null;

        foreach ( $items as $item ) {
            $slug = isset( $item[2] ) ? $item[2] : '';

            if ( 'msc-site-care-support' === $slug ) {
                $support = $item;
                continue;
            }

            if ( 'msc-site-care-upgrade' === $slug ) {
                $upgrade = $item;
                continue;
            }

            $regular[] = $item;
        }

        if ( null !== $support ) {
            $regular[] = $support;
        }
        if ( null !== $upgrade ) {
            $regular[] = $upgrade;
        }

        $submenu['msc-site-care'] = array_values( $regular );
    }
}

if ( ! function_exists( 'msc_site_care_menu_highlight_styles' ) ) {
    function msc_site_care_menu_highlight_styles() {
        ?>
        <style>
            #toplevel_page_msc-site-care .wp-submenu a[href*="page=msc-site-care-support"] {
                color: #0a5fa8;
                font-weight: 600;
            }

            #toplevel_page_msc-site-care .wp-submenu a[href*="page=msc-site-care-upgrade"] {
                color: #a04b00;
                font-weight: 700;
                background: rgba(255, 185, 0, 0.18);
                border-radius: 3px;
                padding-left: 6px;
                margin-left: -6px;
            }
        </style>
        <?php
    }
}
