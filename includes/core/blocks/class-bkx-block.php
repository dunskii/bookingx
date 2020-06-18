<?php defined('ABSPATH') || exit;

/**
 * Class Bkx_Block
 */
class Bkx_Block
{
    /**
     * @var null
     */
    private static $_instance = null;

    /**
     * @var string
     */
    public $plugin_name = 'booking-x';

    /**
     * @var array
     */
    public $attributes = array();

    /**
     * @var string
     */
    public $type = '';

    /**
     * @var array
     */
    public $script_handle = array();

    /**
     * @var array
     */
    public $style_handle = array();

    /**
     * Bkx_Block constructor.
     */
    public function __construct()
    {

    }

    /**
     *
     */
    public function init()
    {
        $this->register_block_type();
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_scripts'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_styles'));
        add_filter('is_bookingx', array($this, 'is_bookingx_call'));

    }

    /**
     * @return string
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function is_bookingx_call()
    {
        return true;
    }

    /**
     *
     */
    public function register_block_type()
    {

        register_block_type($this->get_type(), array(
            'category' => 'booking-x',
            'style' => $this->style_handle,
            'render_callback' => array($this, 'render_block'),
            'editor_script' => $this->script_handle,
            'attributes' => $this->attributes,
        ));

    }

    /**
     *
     */
    public function enqueue_scripts()
    {

        $scripts = $this->getScripts();

        // If no scripts are registered, return.
        if (empty($scripts)) {
            return;
        }

        // Loop through scripts.
        foreach ($scripts as $script) {

            // Prepare parameters.
            $src = isset($script['src']) ? $script['src'] : false;
            $deps = isset($script['deps']) ? $script['deps'] : array();
            $version = isset($script['version']) ? $script['version'] : false;
            $in_footer = isset($script['in_footer']) ? $script['in_footer'] : false;

            // Enqueue script.
            wp_enqueue_script($script['handle'], $src, $deps, $version, $in_footer);

        }

    }

    /**
     * @return array
     */
    public function getScripts()
    {

        return array();

    }

    /**
     *
     */
    public function enqueue_styles()
    {

        // Get registered styles.
        $styles = $this->getStyles();


        if (empty($styles)) {
            return;
        }

        // Loop through styles.
        foreach ($styles as $style) {

            // Prepare parameters.
            $src = isset($style['src']) ? $style['src'] : false;
            $deps = isset($style['deps']) ? $style['deps'] : array();
            $version = isset($style['version']) ? $style['version'] : false;
            $media = isset($style['media']) ? $style['media'] : 'all';

            // Enqueue style.
            wp_enqueue_style($style['handle'], $src, $deps, $version, $media);

        }

    }

    /**
     * @return array
     */
    public function getStyles()
    {

        return array();

    }

    /**
     * @param array $attributes
     * @return string
     */
    public function render_block($attributes = array())
    {

        return '';

    }
}