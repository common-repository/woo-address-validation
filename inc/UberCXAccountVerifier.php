<?php

/**
 * The purpose of this class is to perform user key verification through an external API endpoint.
 */
class UberCXAccountVerifier
{

    const VERIFY_URL = 'http://69.164.215.31:8081/developer/account/verify';
    const APPLICATION = 'avs';

    /**
     * @var UberCXApiClient
     */
    protected $apiClient;

    /**
     * UberCXAccountVerifier constructor.
     */
    public function __construct()
    {
        $this->apiClient = new UberCXApiClient();
    }

    /**
     * @param string $userKey
     * @return array
     * @throws Exception
     */
    public function validateUserKey($userKey)
    {
        $validationResult = array(
            'is_valid' => false,
            'message' => ''
        );
        $response = $this->sendVerifyRequest($userKey);

        if($response) {
            if(isset($response->isVerifySuccess)) {
                $validationResult['is_valid'] = $response->isVerifySuccess;
                $validationResult['message'] = __($response->messageFromProvider, UBERCX_ADDR_DOMAIN);
            }
        } else {
            $validationResult['is_valid'] = true;
            $validationResult['show_notice'] = true;
            $validationResult['message'] = __("User Key hasn't been verified yet.", UBERCX_ADDR_DOMAIN);
        }

        return $validationResult;
    }

    /**
     * @param string $userKey
     * @return bool|object
     * @throws Exception
     */
    protected function sendVerifyRequest($userKey)
    {
        if(!$this->apiClient) {
            throw new \Exception('UberCXApiClient is not initialized.');
        }

        $url = $this->getVerifyUrl() . "?user_key=$userKey&application=" . self::APPLICATION;

        return $this->apiClient->sendApiRequest($url);
    }

    protected function getVerifyUrl()
    {
        if (file_exists(plugin_dir_path(__FILE__) . 'config.txt')) {
            $response = file_get_contents(plugin_dir_path(__FILE__) . 'config.txt');
            $response = json_decode($response);
            if (!empty($response) && isset($response->verify_url)) {
                return $response->verify_url;
            }
        }

        return self::VERIFY_URL;

    }

}