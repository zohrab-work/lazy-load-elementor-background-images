<?php
/**
 * Plugin Name: Elementor Add-on Lazy Load Background images
 * Description: Powerful Elementor add-on to lazy load background images
 * Author: Zohrab Mazmanyan
 * Version: 1.0.0
 *
 * Text Domain: lazy-load-elementor-background-images
 */

add_action('plugins_loaded', 'LazyLoadElementorBackgroundImages::init');

class LazyLoadElementorBackgroundImages
{
    public static function init()
    {
        new static();
    }

    public function __construct()
    {
        add_action( 'elementor/element/before_section_end', [$this, 'add_settings'], 10, 3 );
        add_action( 'elementor/frontend/before_render', [$this, 'frontend_render'], 20, 2 );
        add_action( 'wp_head', [$this, 'header_scripts'] );
        add_action( 'wp_footer', [$this, 'footer_scripts'] );
    }

    public function header_scripts()
    {
        ?>
        <style>
            .lazy-background {background-image: none !important}
        </style>
        <?php
    }

    public function footer_scripts()
    {
        ?>
        <script>
            const lazyBgClass = 'lazy-background';window.addEventListener("load",function(){if("IntersectionObserver"in window){var e=document.querySelectorAll(`.${lazyBgClass}`);var g=new IntersectionObserver(function(b,c){b.forEach(function(d){d.isIntersecting&&(d=d.target,d.classList.remove(lazyBgClass),g.unobserve(d))})});e.forEach(function(b){g.observe(b)})} else {var a=function(){f&&clearTimeout(f);f=setTimeout(function(){var b=window.pageYOffset;e.forEach(function(c){c.offsetTop<window.innerHeight+b&&(c.src=c.dataset.src,c.classList.remove(lazyBgClass))});0==e.length&&(document.removeEventListener("scroll",a),window.removeEventListener("resize",a),window.removeEventListener("orientationChange",a))},20)},f;e=document.querySelectorAll(`.${lazyBgClass}`);document.addEventListener("scroll",a);window.addEventListener("resize",a);window.addEventListener("orientationChange",a)}});
        </script>
        <?php
    }

    public function add_settings($element, $section_id, $args)
    {
        if($section_id === 'section_background') {
            $element->add_control(
                'lazy_load_background_image',
                [
                    'label' => 'Lazy Load Background Image',
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                ]
            );
        }
    }

    public function frontend_render($element)
    {
        $settings = $element->get_settings();
        $is_lazy_enabled = ( $settings[ 'lazy_load_background_image' ] ?? '') === 'yes';
        if ( $is_lazy_enabled ) {
            $element->add_render_attribute( '_wrapper', 'class', 'lazy-background');
            return $element;
        }
        return $element;
    }
}
