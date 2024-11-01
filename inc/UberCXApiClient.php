<?php

/**
 * Class UberCXApiClient
 *
 * Responsible for sending requests to a remote API endpoint.
 */
class UberCXApiClient
{

    public function getCurlHeaders()
    {
        global $woocommerce, $AVSVersion;

        return array(
            'platform:woocommerce',
            'version:'.$woocommerce->version,
            'pVersion:'.$AVSVersion,
            'application:avs'
        );

    }

    public function sendApiRequest($url)
    {
        $curl = curl_init();

        curl_setopt( $curl, CURLOPT_URL, $url );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $curl, CURLOPT_HTTPHEADER, $this->getCurlHeaders() );
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
        if ($response != '') {
            return json_decode($response);
        }

        return false;
    }

}