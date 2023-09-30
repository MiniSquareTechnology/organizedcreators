<?php
/**
 * A helper class for recaptcha.
 *
 * @package fusion-builder
 * @since 3.10
 */

if ( ! class_exists( 'AWB_Recaptcha_Helper' ) ) {
	/**
	 * Shortcode class.
	 *
	 * @since 3.10
	 */
	class AWB_Recaptcha_Helper {

		/**
		 * Class instance.
		 *
		 * @static
		 * @access private
		 * @var object
		 * @since 3.10.0
		 */
		private static $instance;
		/**
		 * Constructor.
		 *
		 * @access public
		 * @since 3.10
		 */
		public function __construct() {

		}

		/**
		 * Render recaptcha field html.
		 *
		 * @access public
		 * @param array $args params.
		 * @since 3.10
		 * @return void
		 */
		public static function render_field( $args = [] ) {
			$fusion_settings = awb_get_fusion_settings();
			$defaults        = [
				'color_theme'    => $fusion_settings->get( 'recaptcha_color_scheme' ),
				'badge_position' => $fusion_settings->get( 'recaptcha_badge_position' ),
				'tab_index'      => '',
				'counter'        => 1,
				'element'        => 'form',
				'wrapper_class'  => 'form-creator-recaptcha',
			];
			$args            = wp_parse_args( $args, $defaults );
			?>
			<?php if ( $fusion_settings->get( 'recaptcha_public' ) && $fusion_settings->get( 'recaptcha_private' ) ) : ?>
				<?php if ( 'v2' === $fusion_settings->get( 'recaptcha_version' ) ) : ?>
					<div class="<?php echo esc_attr( $args['wrapper_class'] ); ?>">
						<div
							id="g-recaptcha-id-<?php echo esc_attr( $args['element'] . '-' . $args['counter'] ); ?>"
							class="awb-recaptcha-v2 fusion-<?php echo esc_attr( $args['element'] ); ?>-recaptcha-v2"
							data-theme="<?php echo esc_attr( $args['color_theme'] ); ?>"
							data-sitekey="<?php echo esc_attr( $fusion_settings->get( 'recaptcha_public' ) ); ?>"
							data-tabindex="<?php echo esc_attr( $args['tab_index'] ); ?>">
						</div>
					</div>
				<?php else : ?>
					<?php $hide_badge_class = 'hide' === $args['badge_position'] ? ' fusion-form-hide-recaptcha-badge' : ''; ?>
					<?php if ( 'hide' !== $args['badge_position'] ) { ?>
						<div class="<?php echo esc_attr( $args['wrapper_class'] ); ?>">
					<?php } ?>
							<div
								id="g-recaptcha-id-<?php echo esc_attr( $args['element'] . '-' . $args['counter'] ); ?>"
								class="fusion-<?php echo esc_attr( $args['element'] ); ?>-recaptcha-v3 recaptcha-container <?php echo esc_attr( $hide_badge_class ); ?>"
								data-sitekey="<?php echo esc_attr( $fusion_settings->get( 'recaptcha_public' ) ); ?>"
								data-badge="<?php echo esc_attr( $args['badge_position'] ); ?>">
							</div>
							<input
								type="hidden"
								name="fusion-<?php echo esc_attr( $args['element'] ); ?>-recaptcha-response"
								class="g-recaptcha-response"
								id="fusion-<?php echo esc_attr( $args['element'] ); ?>-recaptcha-response-<?php echo esc_attr( $args['counter'] ); ?>"
								value="">
					<?php if ( 'hide' !== $args['badge_position'] ) { ?>
						</div>
					<?php } ?>
				<?php endif; ?>
			<?php elseif ( is_user_logged_in() && current_user_can( 'manage_options' ) ) : ?>
					<div class="fusion-builder-placeholder"><?php echo esc_html__( 'reCAPTCHA configuration error. Please check the Global Options settings and your reCAPTCHA account settings.', 'fusion-builder' ); ?></div>
			<?php endif; ?>
			<?php
		}

		/**
		 * Verify recaptcha.
		 *
		 * @static
		 * @access public
		 * @since 3.10
		 * @return array
		 */
		public static function verify() {
			$fusion_settings = awb_get_fusion_settings();
			$response        = [
				'has_error' => false,
				'message'   => '',
			];

			// For old PHP versions.
			if ( version_compare( PHP_VERSION, '5.3' ) >= 0 && ! class_exists( 'ReCaptcha' ) ) {
				require_once FUSION_LIBRARY_PATH . '/inc/recaptcha/src/autoload.php';
				// We use a wrapper class to avoid fatal errors due to syntax differences on PHP 5.2.
				require_once FUSION_LIBRARY_PATH . '/inc/recaptcha/class-fusion-recaptcha.php';
			}

			// Instantiate recaptcha.
			$re_captcha_wrapper = new Fusion_ReCaptcha( $fusion_settings->get( 'recaptcha_private' ) );
			$re_captcha         = $re_captcha_wrapper->recaptcha;
			if ( $re_captcha && isset( $_POST['g-recaptcha-response'] ) && ! empty( $_POST['g-recaptcha-response'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput, WordPress.Security.NonceVerification
				$re_captcha_response = null;
				// Was there a reCAPTCHA response.
				$post_recaptcha_response = ( isset( $_POST['g-recaptcha-response'] ) ) ? trim( wp_unslash( $_POST['g-recaptcha-response'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput, WordPress.Security.NonceVerification

				$server_remote_addr = ( isset( $_SERVER['REMOTE_ADDR'] ) ) ? trim( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput, WordPress.Security.NonceVerification

				if ( 'v2' === $fusion_settings->get( 'recaptcha_version' ) ) {
					$re_captcha_response = $re_captcha->verify( $post_recaptcha_response, $server_remote_addr );
				} else {
					$site_url            = get_option( 'siteurl' );
					$url_parts           = wp_parse_url( $site_url );
					$site_url            = isset( $url_parts['host'] ) ? $url_parts['host'] : $site_url;
					$re_captcha_response = $re_captcha->setExpectedHostname( apply_filters( 'avada_recaptcha_hostname', $site_url ) )->setExpectedAction( 'contact_form' )->setScoreThreshold( $fusion_settings->get( 'recaptcha_score' ) )->verify( $post_recaptcha_response, $server_remote_addr );
				}
				// Check the reCAPTCHA response.
				if ( null === $re_captcha_response || ! $re_captcha_response->isSuccess() ) {
					$response    = [
						'has_error' => true,
						'message'   => __( 'Sorry, ReCaptcha could not verify that you are a human. Please try again.', 'fusion-builder' ),
					];
					$error_codes = [];
					if ( null !== $re_captcha_response ) {
						$error_codes = $re_captcha_response->getErrorCodes();
					}
					if ( empty( $error_codes ) || in_array( 'score-threshold-not-met', $error_codes, true ) ) {
						$response = [
							'has_error' => true,
							'message'   => __( 'Sorry, ReCaptcha could not verify that you are a human. Please try again.', 'fusion-builder' ),
						];
					}
				}
			} else {
				$response = [
					'has_error' => true,
					'message'   => __( 'Sorry, ReCaptcha could not verify that you are a human. Please try again.', 'fusion-builder' ),
				];
			}
			return $response;
		}

		/**
		 * Sets the necessary scripts.
		 *
		 * @access public
		 * @since 3.10
		 */
		public static function enqueue_scripts() {

			// Add reCAPTCHA script.
			$fusion_settings = awb_get_fusion_settings();

			if ( $fusion_settings->get( 'recaptcha_public' ) && $fusion_settings->get( 'recaptcha_private' ) && ! function_exists( 'recaptcha_get_html' ) && ! class_exists( 'ReCaptcha' ) ) {
				$recaptcha_script_uri = 'https://www.google.com/recaptcha/api.js?render=explicit&hl=' . get_locale() . '&onload=fusionOnloadCallback';
				if ( 'v2' === $fusion_settings->get( 'recaptcha_version' ) ) {
					$recaptcha_script_uri = 'https://www.google.com/recaptcha/api.js?hl=' . get_locale();
				}
				wp_enqueue_script( 'recaptcha-api', $recaptcha_script_uri, [], FUSION_BUILDER_VERSION, false );

				// Inline JS to render reCaptcha.
				add_action( 'wp_footer', [ self::get_instance(), 'recaptcha_callback' ], 99 );
			}
		}

		/**
		 * Generate reCaptcha callback
		 *
		 * @access public
		 * @since 3.10
		 */
		public static function recaptcha_callback() {
			$fusion_settings = awb_get_fusion_settings();
			?>
			<script type='text/javascript'>
				<?php if ( 'v2' === $fusion_settings->get( 'recaptcha_version' ) ) { ?>
				jQuery( window ).on( 'load', function() {
					var reCaptchaID;
					jQuery.each( jQuery( '.awb-recaptcha-v2' ), function( index, reCaptcha ) { // eslint-disable-line no-unused-vars
						reCaptchaID = jQuery( this ).attr( 'id' );
						grecaptcha.render( reCaptchaID, {
							sitekey: jQuery( this ).data( 'sitekey' ),
							type: jQuery( this ).data( 'type' ),
							theme: jQuery( this ).data( 'theme' )
						} );
					} );
				});
			<?php } else { ?>
				var active_captcha = [];

				var fusionOnloadCallback = function () {
					grecaptcha.ready( function () {
						jQuery( '.g-recaptcha-response' ).each( function () {
							var $el        = jQuery( this ),
								$container = $el.parent().find( 'div.recaptcha-container' ),
								id         = $container.attr( 'id' ),
								renderId;

							if ( 0 === $container.length || 'undefined' !== typeof active_captcha[ id ] || ( 1 === jQuery( '.fusion-modal' ).find( $container ).length && $container.closest( '.fusion-modal' ).is( ':hidden' ) ) ) {
								return;
							}

							renderId = grecaptcha.render(
								id,
								{
									sitekey: $container.data( 'sitekey' ),
									badge: $container.data( 'badge' ),
									size: 'invisible'
								}
							);

							active_captcha[ id ] = renderId;

							grecaptcha.execute( renderId, { action: 'contact_form' } ).then( function ( token ) {
								$el.val( token );
							});
						});
					});
				};
				<?php } ?>
			</script>
			<?php
		}

		/**
		 * Class instance.
		 *
		 * @access public
		 * @since 3.10
		 * @return object
		 */
		public static function get_instance() {
			if ( empty( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

	}
}
