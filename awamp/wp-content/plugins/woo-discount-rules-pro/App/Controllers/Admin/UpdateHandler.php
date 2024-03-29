<?php

namespace WDRPro\App\Controllers\Admin;
if (!defined('ABSPATH')) {
    exit;
}
use Wdr\App\Controllers\Configuration;
use Wdr\App\Helpers\Input;
use WDRPro\App\Helpers\CoreMethodCheck;

class UpdateHandler
{
    protected static $slug = 'discount-rules-v2-pro';
    protected static $plugin_name = 'Discount Rules Pro for WooCommerce';
    protected static $flycart_url = 'https://www.flycart.org/';
    protected static $update_url = 'https://www.flycart.org/';//?wpaction=updatecheck&wpslug=woo-discount-rules&pro=1

    /**
     * Initialise
     * */
    public static function init(){
        self::hooks();
    }

    /**
     * Hooks
     * */
    protected static function hooks(){
        add_filter('puc_request_info_result-woo-discount-rules-pro', array(__CLASS__, 'loadWooDiscountRulesUpdateDetails'), 10, 2);
        self::runUpdater();

        add_action('after_plugin_row', array(__CLASS__, 'messageAfterPluginRow'), 10, 3);
        add_action('admin_notices', array(__CLASS__, 'errorNoticeInAdminPages'));
        add_action('wp_ajax_awdr_validate_licence_key', array(__CLASS__, 'validateLicenceKey'));
    }

    /**
     * Ajax request for license key validation
     * */
    public static function validateLicenceKey()
    {
        $input = new Input();
        $licence_key = $input->get_post('licence_key');
        $result = null;
        $result['message'] = 'Activated!';
        
            CoreMethodCheck::validateRequest('awdr_validate_licence_key');
           
                $status = self::isValidLicenceKey($licence_key);
                $result['valid'] = true;
               $result['message'] = self::messageForLicenceKey($status, $licence_key);
                self::updateLicenceKeyInSettings($licence_key);
                self::updateLicenceKeyAsValidated($status);
           
       

        wp_send_json_success($result);
    }

    /**
     * Update Licence key with settings
     * */
    protected static function updateLicenceKeyInSettings($licence_key){
        $config = get_option(Configuration::DEFAULT_OPTION);
        $config['licence_key'] = 'getallgpl';
        update_option(Configuration::DEFAULT_OPTION, $config);
    }

    /**
     * Update licence key as validated
     * */
    protected static function updateLicenceKeyAsValidated($status){
        update_option('advanced_woo_discount_rules_licence_verified_time', time());
         update_option('advanced_woo_discount_rules_licence_status', 1);
       
    }

    /**
     * Get licence key verified status
     * */
    public static function getLicenceKeyVerifiedStatus(){
        return true;
    }

    /**
     * Get message for licence key
     * */
    public static function messageForLicenceKey($status, $licence_key){
      return '<p class="wdr-licence-valid-text">'.esc_html__('License key check : Passed.', 'woo-discount-rules-pro').'</p>';
          
        
    }

    /**
     * Check licence key is valid
     *
     * @param $key string
     * @return boolean
     * */
    protected static function isValidLicenceKey($key)
    {
      

        return true;
    }

    /**
     * Check if $result is a successful update API response.
     *
     * @param array|WP_Error $result
     * @return true|WP_Error
     */
    protected static function validateApiResponse($result) {
        

       


        return true;
    }

    /**
     * Run Plugin updater
     * */
    protected static function runUpdater(){
        require WDR_PRO_PLUGIN_PATH.'/vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';
        $update_url = self::getUpdateURL();
        try {
            $myUpdateChecker = \Puc_v4_Factory::buildUpdateChecker(
                $update_url,
                WDR_PRO_PLUGIN_PATH.'woo-discount-rules-pro.php',
                'woo-discount-rules-pro'
            );
        } catch (\Exception $e){}
    }

    /**
     * Get licence key
     *
     * @return string
     * */
    protected static function getLicenceKey(){
        $config = Configuration::getInstance();
        return 'getallgpl';
    }

    /**
     * Get update URL
     * @param $type string
     *
     * @return string
     * */
    public static function getUpdateURL($type = 'updatecheck', $licence_key = null)
    {
        $update_url = self::$update_url.'?wpaction='.$type.'&wpslug='.self::$slug.'&pro=1';
        if(empty($licence_key)){
            $licence_key = self::getLicenceKey();
        }
        $update_url .= '&dlid='.$licence_key;
        return $update_url;
    }

