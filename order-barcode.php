<?php
/*
* Plugin Name: Order Barcode for WooCommerce
* Plugin URI:  
* Description: Barcodes are generated for each order as soon as they are placed in your site.
* Version: 1.0.3
* Author: mascotdevelopers
* Author URI: https://www.mascotdevelopers.com/
* Text Domain: orderbarcode
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
{
  exit;
}
if ( ! defined( 'ORDERBARCODE_VERSION' ) ) {
    define( 'ORDERBARCODE_VERSION', '1.0.0' );
}
if ( ! defined( 'ORDERBARCODE_CSS_URI' ) ) {
    define( 'ORDERBARCODE_CSS_URI', plugins_url( 'css/',__FILE__ ) );
}
if ( ! defined( 'ORDERBARCODE_JS_URI' ) ) {
    define( 'ORDERBARCODE_JS_URI', plugins_url( 'js/',__FILE__ ) );
}
if ( ! defined( 'ORDERBARCODE_API_ENDPOINT' ) ) {
    define( 'ORDERBARCODE_API_ENDPOINT',esc_url('https://api-bwipjs.metafloor.com') );
}
if ( ! defined( 'ORDERBARCODE_SUPPORT_LINK' ) ) {
    define( 'ORDERBARCODE_SUPPORT_LINK',esc_url('https://wordpress.org/support/plugin/order-barcode-for-woocommerce') );
}
if ( ! defined( 'ORDERBARCODE_REVIEW_LINK' ) ) {
    define( 'ORDERBARCODE_REVIEW_LINK',esc_url('https://wordpress.org/plugins/order-barcode-for-woocommerce/#reviews') );
}

if ( ! defined( 'ORDERBARCODE_PRO_LINK' ) ) {
    define( 'ORDERBARCODE_PRO_LINK',esc_url('https://mascotdevelopers.com/order-barcode-pro') );
}


class ORDERBARCODE
{
    function __construct()
    {   
        add_action('wp_enqueue_scripts', array($this,'woobar_styles_scripts'),50);
        add_action( 'admin_enqueue_scripts', array($this,'woobar_admin_script_style'));
        
        add_action('woocommerce_order_details_after_order_table', array($this,'woobar_custom_field_display_order_details'), 10, 1 );
        add_action('woocommerce_admin_order_data_after_shipping_address', array($this,'woobar_checkout_field_display_admin_order_meta'), 10, 1 );
        
        add_action( 'woocommerce_email_order_meta', array($this,'woobar_add_email_order_meta'), 10, 3 );

        add_action('wpo_wcpdf_after_document_label', array($this,'woobar_wcpdf_qrcode'), 10, 2 );        
        add_filter( 'plugin_action_links', array($this,'woobar_admin_links'), 10, 2 );
        add_filter( 'plugin_row_meta', array($this,'woobar_plugin_row_meta') , 10, 2 );

    }//end of function
    function woobar_styles_scripts()
    {
        wp_enqueue_style( 'woobar-style-css', ORDERBARCODE_CSS_URI. 'woobar-main.css',array(),ORDERBARCODE_VERSION);                
        wp_enqueue_script( 'woobar-main-script', ORDERBARCODE_JS_URI . 'woobar-main.js',array('jquery'),ORDERBARCODE_VERSION );
        wp_localize_script( 'woobar-main-script', 'woobarsettings', array( 'ajaxurl' => admin_url('admin-ajax.php'),'base_url'=>get_site_url()) );
    }//end of function
    function woobar_admin_script_style()
    {
        wp_enqueue_style( 'woobar-style-css', ORDERBARCODE_CSS_URI. 'woobar-admin.css',array(),ORDERBARCODE_VERSION);                
    }//end of function
    function woobar_custom_field_display_order_details($order)
    {
        $order_id=$order->id;
        $url = add_query_arg(
            [
                'bcid' => 'code128',
                'text'   => $order_id,                    
                'alttext'=>$order_id,
                'height'=>10,
                'barcolor'=>'000000'
            ], ORDERBARCODE_API_ENDPOINT
        );
        $html='<div style="display:inline-block">';
        $html.='<img src="'.esc_url($url).'">';
        $html.='</div>';
        echo $html;
    }//end of function
    function woobar_checkout_field_display_admin_order_meta($order)
    {       
        $order_id=$order->id;
        $url = add_query_arg(
            [
                'bcid' => 'code128',
                'text'   => $order_id,                    
                'alttext'=>$order_id,
                'height'=>10,
                'barcolor'=>'000000'
            ], ORDERBARCODE_API_ENDPOINT
        );
        $html='<div style="display:inline-block">';
        $html.='<img src="'.esc_url($url).'">';
        $html.='</div>';
        echo $html;
    }//end of function
    function woobar_add_email_order_meta( $order, $sent_to_admin, $plain_text )
    { 
        $html='';
        if ( $plain_text === false ) 
        { 
            $order_id=$order->id;
            $url = add_query_arg(
                [
                    'bcid' => 'code128',
                    'text'   => $order_id,                    
                    'alttext'=>$order_id,
                    'height'=>10,
                    'barcolor'=>'000000'
                ], ORDERBARCODE_API_ENDPOINT
            );

            $html='<div style="display:inline-block">';
            $html.='<img src="'.esc_url($url).'">';
            $html.='</div>';
            echo $html;     
        } 
    }//end of function
    function woobar_wcpdf_qrcode ($template_type, $order) 
    {
        $order_id=$order->id;
        $url = add_query_arg(
            [
                'bcid' => 'code128',
                'text'   => $order_id,                    
                'alttext'=>$order_id,
                'height'=>10,
                'barcolor'=>'000000'
            ], ORDERBARCODE_API_ENDPOINT
        );
        $html='<div style="display:inline-block">';
        $html.='<img src="'.esc_url($url).'">';
        $html.='</div>';
        echo $html;
    }//end of function
    function woobar_admin_links($links, $file )
    {
        $fileName = 'order-barcode/order-barcode.php';
        
        $fileName = preg_replace('/\_trial\//', '/', $fileName);
        $fileName = preg_replace('/\_commercial\//', '/', $fileName);
        if ($file == $fileName) 
        {
            $pro_link = '<a target="_blank" class="get-pro" href="'.ORDERBARCODE_PRO_LINK.'">'.esc_html__('Get Pro','orderbarcode').'</a>';
            array_push( $links, $pro_link );    
        }
        return $links;
    }//end of function

    function woobar_plugin_row_meta( $links, $file ) 
    {
        if (strpos(plugin_dir_path(__FILE__),plugin_dir_path($file))) {
            $row_meta = array($links[0],$links[1]);
            $row_meta['Support']='<a target="_blank" href="'.ORDERBARCODE_SUPPORT_LINK.'">'.esc_html__('Support','orderbarcode').'</a>';
            $row_meta['Getpro']='<a target="_blank" class="get-pro" href="'.ORDERBARCODE_PRO_LINK.'">'.esc_html__('Get Pro','orderbarcode').'</a>';
            $row_meta['RateUs']='<a target="_blank" href="'.ORDERBARCODE_REVIEW_LINK.'"><span class="dashicons dashicons-star-filled stdi-rate"></span>'.esc_html__('Rate Us','orderbarcode').'</a>';
            
            return $row_meta;
        }
        return (array) $links;
    }//end of function
    
}//end of class
new ORDERBARCODE();


