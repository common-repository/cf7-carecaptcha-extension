<?php

/*
Plugin Name: Contact Form 7 - CareCAPTCHA Extension
Description: Provides a customized captcha validation field. Requires Contact Form 7
Version: 1.0
Author: NOAH - Menschen fuer Tiere e.V.
Author URI: https://www.noah.de/
License: GPL2
*/

/*  Copyright 2018  NOAH - Menschen fuer Tiere e.V. https://www.noah.de/

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


add_action( 'plugins_loaded', 'wpcf7cc_init' , 20 );
function wpcf7cc_init(){
	add_action( 'wpcf7_init', 'wpcf7cc_add_shortcode_carecaptcha' );
	add_filter( 'wpcf7_validate_carecaptcha*', 'wpcf7cc_validation_filter', 10, 2 );
}

add_action('wp_enqueue_scripts','wpcf7cc_scripts');
function wpcf7cc_scripts() {
    wp_enqueue_style( 'styles', plugins_url( 'includes/css/style.css', __FILE__ ));
    wp_enqueue_script( 'scripts', plugins_url( 'includes/js/scripts.js', __FILE__ ));
    wp_localize_script('scripts', 'script_vars', array(
			'_introText' => __('Select all images below that match this one:', 'cf7-carecaptcha-extension'),
			'_errorText' => __('Please try again.', 'cf7-carecaptcha-extension'),
			'_verifyText' => __('OK', 'cf7-carecaptcha-extension'),
			'_linkText' => __('Download Plugin', 'cf7-carecaptcha-extension'),
			'_setText1' => __('Be human and support the end of animal cruelty.', 'cf7-carecaptcha-extension'),
			'_setText2' => __('Be human and support the end of animal testing.', 'cf7-carecaptcha-extension'),
			'_setText3' => __('Be human and support the end of factory farming.', 'cf7-carecaptcha-extension'),
			'_setText4' => __('Be human and support the end of animal testing.', 'cf7-carecaptcha-extension'),
			'_setText5' => __('Be human and support the end of factory farming.', 'cf7-carecaptcha-extension')
		)
	);
}

function wpcf7cc_add_shortcode_carecaptcha() {
	wpcf7_add_form_tag(
		array( 'carecaptcha' , 'carecaptcha*' ),
		'wpcf7cc_shortcode_handler', true );
}

function wpcf7cc_shortcode_handler( $tag ) {
	$tag = new WPCF7_FormTag( $tag );

	if ( empty( $tag->name ) )
		return '';

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type, 'wpcf7cc' );


	if ( $validation_error )
		$class .= ' wpcf7-not-valid';

	$atts = array();

	$atts['size'] = $tag->get_size_option( '40' );
	$atts['maxlength'] = $tag->get_maxlength_option();
	$atts['minlength'] = $tag->get_minlength_option();

	if ( $atts['maxlength'] && $atts['minlength'] && $atts['maxlength'] < $atts['minlength'] ) {
		unset( $atts['maxlength'], $atts['minlength'] );
	}

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

	if ( $tag->has_option( 'readonly' ) )
		$atts['readonly'] = 'readonly';

	if ( $tag->is_required() )
		$atts['aria-required'] = 'true';

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$atts['type'] = 'hidden';

	$atts['name'] = $tag->name;

	$atts = wpcf7_format_atts( $atts );

    $dir = plugin_dir_url( __FILE__ );

	$html = sprintf(
		'<p></p>
        <span class="wpcf7-form-control-wrap cc-toggle-wrapper %1$s" data-carecaptcha>
            <input class="cc-toggle-input" %2$s />%3$s
            <div class="cc-toggle">
                <div class="cc-toggle-checkbox">&nbsp;</div>
                <div class="cc-toggle-label">%4$s</div>
                <div class="cc-toggle-logo">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
                        <path fill="#284395" d="M42.2,7.5c0,0,7.4-0.1,10.5,0.2C67.9,9.1,75,19.1,85.1,29.1l0.6,0.6c0.4,0.4,0.9,0.8,1.4,1.2
                            c2.2,1.9,5.1,5.9,5.4,6.2c0.3,0.4,0.6,0.8,0.9,1.2c2.1,4.2,2.4,5.1,2.4,5.1c1,2.3,0,3.1,0,3.1c-1,0.6-1.7-1.4-2.5-2.7
                            c-0.3-0.6-0.8-1.2-1.2-1.7c-0.7-0.8-1.4-1.5-2.3-2.1h0l1.5,2.1c1.5,2.2,2.9,4.6,4.1,7c0.2,0.5,0.4,0.8,0.4,1
                            c0.8,2.1,0.2,2.7,0.1,2.8c0,0,0,0,0,0c-0.3,0.1-0.7,0.1-1,0c-0.2-0.1-1.4-1.7-2.5-3.2c-1.2-1.8-2.6-3.4-4-5v0
                            c-0.6-0.7-1.4-1.1-1.4-1.1l0.1,0.4l2.3,2.8c1.3,1.5,2.4,3.2,3.5,4.8l2.1,3.3c0.5,0.7,0.6,1.7,0.1,2.4c-0.1,0.1-0.1,0.1-0.3,0.2
                            c-0.5,0.3-1.1,0.2-1.8-0.2c-0.6-0.3-1.2-0.8-1.6-1.4c-1.8-2.5-3.7-5-6-7.1l-1-0.9c-1.5-1.9-1.8-1.4-1.8-1.4l5.8,7.6
                            c1.4,2,2,3.3,2.1,4.2c0.2,1.2-1.1,2.4-2.1,1.8c-0.7-0.4-1.6-1.2-2.3-2c-0.9-0.9-1.8-2-2.5-3.1c-3.8-5.5-7.2-7.4-7.2-7.4
                            c-2-1.6-3.5-0.2-3.5-0.2l-1.4,2.1c-1.5,2.1-2,3.5-2.5,6c-0.2,0.9-0.9,2-1.3,2.3c-0.5,0.4-1,0.7-1.4,0.3c-0.2-0.2-0.4-0.5-0.6-0.9
                            c-0.3-0.6,0.8-6.7,0.9-7.3c0.3-1,0.5-1.9,0.7-2.5c0.2-0.9,0.2-1.9,0-2.8c-1.9-7.3-1.7-10.6-1.4-11.7c0.1-0.4,0.1-0.8-0.1-1.2l0,0
                            C61.1,25.9,52,26.9,43.7,28c-1.2,0.2-2.4,0.4-3.6,0.8L42.2,7.5z"
                        />
                        <path fill="#517BBE" d="M7.5,59.3C1.6,34.1,20.1,22.6,31.3,15.1l0.7-0.6c0.5-0.4,0.9-0.8,1.3-1.3c2.1-2.1,6.2-4.7,6.6-5
                            c0.4-0.3,0.8-0.6,1.3-0.8c4.4-1.9,5.3-2.1,5.3-2.1c2.4-0.9,3.1,0.2,3.1,0.2c0.6,1.1-1.5,1.7-2.9,2.3C46,8.2,45.4,8.6,44.9,9
                            c-0.8,0.6-1.6,1.3-2.2,2.1v0l2.2-1.3c2.3-1.4,4.7-2.6,7.3-3.6c0.5-0.2,0.8-0.3,1-0.4c2.1-0.6,2.7,0,2.8,0.1c0,0,0,0,0,0
                            C56,6.3,56,6.6,55.8,7c-0.1,0.2-1.8,1.3-3.4,2.3c-1.8,1.1-3.6,2.4-5.3,3.7l0,0c-0.7,0.6-1.2,1.3-1.2,1.3l0.4,0l3-2.1
                            c1.6-1.2,3.3-2.2,5.1-3.2L57.9,7c0.8-0.4,1.7-0.5,2.4,0c0.1,0.1,0.1,0.1,0.2,0.3c0.2,0.5,0.2,1.1-0.3,1.8c-0.4,0.6-0.9,1.1-1.5,1.5
                            c-2.6,1.6-5.2,3.4-7.4,5.5L50.4,17c-2,1.4-1.5,1.7-1.5,1.7l7.9-5.3c2-1.3,3.4-1.8,4.3-1.9c1.2-0.1,2.4,1.2,1.6,2.2
                            c-0.5,0.7-1.3,1.5-2.1,2.2c-1,0.9-2.1,1.7-3.3,2.4c-5.7,3.5-7.8,6.7-7.8,6.7c-1.8,1.9,0.7,2.3,0.7,2.3l1.9-0.1
                            c4-0.1,6.5,0.5,6.8,0.6c0.3,0.1,3.5,1,3.9,1.5c0.2,0.9-1,1.8-1.8,1.9c-0.6,0.1-1.3,0-1.9-0.2c-2.3-0.7-11.2,1.9-11.2,1.9
                            c-8.4,1.8-12.4,1.9-13.6,1.6c-0.4-0.1-0.8-0.1-1.2,0l0,0c-5.3,5.7-7.2,12.5-6.7,20.9L7.5,59.3z"
                        />
                        <path fill="#A2A1A1" d="M57.5,89.7c-19.7,3.9-37.9-9.4-45.7-21.6l-0.5-0.8c-0.3-0.5-0.7-0.9-1-1.4c-1.9-2.3-4-6.8-4.2-7.2
                            c-0.2-0.4-0.5-0.9-0.6-1.3C4.1,52.9,4,51.9,4,51.9c-0.6-2.5,0.6-3.1,0.6-3.1C5.7,48.4,6,50.6,6.5,52c0.2,0.8,0.6,1.4,1,2
                            c0.5,0.8,1,1.5,1.6,2.2l0.1,0.1l-1.1-2.4c-1.1-2.4-2-4.9-2.7-7.4c-0.1-0.5-0.3-1-0.3-1.1c-0.4-2.2,0.3-2.7,0.4-2.8c0,0,0,0,0,0
                            c0.3-0.1,0.7,0,1,0.2c0.2,0.1,1.1,2,1.9,3.7c0.9,1.9,1.9,3.8,3,5.6l0,0.1c0.5,0.8,1.2,1.3,1.2,1.3l0-0.4l-1.8-3.3
                            C10,48,9.2,46.2,8.4,44.4l-1.7-4.3c-0.1-0.2-0.1-0.5,0-0.7c0.6-1.3,0.4-1.2,0.9-1.3c0.7-0.2,1.4,0.1,2.3,1c0.2,0.3,0.4,0.6,0.6,0.9
                            c1.3,2.9,2.8,5.7,4.7,8.3l0.8,1c1.2,2.2,1.5,1.7,1.5,1.7l-4.3-8.5c-1-2.2-1.3-3.6-1.3-4.5c0-1.2,1.5-2.2,2.4-1.4
                            c0.6,0.6,1.4,1.6,2,2.5c0.7,1,1.3,2.1,1.8,3.3c2.8,6.2,5.8,8.7,5.8,8.7c1.6,2,2.4-0.4,2.4-0.4l0.1-1.9c0.4-4,1.3-6.4,1.4-6.7
                            c0.1-0.3,1.4-3.3,2-3.7c0.9-0.1,1.6,1.2,1.7,2c0,0.7-0.2,1.3-0.4,1.9c-1,2.1,0.5,11.3,0.5,11.3c0.8,9,0.2,12.9-0.1,13.9
                            c-0.1,0.2,0,0.3,0,0.5l0,0c0.2,0.3,0.5,0.6,0.8,0.9c1.7,1.2,7.5,4.5,19.6,2.8L57.5,89.7z"
                        />
                    </svg>
                    <span>CareCAPTCHA</span>
                </div>
            </div>
        </span>',
		sanitize_html_class($tag->name),
        $atts,
        $validation_error,
        __('I’m not a robot', 'cf7-carecaptcha-extension')
    );

	return $html;
}

function wpcf7cc_validation_filter( $result, $tag ) {
	$tag = new WPCF7_FormTag( $tag );

	$name = $tag->name;

	$value = isset( $_POST[$name] )
		? trim( wp_unslash( strtr( (string) $_POST[$name], "\n", " " ) ) )
		: '';

	if ( 'carecaptcha' == $tag->basetype ) {
		if ( $tag->is_required() && '' == $value ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}
	}

	if ( ! empty( $value ) ) {
		$maxlength = $tag->get_maxlength_option();
		$minlength = $tag->get_minlength_option();

		if ( $maxlength && $minlength && $maxlength < $minlength ) {
			$maxlength = $minlength = null;
		}

		$code_units = wpcf7_count_code_units( $value );

		if ( false !== $code_units ) {
			if ( $maxlength && $maxlength < $code_units ) {
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_too_long' ) );
			} elseif ( $minlength && $code_units < $minlength ) {
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_too_short' ) );
			}
		}
	}

	return $result;
}

if ( is_admin() ) {
	add_action( 'wpcf7_admin_init' , 'wpcf7cc_add_tag_generator_carecaptcha' , 100 );
}

function wpcf7cc_add_tag_generator_carecaptcha() {

	if ( ! class_exists( 'WPCF7_TagGenerator' ) ) return;

	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'carecaptcha', __( 'CareCAPTCHA', 'contact-form-7' ),
		'wpcf7cc_tag_generator_carecaptcha' );
}

function wpcf7cc_tag_generator_carecaptcha( $contact_form , $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = $args['id'];

	$description;

	$description = __( "Insert the CareCAPTCHA validation field.", 'contact-form-7' ); ?>

    <div class="control-box">
        <fieldset>
            <legend><?php echo $description; ?></legend>
            <table class="form-table">
                <tbody>
                	<tr>
                    	<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
                    	<td>
                    		<fieldset>
                        		<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
                        		<input class="disabled" type="checkbox" name="required" value="on" checked onclick="return false" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?>
                    		</fieldset>
                    	</td>
                	</tr>
                    <tr>
                    	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
                    	<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
                	</tr>
                	<tr>
                    	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
                    	<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
                	</tr>
                	<tr>
                    	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
                    	<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
                	</tr>
                </tbody>
            </table>
        </fieldset>
    </div>

    <div class="insert-box">
    	<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />
    	<div class="submitbox">
    	    <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
    	</div>
    	<br class="clear" />
    </div>

<?php }