<?php
/**
 * Short desc
 *
 * Long desc
 *
 * @package     WPPR
 * @subpackage  WPPR_Editor
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

/**
 * The default editor class.
 *
 * Class WPPR_Default_Editor.
 */
class WPPR_Default_Editor extends WPPR_Editor_Abstract {
	/**
	 * Save the editor data.
	 */
	public function save() {
		$data = $_POST;

		do_action( 'wppr_before_save', $this->post, $data );
		$status = isset( $data['wppr-review-status'] ) ? strval( $data['wppr-review-status'] ) : 'no';

		$review = $this->review;
		if ( $status === 'yes' ) {

			$review->activate();
			$name  = isset( $data['wppr-editor-product-name'] ) ? sanitize_text_field( $data['wppr-editor-product-name'] ) : '';
			$image = isset( $data['wppr-editor-image'] ) ? sanitize_text_field( $data['wppr-editor-image'] ) : '';
			$click = isset( $data['wppr-editor-link'] ) ? strval( sanitize_text_field( $data['wppr-editor-link'] ) ) : 'image';

			//TODO Setup links as array.
			$link           = isset( $data['wppr-editor-button-text'] ) ? strval( sanitize_text_field( $data['wppr-editor-button-text'] ) ) : '';
			$text           = isset( $data['wppr-editor-button-link'] ) ? strval( sanitize_text_field( $data['wppr-editor-button-link'] ) ) : '';
			$link2          = isset( $data['wppr-editor-button-text-2'] ) ? strval( sanitize_text_field( $data['wppr-editor-button-text-2'] ) ) : '';
			$text2          = isset( $data['wppr-editor-button-link-2'] ) ? strval( sanitize_text_field( $data['wppr-editor-button-link-2'] ) ) : '';
			$price          = isset( $data['wppr-editor-price'] ) ? sanitize_text_field( $data['wppr-editor-price'] ) : 0;
			$options_names  = isset( $data['wppr-editor-options-name'] ) ? $data['wppr-editor-options-name'] : array();
			$options_values = isset( $data['wppr-editor-options-value'] ) ? $data['wppr-editor-options-value'] : array();
			$options_values = isset( $data['wppr-editor-options-value'] ) ? $data['wppr-editor-options-value'] : array();
			$pros           = isset( $data['wppr-editor-pros'] ) ? $data['wppr-editor-pros'] : array();
			$cons           = isset( $data['wppr-editor-cons'] ) ? $data['wppr-editor-cons'] : array();
			$options        = array();
			foreach ( $options_names as $k => $op_name ) {
				if ( isset( $options_values[ $k ] ) && ! empty( $op_name ) && ! empty( $options_values[ $k ] ) ) {
					$options[] = array(
						'name'  => sanitize_text_field( $op_name ),
						'value' => sanitize_text_field( $options_values[ $k ] ),
					);

				}
			}
			if ( is_array( $pros ) ) {
				$pros = array_map( 'sanitize_text_field', $pros );
			} else {
				$pros = array();
			}
			if ( is_array( $cons ) ) {
				$cons = array_map( 'sanitize_text_field', $cons );
			} else {
				$cons = array();
			}
			$review->set_name( $name );
			$review->set_image( $image );
			if ( $click === 'image' || $click === 'link' ) {
				$review->set_click( $click );

			}
			$links = array();
			if ( ! empty ( $link ) && ! empty ( $text ) ) {
				$links[ $link ] = $text;
			}
			if ( ! empty ( $link2 ) && ! empty ( $text2 ) ) {
				$links[ $link2 ] = $text2;
			}
			$review->set_links( $links );
			$review->set_price( $price );
			$review->set_pros( $pros );
			$review->set_cons( $cons );
			$review->set_options( $options );
			wppr_set_option( 'last_review', $review->get_ID() );
		} else {
			$review->deactivate();
		}
		do_action( 'wppr_after_save', $this->post, $data );
	}

