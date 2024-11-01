<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



class UberCXAddrVal{

	//Name of the settings arrays
	protected $option_name = 'ubercx_addr_settings';  //this is what gets saved
	protected $option_group = 'ubercx_addr_settings'; //our settings group
	protected $section_name = 'ubercx_settings_section1';
    /**
     * @var UberCXAccountVerifier
     */
	protected $account_verifier;

	public $enabled;		//is it enabled?
    protected $user_key_validation_message = '';
    

	//The constructor
	public function __construct(){
	    $this->account_verifier = new UberCXAccountVerifier();

		add_filter('woocommerce_settings_tabs_array', array($this, 'add_woo_settings_tab'), 51);
        add_action('woocommerce_settings_tabs_settings_tab_snapcx_avs', array($this, 'add_woo_settings_tab_options'));
        add_action('woocommerce_update_options_settings_tab_snapcx_avs', array($this, 'update_woo_settings_tab_options'));

        //inject our custom eror html/jscript into the checkout page
		add_action('woocommerce_checkout_before_customer_details',array(&$this, 'add_error_html'),12);
		//lets hook into the checkout function
		add_action('woocommerce_after_checkout_validation', array(&$this, 'validate_address'));
		//Handles the ajax call
		add_action('wp_ajax_ubercx_get_error', array(&$this, 'get_error'));
        add_action('wp_ajax_nopriv_ubercx_get_error', array(&$this, 'get_error'));

        //Settings Link
        add_filter( "plugin_action_links_".UBERCX_ADDR_BASENAME, array(&$this, 'plugin_add_settings_link'));
	}
	
	function get_error(){
		$trans = get_transient('ubercx_addr_val');
		//if we have no transient - it means there was no error!
		if($trans==false){
			wp_send_json( null );
		}
		//delete the transient
		delete_transient('ubercx_addr_val');
		//otherwise we have some error!
		wp_send_json( $trans );
	}

	function add_error_html() {
		$html = "
	<div id='ubercx_addr_correction' class='' style='display: none;'>
		<h3>There appears to be a problem with the address. Please correct or select one below.</h3>
		<div id='ubercx_orig_addr'>
			<div id='ubercx_addr_radio' class='ubercx-addr-radio'></div>	
			<div style='display: none;' id='ubercx_orig_placeholder'></div>						
		</div>		
	</div>
	";
		echo $html;
		echo '<div id="ubercx_error_placeholder"></div>';
		
		//add the ajax url var
		$html = '
			<script type="text/javascript">
			var ajaxurl = "' . admin_url('admin-ajax.php') . '";
			</script>				
		';
	
		echo $html;
	}


    /**
     * Add a new plugin's settings tab to Woocommerce settings page
     *
     * @param $settings_tabs
     * @return mixed
     */
	public function add_woo_settings_tab($settings_tabs) {
        $settings_tabs['settings_tab_snapcx_avs'] = __( 'SnapCX Address Validation', UBERCX_ADDR_DOMAIN );
        return $settings_tabs;
    }

    /**
     * Add options to Plugin's settings tap
     */
    public function add_woo_settings_tab_options() {
        woocommerce_admin_fields($this->get_woo_settings_tab_options());
    }