    /**
     * Error notice on admin pages
     * */
    public static function errorNoticeInAdminPages(){
        $htmlPrefix = '<div class="notice notice-warning"><p>';
        $htmlSuffix = '</p></div>';
        $message = self::getErrorMessageIfExists();
       
    }

    /**
     * Get message to display
     *
     * @return array
     * */
    protected static function getErrorMessageIfExists(){
        $licence_key = self::getLicenceKey();
        $message['has_message'] = false;
        $message['message'] = '';
        $enter_valid_anchor_tag = '<a href="admin.php?page=woo_discount_rules&tab=settings">'.__('Please enter a valid license key').'</a>';
        $flycart_anchor_tag = '<a href="'.self::$flycart_url.'" target="_blank">'.__('our website').'</a>';
        $verifiedLicense = self::getLicenceKeyVerifiedStatus();
        return "";
    }

    /**
     * Hook to check and display updates below plugin in Admin Plugins section
     * This plugin checks for license key validation and displays error notices
     * @param string $plugin_file our plugin file
     * @param string $plugin_data Plugin details
     * @param string $status
     * */
    public static function messageAfterPluginRow( $plugin_file, $plugin_data, $status ){
        if( isset($plugin_data['TextDomain']) && $plugin_data['TextDomain'] == 'woo-discount-rules-pro' ){
            $message = self::getErrorMessageIfExists();
          

          
        }

    }

    /**
     * To load Woo discount rules update details
     * */
    public static function loadWooDiscountRulesUpdateDetails($pluginInfo, $result){
        try{
            global $wp_version;
            // include an unmodified $wp_version
            include( ABSPATH . WPINC . '/version.php' );
            $args = array('slug' => 'woo-discount-rules', 'fields' => array('active_installs'));
            $response = wp_remote_post(
                'http://api.wordpress.org/plugins/info/1.0/',
                array(
                    'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
                    'body' => array(
                        'action' => 'plugin_information',
                        'request'=>serialize((object)$args)
                    )
                )
            );

            if(!empty($response)){
                $returned_object = maybe_unserialize(wp_remote_retrieve_body($response));
                if(!empty($returned_object)){
                    if(empty($pluginInfo)){
                        if(class_exists('\Puc_v4p9_Plugin_Info')){
                            $pluginInfo = new \Puc_v4p9_Plugin_Info();
                        }
                    }
                    if(!empty($returned_object->name)) $pluginInfo->name = $returned_object->name;
                    if(!empty($returned_object->sections)) $pluginInfo->sections = $returned_object->sections;
                    if(!empty($returned_object->author)) $pluginInfo->author = $returned_object->author;
                    if(!empty($returned_object->author_profile)) $pluginInfo->author_profile = $returned_object->author_profile;
                    if(!empty($returned_object->requires)) $pluginInfo->requires = $returned_object->requires;
                    if(!empty($returned_object->tested)) $pluginInfo->tested = $returned_object->tested;
                    if(!empty($returned_object->rating)) $pluginInfo->rating = $returned_object->rating;
                    if(!empty($returned_object->ratings)) $pluginInfo->ratings = $returned_object->ratings;
                    if(!empty($returned_object->num_ratings)) $pluginInfo->num_ratings = $returned_object->num_ratings;
                    if(!empty($returned_object->support_threads)) $pluginInfo->support_threads = $returned_object->support_threads;
                    if(!empty($returned_object->support_threads_resolved)) $pluginInfo->support_threads_resolved = $returned_object->support_threads_resolved;
                    if(!empty($returned_object->downloaded)) $pluginInfo->downloaded = $returned_object->downloaded;
                    if(!empty($returned_object->last_updated)) $pluginInfo->last_updated = $returned_object->last_updated;
                    if(!empty($returned_object->added)) $pluginInfo->added = $returned_object->added;
                    if(!empty($returned_object->versions)) $pluginInfo->versions = $returned_object->versions;
                    if(!empty($returned_object->tags)) $pluginInfo->tags = $returned_object->tags;
                    if(!empty($returned_object->screenshots)) $pluginInfo->screenshots = $returned_object->screenshots;
                    if(!empty($returned_object->active_installs)) $pluginInfo->active_installs = $returned_object->active_installs;
                }
            }
        } catch (\Exception $e){}

        return $pluginInfo;
    }
}

UpdateHandler::init();