	/**
	 * Render the editors data.
	 */
	public function render() {
		$review = $this->review;
		$check  = $review->is_active() ? 'yes' : 'no';
		?>
        <p class="wppr-active wppr-<?php echo $check; ?>">
            <label for="wppr-review-yes"><?php _e( 'Is this a review post ?', 'wp-product-review' ); ?> </label>
			<?php
			echo wppr_fields()->radio( array(
				'name'    => 'wppr-review-status',
				'id'      => 'wppr-review-yes',
				'class'   => 'wppr-review-status',
				'value'   => 'yes',
				'current' => $check
			) );
			?>
            <label for="wppr-review-no"><?php _e( 'Yes', 'wp-product-review' ); ?></label>
			<?php
			echo wppr_fields()->radio( array(
				'name'    => 'wppr-review-status',
				'id'      => 'wppr-review-no',
				'class'   => 'wppr-review-status',
				'value'   => 'no',
				'current' => $check
			) );
			?>
            <label for="wppr-review-no"><?php _e( 'No', 'wp-product-review' ); ?></label>

        </p>
        <div class="wppr-review-editor " id="wppr-meta-<?php echo $check; ?>">

			<?php do_action( 'wppr_editor_before', $this->post ); ?>
            <div class="wppr-review-details wppr-review-section">
                <h4><?php _e( 'Product Details', 'wp-product-review' ); ?></h4>
                <p><?php _e( 'Specify the general details for the reviewed product.', 'wp-product-review' ); ?></p>
				<?php do_action( 'wppr_editor_details_before', $this->post ); ?>
                <div class="wppr-review-details-fields wppr-review-fieldset">
                    <ul>
                        <li>
                            <label for="wppr-editor-product-name"><?php _e( 'Product Name', 'wp-product-review' ); ?></label>
							<?php
							echo wppr_fields()->text( array(
								'name'        => 'wppr-editor-product-name',
								'value'       => $review->get_name(),
								'placeholder' => __( 'Product name', 'wp-product-review' ),
							) );
							?>
                        </li>
                        <li>
                            <label for="wppr-editor-product-image"><?php _e( 'Product Image', 'wp-product-review' ); ?></label>
							<?php
							echo wppr_fields()->image( array(
								'name'   => 'wppr-editor-image',
								'value'  => $review->get_image(),
								'action' => __( 'Choose or Upload an Image', 'wp-product-review' )
							) )
							?>
                            <small>
                                *<?php _e( 'If no image is provided, featured image is used', 'wp-product-review' ); ?></small>
                        </li>
                        <li>
							<span><?php _e( 'Product Image Click', 'wp-product-review' ); ?>
                                : </span>
							<?php
							echo wppr_fields()->radio( array(
								'name'    => 'wppr-editor-link',
								'id'      => 'wppr-editor-link-show',
								'class'   => 'wppr-editor-link',
								'value'   => 'image',
								'current' => $this->get_value( 'wppr-editor-link' )
							) );
							?>
                            <label for="wppr-editor-link-show"><?php _e( 'Show Whole Image', 'wp-product-review' ); ?></label>
							<?php
							echo wppr_fields()->radio( array(
								'name'    => 'wppr-editor-link',
								'id'      => 'wppr-editor-link-open',
								'class'   => 'wppr-editor-link',
								'value'   => 'link',
								'current' => $this->get_value( 'wppr-editor-link' )
							) );
							?>
                            <label for="wppr-editor-link-open"><?php _e( 'Open Affiliate link', 'wp-product-review' ); ?> </label>

                        </li>
						<?php
						$links = $review->get_links();
						?>
                        <li>
                            <label for="wppr-editor-button-text"><?php _e( 'Affiliate Button Text', 'wp-product-review' ); ?> </label>
							<?php
							echo wppr_fields()->text( array(
								'name'        => 'wppr-editor-button-text',
								'value'       => $this->get_value( 'wppr-editor-button-text' ),
								'placeholder' => __( 'Affiliate Button Text', 'wp-product-review' ),
							) );
							?>
                        </li>
                        <li>
                            <label for="wppr-editor-button-link"><?php _e( 'Affiliate Link', 'wp-product-review' ); ?> </label>
							<?php
							echo wppr_fields()->text( array(
								'name'        => 'wppr-editor-button-link',
								'value'       => $this->get_value( 'wppr-editor-button-link' ),
								'placeholder' => __( 'Affiliate Link', 'wp-product-review' ),
							) );
							?>
							<?php if ( count( $links ) < 2 ) : ?>
                                <a id="wppr-editor-new-link"
                                   title="<?php _e( 'Add new link', 'wp-product-review' ); ?>">+
                                </a>
							<?php endif; ?>
                        </li>
						<?php
						if ( ! empty ( $links ) ) {
							if ( count( $links ) > 1 ) {
								$i = 1;
								foreach ( $links as $url => $text ) {
									if ( $i > 1 ) {
										?>
                                        <li>
                                            <label for="wppr-editor-button-text-<?php $i; ?>"><?php echo __( 'Affiliate Button Text', 'wp-product-review' ) . ' ' . $i; ?> </label>
											<?php
											echo wppr_fields()->text( array(
												'name'        => 'wppr-editor-button-text-' . $i,
												'value'       => $text,
												'placeholder' => __( 'Affiliate Button Text', 'wp-product-review' ) . ' ' . $i,
											) );
											?>
                                        </li>
                                        <li>
                                            <label for="wppr-editor-button-link-<?php $i; ?>"><?php echo __( 'Affiliate Link', 'wp-product-review' ) . ' ' . $i; ?> </label>
											<?php
											echo wppr_fields()->text( array(
												'name'        => 'wppr-editor-button-link-' . $i,
												'value'       => $url,
												'placeholder' => __( 'Affiliate Link', 'wp-product-review' ) . ' ' . $i,
											) );
											?>
                                        </li>
										<?php
									}
									$i ++;
								}
							}
						}
						?>

                        <li>
                            <label for="wppr-editor-price"><?php _e( 'Product Price', 'wp-product-review' ); ?> </label>
							<?php
							echo wppr_fields()->text( array(
								'name'        => 'wppr-editor-price',
								'value'       => $review->get_price(),
								'placeholder' => __( 'Product Price', 'wp-product-review' ),
							) );
							?>
                        </li>

                    </ul>
                </div>
				<?php do_action( 'wppr_editor_details_after', $this->post ); ?>
            </div><!-- end .review-settings-notice -->
            <div class="wppr-review-options  wppr-review-section">
                <h4><?php _e( 'Product Options', 'wp-product-review' ); ?></h4>
                <p><?php _e( 'Insert your options and their grades. Grading must be done from 0 to 100.', 'wp-product-review' ); ?></p>

				<?php do_action( 'wppr_editor_options_before', $this->post ); ?>
                <div class="wppr-review-fieldset wppr-review-options-fields">
                    <ul class="wppr-review-options-list clear">
						<?php
						$options_nr = wppr_get_option( 'cwppos_option_nr' );
						for ( $i = 1; $i <= $options_nr; $i ++ ) {
							?>
                            <li>
                                <label for="wppr-editor-options-text-<?php echo $i; ?>"><?php echo $i; ?></label>
								<?php
								echo wppr_fields()->text( array(
									'name'        => 'wppr-editor-options-name[]',
									'id'          => 'wppr-editor-options-name-' . $i,
									'value'       => $this->get_value( 'wppr-option-name-' . $i ),
									'placeholder' => __( 'Option', 'wp-product-review' ) . ' ' . $i,
								) );
								echo wppr_fields()->text( array(
									'name'        => 'wppr-editor-options-value[]',
									'id'          => 'wppr-editor-options-value-' . $i,
									'class'       => 'wppr-text wppr-option-number',
									'value'       => $this->get_value( 'wppr-option-value-' . $i ),
									'placeholder' => __( 'Grade', 'wp-product-review' )
								) );
								?>
                            </li>
							<?php
						}
						?>
                    </ul>
                </div>
				<?php do_action( 'wppr_editor_options_after', $this->post ); ?>

            </div>
            <div class="wppr-review-pros  wppr-review-section">
                <h4><?php _e( 'Pro Features', 'wp-product-review' ); ?></h4>
                <p><?php _e( 'Insert product\'s pro features below.', 'wp-product-review' ); ?></p>

				<?php do_action( 'wppr_editor_pros_before', $this->post ); ?>
                <div class="wppr-review-fieldset wppr-review-pros-fields">
                    <ul class="wppr-review-options-list clear">
						<?php
						$options_nr = wppr_get_option( 'cwppos_option_nr' );
						$pros       = $review->get_pros();
						for ( $i = 1; $i <= $options_nr; $i ++ ) {
							?>
                            <li>
                                <label for="wppr-editor-pros-<?php echo $i; ?>"><?php echo $i; ?></label>
								<?php
								echo wppr_fields()->text( array(
									'name'        => 'wppr-editor-pros[]',
									'id'          => 'wppr-editor-pros-' . $i,
									'value'       => isset( $pros[ $i - 1 ] ) ? $pros[ $i - 1 ] : '',
									'placeholder' => __( 'Option', 'wp-product-review' ) . ' ' . $i,
								) );
								?>
                            </li>
							<?php
						}
						?>
                    </ul>
                </div>
				<?php do_action( 'wppr_editor_pros_after', $this->post ); ?>
            </div>
            <div class="wppr-review-cons  wppr-review-section">
                <h4><?php _e( 'Cons Features', 'wp-product-review' ); ?></h4>
                <p><?php _e( 'Insert product\'s cons features below.', 'wp-product-review' ); ?></p>
				<?php do_action( 'wppr_editor_cons_before', $this->post ); ?>
                <div class="wppr-review-fieldset wppr-review-cons-fields">
                    <ul class="wppr-review-options-list clear">
						<?php
						$options_nr = wppr_get_option( 'cwppos_option_nr' );
						$cons       = $review->get_cons();
						for ( $i = 1; $i <= $options_nr; $i ++ ) {
							?>
                            <li>
                                <label for="wppr-editor-cons-<?php echo $i; ?>"><?php echo $i; ?></label>
								<?php
								echo wppr_fields()->text( array(
									'name'        => 'wppr-editor-cons[]',
									'id'          => 'wppr-editor-cons-' . $i,
									'value'       => isset( $cons[ $i - 1 ] ) ? $cons[ $i - 1 ] : '',
									'placeholder' => __( 'Option', 'wp-product-review' ) . ' ' . $i,
								) );
								?>
                            </li>
							<?php
						}
						?>
                    </ul>
                </div>
				<?php do_action( 'wppr_editor_cons_after', $this->post ); ?>
            </div>
            <br class="clear">

			<?php do_action( 'wppr_editor_after', $this->post ); ?>
        </div>
		<?php
	}

	public function load_style() {
		wp_enqueue_style( 'wppr-default-editor-css', WPPR_URL . '/css/editor.css', array(), WPPR_LITE_VERSION );
		wp_enqueue_script( 'wppr-default-editor-js', WPPR_URL . '/javascript/editor.js', array( 'jquery' ), WPPR_LITE_VERSION, true );
	}
}