	/**
	 * Function to add plugin settings link to Plugins page
	 * 
	 */
	public function get_woo_settings_tab_options() {
        $settings = array(
            'section_title' => array(
                'name'     => __( 'Address Validation settings', UBERCX_ADDR_DOMAIN ),
                'type'     => 'title',
                'desc'     => '<p><i>Enter your snapCX User Key here. If you do not have one, <a target="_blank"  href="https://snapcx.io/pricing?solution=avs&utm_source=wordpress&utm_medium=plugin&utm_campaign=avs">sign up for a FREE snapCX account here and get subscription with TRIAL.</a> NO credit card required</i></p>',
                'id'       => 'ubercx_option_name[section_title]'
            ),
            'enable' => array(
                'name' => __( 'Enable/Disable', UBERCX_ADDR_DOMAIN ),
                'type' => 'checkbox',
                'desc' => __( '', UBERCX_ADDR_DOMAIN ),
                'id'   => 'ubercx_option_name[enable]'
            ),
            'user_key' => array(
                'name' => __( 'User Key', UBERCX_ADDR_DOMAIN ),
                'type' => 'text',
                'desc' => '<span> <a target="_blank" href="https://snapcx.io/pricing?solution=avs&utm_source=wordpress&utm_medium=plugin&utm_campaign=avs">Get your User Key (open a FREE TRIAL account)</a></span>',
                'id'   => 'ubercx_option_name[user_key]',
                "custom_attributes" => array(
                    'required' => 'required'
                )
            ),
            'enable_global' => array(
                'name' => __( 'Enable/Disable Global Address Validation', UBERCX_ADDR_DOMAIN ),
                'type' => 'checkbox',
                'desc' => __( '', UBERCX_ADDR_DOMAIN ),
                'id'   => 'ubercx_option_name[enable_global]'
            ),
            'sectionend' => array(
                'type' => 'sectionend',
                'id' => 'ubercx_option_name[sectionend]'
            ),
            'section_link_bottom' => array(
                'name'     => '',
                'type'     => 'title',
                'desc' => 'If you like this plugin please leave us a <a href="https://wordpress.org/support/plugin/woo-address-validation/reviews?rate=5#new-post" target="_blank" class="wc-rating-link" data-rated="Thanks :)">★★★★★</a> rating. Many thanks in advance!',
                'id'       => 'ubercx_option_name[section_link_bottom]'
            ),
        );
        return apply_filters( 'woocommerce_settings_tab_snapcx_avs_settings', $settings );
    }

    public function update_woo_settings_tab_options() {
	    if($this->validateUserKey()) {
            woocommerce_update_options( $this->get_woo_settings_tab_options() );
        }

        return;
    }

	function plugin_add_settings_link( $links ) {
	    $settings_link = '<a href="admin.php?page=wc-settings&tab=settings_tab_snapcx_avs">' . __( 'Settings' ) . '</a>';
	    array_push( $links, $settings_link );
	  	return $links;
	}

	/**
	 * Function to register activation actions
	 * 
	 */

