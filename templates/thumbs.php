<?php
/**
 * Template Page for the thumbs
 *
 * Follow variables are useable:
 *        $images
 *            -> filename, path, thumbURL, url
 */
?>
    <figure role="group"
            class="gallery pg-theme-thumbs pg-theme-list <?php echo esc_attr( $this->option( 'containerClass' ) ); ?>">
        <?php foreach ( $images as $image ): ?>
            <?php
            $count += 1;

            if ( !empty( $appendList[$count] ) ):
                foreach ( $appendList[$count] as $appendTemplate ):
                    echo '<div class="item">';
                    echo do_shortcode( '[elementor-template id=' . intval( $appendTemplate ) . ']' );
                    echo '</div>';
                endforeach;
            endif;


            $thumbUrl = \Lib\PostGalleryImage::getThumbUrl( $image['path'],
                [
                    'width' => $this->option( 'thumbWidth' ),
                    'height' => $this->option( 'thumbHeight' ),
                    'scale' => $this->option( 'thumbScale' ),
                ] );
            ?>
            <div class="item" <?php echo wp_kses_post( $image['imageOptionsParsed'] ); ?>>
                <figure class="inner">
                    <a href="<?php echo esc_url( $image['url'] ) ?>">
                        <?php if ( $this->option( 'useSrcset' ) ): ?>
                            <img class="post-gallery_thumb"
                                    src="<?php echo esc_url( $image['url'] ) ?>"
                                    data-title="<?php echo esc_attr( $image['title'] ) ?>"
                                    data-desc="<?php echo esc_attr( $image['desc'] ) ?>"
                                    alt="<?php echo esc_attr( $image['alt'] ) ?>"
                                    srcset="<?php echo esc_attr( $image['srcset'] ); ?>"
                                    sizes="<?php echo esc_attr( $srcsetSizes ); ?>"
                            />
                        <?php else: ?>
                            <img class="post-gallery_thumb"
                                    src="<?php echo esc_url( $thumbUrl ) ?>"
                                    data-title="<?php echo esc_attr( $image['title'] ) ?>"
                                    data-desc="<?php echo esc_attr( $image['desc'] ) ?>"
                                    alt="<?php echo esc_attr( $image['alt'] ) ?>"
                                    data-scale="<?php echo esc_attr( $this->option( 'thumbScale' ) ); ?>"/>
                        <?php endif; ?>

                    </a>
                    <div class="bg-image" style="background-image: url('<?php echo esc_url( $thumbUrl ); ?>');"></div>

                    <?php if ( !empty( $this->option( 'showCaptions' ) ) ): ?>
                        <?php
                        $caption = $this->getCaption( $image );
                        if ( !empty( $caption ) ): ?>
                            <figcaption class="caption-wrapper"><?php echo wp_kses_post( $caption ); ?></figcaption>
                        <?php endif; ?>
                    <?php endif; ?>
                </figure>
            </div>
        <?php endforeach; ?>
    </figure>
<?php if ( $this->option( 'imageAnimation' ) ): ?>
    <script>
      jQuery(function () {
        window.registerPgImageAnimation('<?php echo esc_js( $id ); ?>', <?php echo intval( $this->option( 'imageAnimationTimeBetween' ) ); ?>);
      });
    </script>
<?php endif; ?>


<?php if ( $this->option( 'connectedWith' ) ): ?>
    <script>
      jQuery(function ($) {
        $('#<?php echo esc_js( $id ); ?>.postgallery-wrapper a').each(function (index, element) {
          element = $(element);
          element.addClass('no-litebox');
          element.on('click', function (e) {
            e.preventDefault();
            $('#<?php echo esc_js( $id ); ?>')[0].connectedSwiper.slideTo(element.closest('.item').index() + 1);
          });
        });


        $(window).on('load', function () {
          $('#<?php echo esc_js( $id ); ?>')[0].connectedSwiper = document.querySelector('.elementor-element-<?php echo esc_js( $this->option( 'connectedWith' ) ); ?> .elementor-main-swiper').swiper;
          $('#<?php echo esc_js( $id ); ?>')[0].connectedSwiper.on('slideChange', function () {
            setActiveSlide('<?php echo esc_js( $id ); ?>');
          });
          setActiveSlide('<?php echo esc_js( $id ); ?>');
        });
      }, jQuery);
    </script>
<?php endif; ?>