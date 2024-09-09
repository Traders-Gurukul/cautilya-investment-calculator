<?php
/*
Plugin Name: Cautilya Investment Calculator
Plugin URI: https://cautilyawealth.com/
Description: An investment calculator for SIP, Lumpsum, SWP, and Goal-based investments.
Version: 1.0
Author: Arun B Ayyar
License: Apache 2.0
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function cic_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array('jquery'), '3.7.1', true);
    wp_enqueue_style('cic-styles', plugin_dir_url(__FILE__) . 'calculator-styles.css');
    wp_enqueue_script('cic-script', plugin_dir_url(__FILE__) . 'calculator-script.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'cic_enqueue_scripts');

function cic_calculator_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'calculator-template.php';
    return ob_get_clean();
}
add_shortcode('cautilya_investment_calculator', 'cic_calculator_shortcode');