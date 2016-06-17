<?php

namespace Gejst\ACFDateTimePolyfill;
/*
Plugin Name: ACF DateTime-picker polyfill
Plugin URI: http://gejststudio.com
Description: Polyfills the new built in ACF DateTime-picker to work with sites that have already been using the DateTime picker plugin
Author: Anders Thorborg
Version: 0.1
Author URI: http://gejststudio.com
*/

class DateTimePickerPolyfill {

  public function __construct() {
    add_filter('acf/load_value/type=date_time_picker', [$this, 'load_value'], 10, 3);
    add_filter('acf/format_value/type=date_time_picker', [$this, 'format_value'], 10, 3);
    add_filter('acf/update_value/type=date_time_picker', [$this, 'update_value'], 10, 3);
    $this->defaults = $this->add_on_get_defaults();
  }


  /*========================================
  =            Filter functions            =
  ========================================*/


  public function update_value( $value, $post_id, $field  ) {
    switch ( $this->get_field_status( $field ) ) {
      case 'add-on':
      case 'updated':
        return $this->add_on_update_value( $value, $field );

    }
    return $value;
  }

  public function load_value( $value, $post_id, $field  ) {
    switch ( $this->get_field_status( $field ) ) {
      case 'add-on':
      case 'updated':
        return date( 'Y-m-d H:i:s', $value );
    }
    return $value;
  }

  public function format_value( $value, $post_id, $field  ) {
   switch ( $this->get_field_status( $field ) ) {
      case 'add-on':
      case 'updated':
        return $this->add_on_format_value( strtotime( $value ), $field );
    }
    return $value;
  }


  /*========================================
  =            Helper functions            =
  ========================================*/


  private function get_field_status( $field ) {
    if ( ! isset( $field['date_format'] ) && isset( $field['display_format'] ) ) {
      return 'native';
    }
    if ( isset( $field['date_format'] ) && ! isset( $field['display_format'] ) ) {
      return 'add-on';
    }
    if ( isset( $field['date_format'] ) && isset( $field['display_format'] ) ) {
      return 'updated';
    }
  }


  /*========================================
  =            Add-on functions            =
  ========================================*/


  private function add_on_load_value( $value, $field ) {
    $field = array_merge($this->defaults, $field);
    if ( $value != '' && $field['save_as_timestamp'] == 'true' && $field['get_as_timestamp'] != 'true' && $this->is_valid_timestamp( $value ) ) {
      if ( $field['show_date'] == 'true') {
         $value = date_i18n(sprintf("%s %s",$this->js_to_php_dateformat($field['date_format']),$this->js_to_php_timeformat($field['time_format'])), $value);
      } else {
         $value = date_i18n(sprintf("%s",$this->js_to_php_timeformat($field['time_format'])), $value);
      }
    }
    return $value;
  }

  private function add_on_update_value( $value, $field ) {
    $field = array_merge( $this->defaults, $field );
    if ($value != '' && $field['save_as_timestamp'] == 'true') {
        if (preg_match('/^dd?\//',$field['date_format'] )) { //if start with dd/ or d/ (not supported by strtotime())
            $value = str_replace('/', '-', $value);
        }
        $value = strtotime( $value );
    }
    return $value;
  }

  private function add_on_format_value($value, $post_id, $field) {
    $field = array_merge($this->defaults, $field);
    if ( $value != '' && $field['save_as_timestamp'] == 'true' && $field['get_as_timestamp'] != 'true' && $this->is_valid_timestamp( $value ) ) {
      if ( $field['show_date'] == 'true') {
         $value = date_i18n( sprintf( "%s %s", $this->js_to_php_dateformat( $field['date_format'] ), $this->js_to_php_timeformat( $field['time_format'] ) ), $value );
      } else {
         $value = date_i18n( sprintf( "%s", $this->js_to_php_timeformat( $field['time_format'] ) ), $value );
      }
    }
    return $value;
  }

  private function is_valid_timestamp( $timestamp ) {
    return ( (string) (int) $timestamp === (string) $timestamp );
  }

  private function js_to_php_dateformat($date_format) {
    $chars = array(
        // Day
        'dd' => 'd', 'd' => 'j', 'DD' => 'l','D' => 'D', 'o' => 'z',
        // Month
        'mm' => 'm', 'm' => 'n', 'MM' => 'F', 'M' => 'M',
        // Year
        'yy' => 'Y', 'y' => 'y',
    );
    return strtr((string)$date_format, $chars);
  }

  private function js_to_php_timeformat( $time_format ) {
    $chars = array(
      //hour
      'HH' => 'H', 'H'  => 'G', 'hh' => 'h' , 'h'  => 'g',
      //minute
      'mm' => 'i', 'm'  => 'i',
      //second
      'ss' => 's', 's' => 's',
      //am/pm
      'TT' => 'A', 'T' => 'A', 'tt' => 'a', 't' => 'a'
    );
    return strtr( (string) $time_format, $chars );
  }

  private function add_on_get_defaults() {
    return array(
      'label'             => __( 'Choose Time', 'acf-field-date-time-picker' ),
      'time_format'       => 'h:mm tt',
      'show_date'         => 'true',
      'date_format'       => 'm/d/y',
      'show_week_number'  => 'false',
      'picker'            => 'slider',
      'save_as_timestamp' => 'true',
      'get_as_timestamp'  => 'false'
    );
  }
}


/*============================
=            Init            =
============================*/

function init() {
  if( has_native_date_time_picker() ) {
    new DateTimePickerPolyfill();
  }
}

add_action( 'init', __NAMESPACE__ . '\\init' );


/*==========================================
=            Disable datepicker            =
==========================================*/

function muplugins_loaded() {
  if ( ! function_exists( 'get_plugins_data' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
  }
  // Deactivate date picker plugin if native date picker is installed
  $date_time_picker_file = 'acf-field-date-time-picker/acf-date_time_picker.php';
  if ( has_native_date_time_picker() && is_plugin_active( $date_time_picker_file ) ) {
    deactivate_plugins( plugin_basename( WP_PLUGIN_DIR . '/' . $date_time_picker_file ) );
  }
}
add_action( 'muplugins_loaded', __NAMESPACE__ . '\\muplugins_loaded');

function has_native_date_time_picker () {
  if ( is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ) {
    $acf = get_plugin_data( WP_PLUGIN_DIR . '/advanced-custom-fields-pro/acf.php' );
    return $acf['Version'] >= '5.3.9';
  }
  return false;

}