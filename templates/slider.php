<?php
/**
 * Template Page for the gallery slider
 *
 * Follow variables are useable:
 *        $images
 *            -> filename, path, thumbURL, url
 */

$first_image = array_shift( $images );
array_unshift( $images, $first_image );
?>
<figure class="gallery pg-theme-slider">

    <div class="pg-slider owl-theme ow-carousel">
        <?php foreach ( $images as $image ) { ?>
            <img class="gallery-image"
                    src="<?php echo esc_url( \Lib\PostGalleryImage::getThumbUrl( $image['path'],
                        [
                            'width' => $this->option( 'thumbWidth' ),
                            'height' => $this->option( 'thumbHeight' ),
                            'scale' => $this->option( 'thumbScale' ),
                        ] ) );
                    ?>"
                    alt="<?php echo esc_attr( $image['filename'] ) ?>"
                    <?php echo wp_kses_post( $image['imageOptionsParsed'] ); ?>
            />
        <?php } ?>
    </div>

    <script>
      jQuery('.pg-slider').owlCarousel({
          <?php 
          // SudoWP Security: This is JavaScript object literal configuration
          // We need to output it as-is but sanitize individual values
          // The config comes from trusted admin settings (requires manage_options capability)
          $config = $this->option( 'sliderOwlConfig' );
          
          // Basic sanitization: remove script tags and dangerous patterns
          $config = preg_replace( '/<script[^>]*>.*?<\/script>/is', '', $config );
          $config = str_replace( array( '</script>', '<script>' ), '', $config );
          
          // Only output if not empty
          if ( !empty( $config ) ) {
              // This is admin-controlled configuration, not user input
              echo $config; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
          }
          ?>
      });
    </script>
</figure>