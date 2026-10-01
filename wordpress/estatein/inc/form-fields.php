<?php
/**
 * Form field markup helpers (same markup as the static build).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Text-like input with its label.
 *
 * @param string $id          Element id.
 * @param string $name        Field name.
 * @param string $label       Label.
 * @param string $placeholder Placeholder.
 * @param array  $opts        type, autocomplete, required.
 */
function estatein_input( $id, $name, $label, $placeholder, $opts = array() ) {
	$opts = wp_parse_args(
		$opts,
		array(
			'type'         => 'text',
			'autocomplete' => '',
			'required'     => false,
		)
	);
	?>
	<div class="field">
		<label class="field__label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<input class="field__control" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $opts['type'] ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>"<?php echo $opts['autocomplete'] ? ' autocomplete="' . esc_attr( $opts['autocomplete'] ) . '"' : ''; ?><?php echo $opts['required'] ? ' required' : ''; ?>>
	</div>
	<?php
}

/**
 * Select styled like the design (placeholder option + chevron icon).
 *
 * @param string $id          Element id.
 * @param string $name        Field name.
 * @param string $label       Label.
 * @param string $placeholder First (empty) option.
 * @param array  $options     value => label, or a list of labels.
 */
function estatein_select( $id, $name, $label, $placeholder, $options ) {
	?>
	<div class="field">
		<label class="field__label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<div class="field__select">
			<select class="field__control" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>">
				<option value="" selected><?php echo esc_html( $placeholder ); ?></option>
				<?php foreach ( $options as $value => $text ) : ?>
					<option value="<?php echo esc_attr( is_int( $value ) ? $text : $value ); ?>"><?php echo esc_html( $text ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php echo estatein_icon( 'chevron-down' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
	<?php
}

/**
 * Message textarea.
 *
 * @param string $id       Element id.
 * @param bool   $required Required.
 */
function estatein_message( $id, $required = false ) {
	?>
	<div class="field">
		<label class="field__label" for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Message', 'estatein' ); ?></label>
		<textarea class="field__control field__control--area" id="<?php echo esc_attr( $id ); ?>" name="message" placeholder="<?php esc_attr_e( 'Enter your Message here..', 'estatein' ); ?>" rows="5"<?php echo $required ? ' required' : ''; ?>></textarea>
	</div>
	<?php
}

/**
 * Name / email / phone fields shared by the forms.
 *
 * @param string $prefix Id prefix.
 */
function estatein_person_fields( $prefix ) {
	estatein_input( "{$prefix}-first-name", 'first_name', __( 'First Name', 'estatein' ), __( 'Enter First Name', 'estatein' ), array( 'autocomplete' => 'given-name', 'required' => true ) );
	estatein_input( "{$prefix}-last-name", 'last_name', __( 'Last Name', 'estatein' ), __( 'Enter Last Name', 'estatein' ), array( 'autocomplete' => 'family-name', 'required' => true ) );
	estatein_input( "{$prefix}-email", 'email', __( 'Email', 'estatein' ), __( 'Enter your Email', 'estatein' ), array( 'type' => 'email', 'autocomplete' => 'email', 'required' => true ) );
	estatein_input( "{$prefix}-phone", 'phone', __( 'Phone', 'estatein' ), __( 'Enter Phone Number', 'estatein' ), array( 'type' => 'tel', 'autocomplete' => 'tel' ) );
}

/**
 * Names of the terms of a property taxonomy (for select options).
 *
 * @param string $taxonomy Taxonomy.
 * @return string[]
 */
function estatein_term_names( $taxonomy ) {
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
	return is_wp_error( $terms ) ? array() : wp_list_pluck( $terms, 'name' );
}

/**
 * Budget / price ranges used by the forms and the search filters.
 *
 * @return array range => label
 */
function estatein_price_ranges() {
	return apply_filters(
		'estatein_price_ranges',
		array(
			'0-500000'       => __( 'Up to $500,000', 'estatein' ),
			'500000-1000000' => __( '$500,000 – $1,000,000', 'estatein' ),
			'1000000-'       => __( '$1,000,000+', 'estatein' ),
		)
	);
}
