<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MSC_External_Links_Module {
    /** @var MSC_External_Links */
    private $plugin;

    public function __construct( $plugin ) {
        $this->plugin = $plugin;

        add_filter( 'the_content', array( $this, 'filter_content' ), 20 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    public function enqueue_assets() {
        if ( ! $this->is_enabled() || ! (int) $this->plugin->get_option( 'icon', 1 ) ) {
            return;
        }

        wp_enqueue_style(
            'mscel-external-links',
            MSCEL_PLUGIN_URL . 'assets/css/external-links.css',
            array(),
            MSCEL_PLUGIN_VERSION
        );
    }

    public function filter_content( $content ) {
        if ( ! $this->is_enabled() || empty( $content ) || ! is_singular() ) {
            return $content;
        }

        $post = get_post();
        if ( ! $post ) {
            return $content;
        }

        $allowed = (array) $this->plugin->get_option( 'post_types', array( 'post', 'page' ) );
        if ( ! in_array( $post->post_type, $allowed, true ) ) {
            return $content;
        }

        if ( false === stripos( $content, '<a ' ) ) {
            return $content;
        }

        libxml_use_internal_errors( true );
        $dom = new DOMDocument();
        $dom->loadHTML( '<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
        libxml_clear_errors();

        $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
        $exclude   = $this->excluded_domains();

        foreach ( $dom->getElementsByTagName( 'a' ) as $link ) {
            $href = trim( (string) $link->getAttribute( 'href' ) );
            if ( '' === $href || 0 === strpos( $href, '#' ) || 0 === strpos( $href, '/' ) ) {
                continue;
            }

            $host = wp_parse_url( $href, PHP_URL_HOST );
            if ( ! $host ) {
                continue;
            }

            $is_external = $this->is_external_host( $host, (string) $site_host, $exclude );
            $is_external = (bool) apply_filters( 'mscel_is_external_link', $is_external, $href, $host, $site_host );

            if ( ! $is_external ) {
                continue;
            }

            if ( (int) $this->plugin->get_option( 'new_tab', 1 ) ) {
                $link->setAttribute( 'target', '_blank' );
            }

            $existing_rel = trim( (string) $link->getAttribute( 'rel' ) );
            $parts        = preg_split( '/\s+/', strtolower( $existing_rel ) );
            $parts        = is_array( $parts ) ? $parts : array();
            $parts[]      = 'noopener';
            $parts[]      = 'noreferrer';
            $parts        = array_unique( array_filter( $parts ) );
            $link->setAttribute( 'rel', implode( ' ', $parts ) );

            $classes   = trim( (string) $link->getAttribute( 'class' ) );
            $class_set = preg_split( '/\s+/', $classes );
            $class_set = is_array( $class_set ) ? $class_set : array();
            $class_set[] = 'mscel-external-link';

            if ( (int) $this->plugin->get_option( 'icon', 1 ) ) {
                $class_set[] = 'mscel-has-icon';
            }

            $class_set = array_unique( array_filter( $class_set ) );
            $link->setAttribute( 'class', implode( ' ', $class_set ) );
        }

        return (string) $dom->saveHTML();
    }

    private function is_enabled() {
        return (bool) $this->plugin->get_option( 'module_enabled', 1 );
    }

    private function excluded_domains() {
        $raw = (string) $this->plugin->get_option( 'excluded', '' );
        if ( '' === trim( $raw ) ) {
            return array();
        }

        $lines = preg_split( '/\R+/', $raw );
        $lines = is_array( $lines ) ? $lines : array();

        return array_values(
            array_filter(
                array_map(
                    static function ( $domain ) {
                        $domain = strtolower( trim( (string) $domain ) );
                        $domain = preg_replace( '#^https?://#', '', $domain );
                        return trim( (string) $domain, '/' );
                    },
                    $lines
                )
            )
        );
    }

    private function is_external_host( $host, $site_host, $exclude ) {
        $host      = strtolower( (string) $host );
        $site_host = strtolower( (string) $site_host );

        if ( '' === $host || '' === $site_host ) {
            return false;
        }

        if ( $host === $site_host || $this->ends_with( $host, '.' . $site_host ) ) {
            return false;
        }

        foreach ( $exclude as $domain ) {
            if ( $host === $domain || $this->ends_with( $host, '.' . $domain ) ) {
                return false;
            }
        }

        return true;
    }

    private function ends_with( $haystack, $needle ) {
        $needle_len = strlen( (string) $needle );
        if ( 0 === $needle_len ) {
            return true;
        }
        return substr( (string) $haystack, -$needle_len ) === (string) $needle;
    }
}
