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
          // SudoWP Security: Allow JavaScript config but escape to prevent XSS
          // This is JavaScript object notation, not HTML
          echo esc_js( $this->option( 'sliderOwlConfig' ) ); 
          ?>
      });
    </script>
</figure>