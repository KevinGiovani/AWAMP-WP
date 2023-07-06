<?php
/**
* Suitbuilder: Excerpt
*
* @since Suitbuilder: 1.0.6
*/
if( ! class_exists( 'Suitbuilder_Excerpt' ) ):

class Suitbuilder_Excerpt{

    /**
    * Default length (by WordPress)
    *
    * @since Suitbuilder 1.0.6
    * @access public
    * @var int
    */
    public $length = 55;

    /**
    * Default post id
    *
    * @since Suitbuilder 1.0.6
    * @access public
    * @var int
    */
    public $post_id = false;

    /**
    * Read more Text for excerpt
    * @since Suitbuilder 1.0.6
    * @access public
    * @var string
    */
    public $more_text = '';

    /**
    * So you can call: suitbuilder_excerpt( 'short' );
    *
    * @since  Suitbuilder 1.0.6
    * @access protected
    * @var    array
    */
    protected $types = array(
        'short'   => 25,
        'regular' => 55,
        'long'    => 100
    );

    /**
    * Stores class instance
    * 
    * @since  Suitbuilder 1.0.6
    * @access protected
    * @var    object
    */
    protected static $instance = NULL;

    /**
    * Retrives the instance of this class
    * 
    * @since  Suitbuilder 1.0.6
    * @access public
    * @return object
    */
    public static function get_instance() {

        if ( ! self::$instance ) {
          self::$instance = new self();
        }

        return self::$instance;
    }

    /**
    * Sets the length for the excerpt,then it adds the WP filter
    * And automatically calls the_excerpt();
    *
    * @since Suitbuilder 1.0.6
    * @param string $new_length 
    * @access public
    * @return void
    */
    public function excerpt( $new_length = 55, $echo, $more_text, $page_id ) {

        $this->post_id  = $page_id;
        $this->length    = $new_length;
        $this->more_text = $more_text;
        add_filter( 'excerpt_more', array( $this, 'new_excerpt_more' ) );
        add_filter( 'excerpt_length', array( $this, 'new_length' ) );

        if( $echo ){
          the_excerpt();
        }else{
            $content = $page_id ?  get_the_excerpt( $page_id ) :  get_the_excerpt();
            remove_filter( 'excerpt_more', array( $this, 'new_excerpt_more' ) );
            remove_filter( 'excerpt_length', array( $this, 'new_length' ) );
            return $content;
        }
    
    }

    public function new_excerpt_more( $more ){
        $output = '';
        global $suitbuilder_customizer_all_values;

        if( is_admin() ){
            return $more;
        }
        if( 1 == $suitbuilder_customizer_all_values['suitbuilder-latest-blog-enable-button'] ){
            $read_more_text = esc_html__('Read more','suitbuilder');
        }
        if ( ! empty( $read_more_text ) ) {
            $output = ' <div class="read-more-text"><a href="' . esc_url( get_permalink( $this->post_id ) ) . '" class="read-more">' . $read_more_text . '</a></div>';
        }

        return $this->more_text . $output;
    }

    /** 
    * Tells WP the new length
    *
    * @since Suitbuilder 1.0.6
    * @access public
    * @return int
    */
    public function new_length( $length ) {
        if( is_admin() ){
            return $length;
        }

        if( isset( $this->types[ $this->length ] ) ){
          return $this->types[ $this->length ];
        }else{
          return $this->length;
        }
    }
}

endif;

/**
* Call to Suitbuilder_Excerpt
*
* @since  1.0.6
* @uses   Suitbuilder_Excerpt:::get_instance()->excerpt()
* @param  int $length
* @return void
*/
if( ! function_exists( 'suitbuilder_excerpt' ) ):

    function suitbuilder_excerpt( $length = 30, $echo = true, $more = '', $page_id = false ) {
        $length = apply_filters( 'suitbuilder_custom_excerpt_length', $length );
        return Suitbuilder_Excerpt::get_instance()->excerpt( $length, $echo, $more, $page_id );
    }
endif;