<?php
/**
 * Plugin Name:     Tiny Default Thumbnail
 * Plugin URI:      https://vincentdubroeucq.com
 * Description:     Allows you to add a default thumbnail to posts and pages
 * Author:          Vincent Dubroeucq
 * Author URI:      https://vincentdubroeucq.com
 * Text Domain:     tiny-default-thumbnail
 * Domain Path:     /languages
 * Version:         0.2
 *
 * @package         Tiny_Default_Thumbnail
 */
defined( 'ABSPATH' ) || die();


add_action( 'plugins_loaded', 'tiny_default_thumbnail_load_textdomain' );
/**
 * Load translations
 */
function tiny_default_thumbnail_load_textdomain() {
    load_plugin_textdomain( 'tiny-default-thumbnail', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}


add_filter( 'post_thumbnail_html', 'tiny_default_thumbnail_html', 10, 5 );
/**
 * Adds default fallback for post thumbnails
 * 
 * @param   string  $html          The <img> tag HTML.
 * @param   int     $post_id       The post ID.
 * @param   int     $thumbnail id  The thumbnail ID.
 * @param   string  $size          The thumbnail size.
 * @param   array   $attributes    Array of atributes.
 * @return  string  $html          Filtered HTML.
 *   
 */
function tiny_default_thumbnail_html( $html, $post_id, $thumbnail_id, $size, $attr ){
    $post_type = get_post_type( $post_id );
    if( empty( $html ) && ! $thumbnail_id && in_array( $post_type, array( 'post', 'page' ) ) ){
        $default_id = get_option( "tiny_default_${post_type}_thumbnail" );
        $html       = wp_get_attachment_image( $default_id, $size, false, $attr );
    }
    return $html;
}


add_filter( 'has_post_thumbnail', 'tiny_default_thumbnail_has_post_thumbnail', 10, 3 );
/**
 * Filters whether the post or page has a thumbnail
 * 
 * @param bool             $has_thumbnail  true if the post has a post thumbnail, otherwise false.
 * @param int|WP_Post|null $post           Post ID or WP_Post object. Default is global `$post`.
 * @param int|false        $thumbnail_id   Post thumbnail ID or false if the post does not exist.
 */

function tiny_default_thumbnail_has_post_thumbnail( $has_thumbnail, $post, $thumbnail_id ){
    $post_type = get_post_type( $post );
    if( ! $thumbnail_id && in_array( $post_type, array( 'post', 'page' ) ) ){
        return (bool) get_option( "tiny_default_${post_type}_thumbnail" );
    }
    return $has_thumbnail;
}


add_action( 'customize_register', 'tiny_default_thumbnail_customize_register' );
/**
 * Adds Customizer Settings
 */
function tiny_default_thumbnail_customize_register( $wp_customize ) {
    $wp_customize->add_section( 'thumbnail', array(
        'title' => __( 'Thumbnail settings', 'tiny-default-thumbnail' ),
    ) );
    $wp_customize->add_setting( 'tiny_default_post_thumbnail', array(
        'type'              => 'option',
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'tiny_default_post_thumbnail', array(
        'label'     => __( 'Default post thumbnail', 'tiny-default-thumbnail' ),
        'section'   => 'thumbnail',
        'mime_type' => 'image',
    ) ) );
    $wp_customize->add_setting( 'tiny_default_page_thumbnail', array(
        'type'              => 'option',
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'tiny_default_page_thumbnail', array(
        'label'     => __( 'Default page thumbnail', 'tiny-default-thumbnail' ),
        'section'   => 'thumbnail',
        'mime_type' => 'image',
    ) ) );
}