	function ubercx_plugin_activate(){
			
		//Check for WooCommerce Installment
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) and current_user_can( 'activate_plugins' ) ) {
			// Stop activation redirect and show error
			wp_die('Sorry, but this plugin requires the Woocommerce to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
			
			
		}
		
	}
    /**
	 * Function to register deactivation actions
	 * 
	 */
	function ubercx_plugin_deactivate(){ 
	
		delete_option('ubercx_option_name');
	
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info()
	{
		//print 'Enter your settings below:';
	}

	
	/**
	 *
	 * Sanitize the form input
	 */
	public function sanitize($input){
		//is the enabled button checked?
		if(isset($input['enable'])){
			$input['enable'] = "checked";
		} else {
			$input['enable'] = "";
		}
		if(isset($input['enable_global'])){
			$input['enable_global'] = "checked";
		} else {
			$input['enable_global'] = "";
		}

		return $input;
	}


	/**
	 *
	 * Lets do the validation!
	 */
	public function validate_address( $data=''){

		//check if the plugin is enabled
		$opt = get_option('ubercx_option_name');
		$enabled = $opt['enable'];
		
		if($enabled != 'yes'){
			return;
		} 
		
		
		//ok if we have a 'which_to_use' it means the user has selected one - which means we validated already
		//Lets see if they have changed any data, if they have we need to revalidate!!!
		if(isset($_POST['ubercx_which_to_use'])){
			
			//ok lets see if any of the fields are dirty
			
			//which one did they select
			$selected = $_POST['ubercx_which_to_use'];
			
			// create the hidden id used in the html so we can check if it is dirty
			if($selected != 'orig'){
				$selected = "corrected_" . $selected;
			} 
			
			//collect the fields from the hidden fields in post
			$addr1 = $_POST['ubercx_addr_' . $selected . '_addr1'];
			$addr2 = $_POST['ubercx_addr_' . $selected . '_addr2'];
			$city = $_POST['ubercx_addr_' . $selected . '_city'];
			$state = $_POST['ubercx_addr_' . $selected . '_state'];
			$zip = $_POST['ubercx_addr_' . $selected . '_zip'];

			//Now compare them to the form to see if it is dirty

			//Billing or shipping addr?
			$dirty = false;
			
			if($data['ship_to_different_address'] == true){
				($data['shipping_address_1'] == $addr1) ? $dirty=$dirty : $dirty=true;
				($data['shipping_address_2'] == $addr2) ? $dirty=$dirty : $dirty=true;
				($data['shipping_city'] == $city) ? $dirty=$dirty : $dirty=true;
				($data['shipping_state'] == $state) ? $dirty=$dirty : $dirty=true;
				($data['shipping_postcode'] == $zip) ? $dirty=$dirty : $dirty=true;
				//($data['shipping_country'] == 'US') ? $dirty=$dirty : $dirty=true;
			} else {
				($data['billing_address_1'] == $addr1) ? $dirty=$dirty: $dirty=true;
				($data['billing_address_2'] == $addr2) ? $dirty=$dirty: $dirty=true;
				($data['billing_city'] == $city) ? $dirty=$dirty: $dirty=true;
				($data['billing_state'] == $state) ? $dirty=$dirty: $dirty=true;
				($data['billing_postcode'] == $zip) ? $dirty=$dirty : $dirty=true;
				//($data['billing_country'] == 'US') ? $dirty=$dirty: $dirty=true;
			}				
			
			//if clean then lets just return the data and we are good to go
			if(!$dirty){
				
				//TODO for now we return nothing so the order doesnt process
				//faking error on clean!
				//wc_add_notice( __( 'Everything is good but I dont want to submit!', UBERCX_ADDR_DOMAIN ), 'error' );
				
				//return;
				
				
				return $data;
			} 
		}
		
		//so either it is dirty or it is the first time thru - either way validate the address!
		//now check if the user opted to use the corrected addr
		
		//get the user key
		$user_key = $opt['user_key'];
		
		//lets get the address, ship to billing?
		
		if($data['ship_to_different_address'] == true){
			//use the shipping address
			$first_name = $data['shipping_first_name'];
			$last_name = $data['shipping_last_name'];
			$address_1 = $data['shipping_address_1'];
			$address_2 = $data['shipping_address_2'];
			$city = $data['shipping_city'];
			$state = $data['shipping_state'];
			$zip = $data['shipping_postcode'];
			$country =  $data['shipping_country'];
		} else {
			//otherwise use the billing addres
			$first_name = $data['billing_first_name'];
			$last_name = $data['billing_last_name'];
			$address_1 = $data['billing_address_1'];
			$address_2 = $data['billing_address_2'];
			$city = $data['billing_city'];
			$state = $data['billing_state'];
			$zip = $data['billing_postcode'];
			$country =  $data['billing_country'];
		}
		
		
		//ok now lets call our API
		$api_url  =  $this->uc_getApiUrl();
		$global_api_url  =  $this->uc_getGlobalApiUrl();
		
			//TODO need zip here
			//TODO request_id needs to be generated dynamically. Possibly using storeaddress_timestamp	
			//TODO Add address2, only if available.
		$requestId = 'WooCommerce_' . time();
		
		//need to check we are a US address or another. So that we call API endpoint as per address.
		if($data['ship_to_different_address'] == true){
			if($data['shipping_country'] != 'US' && $opt['enable_global'] == 'yes' )
				$request_url = $global_api_url;
			else if ( $data['shipping_country'] == 'US' )
				$request_url = $api_url;
			else 
				return;
		} else {
			if($data['billing_country'] != "US" && $opt['enable_global'] == 'yes' )
				$request_url = $global_api_url;
			else if ( $data['billing_country'] == 'US' )
				$request_url = $api_url;
			else 
				return;
		}
		
		$url = $request_url.'?request_id='.$requestId.'&street='.urlencode($address_1).'&secondary='.urlencode($address_2).'&state='.urlencode($state).'&city='.urlencode($city).'&zipcode='.urlencode($zip).'&country='.urlencode($country);    
			
			global $woocommerce,$AVSVersion;
			// Start cURL
			$curl = curl_init();
			// Headers
			$headers = array('platform: woocommerce','version:'.$woocommerce->version,'pVersion:'.$AVSVersion);
			$headers[] = 'user_key:'.$user_key;
			//$headers[] = 'Accept: application/json';
			curl_setopt( $curl, CURLOPT_URL, $url );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $curl, CURLOPT_HEADER, false);
			
			// Get response
			$response = curl_exec($curl);
			
			// Get HTTP status code
			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			//TODO put status check for "200". 
			// Close cURL
			curl_close($curl);
			
			// Return response from server
			if($response!=''){
				$response = json_decode($response);	
			} else {
				return $data;
			}
			
			//Always store the original addr in the transient
			$transient = array();
			$transient['orig'] = array();
			$transient['orig']['addr1'] = $address_1;
			$transient['orig']['addr2'] = $address_2;
			$transient['orig']['city'] = $city;
			$transient['orig']['state'] = $state;
			$transient['orig']['zip'] = $zip;
			

			//ok lets deal with the response and see what we got - be cautious and handle
			//everything from blank onwards!
			//TODO check for status == 200 
			if(is_object($response)
			&& isset( $response->header)
			&& isset( $response->header->status) 
			&& $response->header->status == 'SUCCESS'){
				//we should have something returned
				
				
				//lets see what kind of response we got...
				//The response code is hidden in the array, so first make sure
				//it exists then switch, am assuming the first one is the same for all
				//TODO check, if addressRecord is not null and has length > 0
				if(isset($response->addressRecord[0])
				&& isset($response->addressRecord[0]->addressSummary)
				&& isset($response->addressRecord[0]->addressSummary->matchCode)){
					//ok we have some kind of response, lets populate the transient
					
					switch($response->addressRecord[0]->addressSummary->matchCode){
						case 'AVS_01':
							//so the service is funky and validates not including zip
							//so if the response we get has a different zip we need to create a corrected address
							$returnedPostcode = wc_normalize_postcode($response->addressRecord[0]->address[0]->zipCode);
							$origZipCode = wc_normalize_postcode($zip);
							if($returnedPostcode != $origZipCode){
								wc_add_notice( __( 'There is a problem with your zip code, please check', UBERCX_ADDR_DOMAIN ), 'error' );
								//loop thru the matching addrs
								$transient['corrected'] = array();
									
								for($i=0; $i<count($response->addressRecord[0]->address); $i++){
								
									//save on typing store in temp!!!!
									$temp = $response->addressRecord[0]->address[$i];
								
									$transient['corrected'][$i]['addr1'] =  is_null($temp->addressLine1) ? "" : $temp->addressLine1 ;
									$transient['corrected'][$i]['addr2'] = is_null($temp->addressLine2) ? "" : $temp->addressLine2 ;
									$transient['corrected'][$i]['city'] = is_null($temp->city) ? "" : $temp->city ;
									$transient['corrected'][$i]['state'] = is_null($temp->state) ? "" : $temp->state;
									$transient['corrected'][$i]['zip'] = is_null($temp->zipCode) ? "" : $temp->zipCode;
								}
								
								break;	
							}
							
							return $data;
							break;
							
						case 'AVS_02':
							//OK we should get a bunch of returned addr's - lets
							//add them to the transient
							wc_add_notice( __( 'There appears to be an error in your address', UBERCX_ADDR_DOMAIN ), 'error' );
							
							//loop thru the matching addrs
							$transient['corrected'] = array();
							
							for($i=0; $i<count($response->addressRecord[0]->address); $i++){
								
								//save on typing store in temp!!!!
								$temp = $response->addressRecord[0]->address[$i];
								
								$transient['corrected'][$i]['addr1'] =  is_null($temp->addressLine1) ? "" : $temp->addressLine1 ;
								$transient['corrected'][$i]['addr2'] = is_null($temp->addressLine2) ? "" : $temp->addressLine2 ;
								$transient['corrected'][$i]['city'] = is_null($temp->city) ? "" : $temp->city ;
								$transient['corrected'][$i]['state'] = is_null($temp->state) ? "" : $temp->state;
								$transient['corrected'][$i]['zip'] = is_null($temp->zipCode) ? "" : $temp->zipCode;
							}
							break;
							
						case 'AVS_03':
							//we just show the original
							//but it is invalid!!!! Need to make sure the user corrects it
							wc_add_notice( __( 'There is a problem with your address - please check below', UBERCX_ADDR_DOMAIN ), 'error' );
							break;
							
						default:
							//unknown return code, have to just go with the addr
							return $data;
					}
				} else {
					//no match code found so something went wrong, lets just let it go thru and go with user addr
					return $data;
				}
				
			} else {
				//TODO, put some notification. Like re-confirm user, if you want to use this shipping address.
				//nothing was returned from the API call or something went wrong, just go with orig
				return $data;
			}
			
			//ok lets see if we can force a reload!!!
			//WC()->session->set('reload_checkout', true);

			set_transient('ubercx_addr_val', $transient);
			
			return $data;
	}

	public function validate_settings(){

	}
	
	/**
	 * Function to get end-point of API
	 * 
	 * @since 1.0.0
	 */
	public function uc_getApiUrl(){
		if(file_exists(plugin_dir_path( __FILE__ ).'config.txt')){
			$response = file_get_contents(plugin_dir_path( __FILE__ ).'config.txt');
			$response = json_decode($response);
			if(!empty($response)){
				return $response->api_endpoint;
			}
		} 
	}
	
	/**
	 * Function to get end-point of API
	 * 
	 * @since 1.2.0
	 */
	public function uc_getGlobalApiUrl(){
		if(file_exists(plugin_dir_path( __FILE__ ).'config.txt')){
			$response = file_get_contents(plugin_dir_path( __FILE__ ).'config.txt');
			$response = json_decode($response);
			if(!empty($response)){
				return $response->global_api_endpoint;
			}
		} 
	}
	
	public function wc_normalize_postcode( $postcode ) {
        return preg_replace( '/[\s\-]/', '', trim( strtoupper( $postcode ) ) );
    }


    /**
     * Validate user_key via API endpoint.
     *
     * @return bool
     * @throws Exception
     */
	protected function validateUserKey() {
        WC_Admin_Notices::remove_all_notices();

        if (!isset($_POST['ubercx_option_name']['user_key'])) {
            $this->showInvalidUserKeyNotice("User key cannot be empty.");
            return false;
        }

        // Do not bother API in case if user_key wasn't changed.
//        $currentOptions = get_option('ubercx_option_name');
//        if ($_POST['ubercx_option_name']['user_key'] == $currentOptions['user_key']) {
//            return true;
//        }

        $user_key = wp_strip_all_tags($_POST['ubercx_option_name']['user_key']);
        $validateResult = $this->account_verifier->validateUserKey($user_key);

        if(isset($validateResult['is_valid']) && $validateResult['is_valid'] === true) {
            if (isset($validateResult['show_notice']) && isset($validateResult['message'])) {
                $this->showUserKeyNotice($validateResult['message']);
            } else {
                $this->showSuccessMessage("Your User Key is validated.");
            }
            return true;
        }

        if(isset($validateResult['message'])) {
            $this->showInvalidUserKeyNotice($validateResult['message']);
            return false;
        }

        $this->showInvalidUserKeyNotice("Invalid User Key.");
        return false;
    }

    protected function showInvalidUserKeyNotice($text = '') {
        WC_Admin_Settings::add_error(__($text, UBERCX_ADDR_DOMAIN));
    }

    protected function showUserKeyNotice($text = '') {
        WC_Admin_Notices::add_custom_notice(UBERCX_ADDR_DOMAIN.'_userkey_notice',__($text, UBERCX_ADDR_DOMAIN));
    }

    protected function showSuccessMessage($text = '') {
        WC_Admin_Settings::add_message(__($text, UBERCX_ADDR_DOMAIN));
    }
}