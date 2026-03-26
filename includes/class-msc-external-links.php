<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MSC_External_Links {
    const OPTION_KEY = 'mscel_options';

    /** @var MSC_External_Links|null */
    private static $instance = null;

    /** @var array<string,mixed> */
    private $options = array();

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function default_options() {
        return array(
            'module_enabled' => 1,
            'post_types'     => array( 'post', 'page' ),
            'new_tab'        => 1,
            'icon'           => 1,
            'excluded'       => '',
        );
    }

    public static function activate() {
        $stored = get_option( self::OPTION_KEY, array() );
        if ( ! is_array( $stored ) ) {
            $stored = array();
        }
        update_option( self::OPTION_KEY, wp_parse_args( $stored, self::default_options() ) );
    }

    public static function deactivate() {}

    private function __construct() {
        $this->options = wp_parse_args( get_option( self::OPTION_KEY, array() ), self::default_options() );

        new MSC_External_Links_Settings( $this );

        if ( ! $this->is_pro_active() ) {
            new MSC_External_Links_Module( $this );
        }
    }

    public function is_pro_active() {
        return (bool) apply_filters( 'mscel_pro_active', false );
    }

    public function get_options() {
        return $this->options;
    }

    public function get_option( $key, $default = null ) {
        return array_key_exists( $key, $this->options ) ? $this->options[ $key ] : $default;
    }

    public function update_options( $new_options ) {
        $this->options = wp_parse_args( $new_options, self::default_options() );
        update_option( self::OPTION_KEY, $this->options );
    }
}
