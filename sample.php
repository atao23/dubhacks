#!/usr/bin/php
<?php

/**
 * Yelp API v2.0 code sample.
 *
 * This program demonstrates the capability of the Yelp API version 2.0
 * by using the Search API to query for businesses by a search term and location,
 * and the Business API to query additional information about the top result
 * from the search query.
 * 
 * Please refer to http://www.yelp.com/developers/documentation for the API documentation.
 * 
 * This program requires a PHP OAuth2 library, which is included in this branch and can be
 * found here:
 *      http://oauth.googlecode.com/svn/code/php/
 * 
 * Sample usage of the program:
 * `php sample.php --term="bars" --location="San Francisco, CA"`
 */

// Enter the path that the oauth library is in relation to the php file
require_once('OAuth.php');

// Set your OAuth credentials here  
// These credentials can be obtained from the 'Manage API Access' page in the
// developers documentation (http://www.yelp.com/developers)

$CONSUMER_KEY = 'SZHASoV4ahH-OisGrSLgBQ';
$CONSUMER_SECRET = 'DpbKTCsb7RGXQE78qBaFzMDMXYo';
$TOKEN = 'bRKmn_1yc_7nRlep2XsJfwU8TgJN0_Xn';
$TOKEN_SECRET = '3j23jjeUQhWDyiR3E_6FOs8Vf5o';

$API_HOST = 'api.yelp.com';
$DEFAULT_TERM = 'Food';
$DEFAULT_LOCATION = 'Seatte, WA';
$DEFAULT_SORT = 0;
$SEARCH_LIMIT = 20;
$SEARCH_PATH = '/v2/search/';
$BUSINESS_PATH = '/v2/business/';


/** 
 * Makes a request to the Yelp API and returns the response
 * 
 * @param    $host    The domain host of the API 
 * @param    $path    The path of the APi after the domain
 * @return   The JSON response from the request      
 */
function request($host, $path) {
    $unsigned_url = "http://" . $host . $path;

    // Token object built using the OAuth library
    $token = new OAuthToken($GLOBALS['TOKEN'], $GLOBALS['TOKEN_SECRET']);

    // Consumer object built using the OAuth library
    $consumer = new OAuthConsumer($GLOBALS['CONSUMER_KEY'], $GLOBALS['CONSUMER_SECRET']);

    // Yelp uses HMAC SHA1 encoding
    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();

    $oauthrequest = OAuthRequest::from_consumer_and_token(
        $consumer, 
        $token, 
        'GET', 
        $unsigned_url
    );
    
    // Sign the request
    $oauthrequest->sign_request($signature_method, $consumer, $token);
    
    // Get the signed URL
    $signed_url = $oauthrequest->to_url();
    
    // Send Yelp API Call
    $ch = curl_init($signed_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $data = curl_exec($ch);
    curl_close($ch);
    
    return $data;
}

/**
 * Query the Search API by a search term and location 
 * 
 * @param    $term        The search term passed to the API 
 * @param    $location    The search location passed to the API 
 * @return   The JSON response from the request 
 */
function search($term, $location, $sort, $offset) {
    $url_params = array();
    
    $url_params['term'] = $term;
    $url_params['location'] = $location;
    $url_params['sort'] = $sort;
    $url_params['limit'] = $GLOBALS['SEARCH_LIMIT'];
    $url_params['offset'] = $offset;
    $search_path = $GLOBALS['SEARCH_PATH'] . "?" . http_build_query($url_params);


    // print($search_path);
    
    return request($GLOBALS['API_HOST'], $search_path);
}

/**
 * Query the Business API by business_id
 * 
 * @param    $business_id    The ID of the business to query
 * @return   The JSON response from the request 
 */
function get_business($business_id) {
    $business_path = $GLOBALS['BUSINESS_PATH'] . $business_id;
    
    return request($GLOBALS['API_HOST'], $business_path);
}

function query_lat_lng($street, $city, $state, $zip, $country) {
    $streetNoSpace = str_replace(" ", "+", $street);
    $googleKey = 'AIzaSyBj-sD-syW-Uzb9POxM5ptutzseVUYn7yU';
    $googleUrl = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $streetNoSpace . "," . $city . "," . $state . "&key=" . $googleKey;
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $googleUrl,
        CURLOPT_USERAGENT => 'DubHacks'
    ));

    $resp = curl_exec($curl);
    curl_close($curl);
    
    $parsed_response = json_decode($resp, true);
    $lat = $parsed_response['results'][0]['geometry']['location']['lat'];
    $lng = $parsed_response['results'][0]['geometry']['location']['lng'];

    $processed_response = array();
    $processed_response["lat"] = $lat;
    $processed_response["lng"] = $lng;
    return $processed_response;
}

function check_database_for_query($term, $location) {
    $filepath = "";
    $filename = $filepath . $term . "+" . $location . ".json";
    return file_exists($filename);
}

function perform_query($term, $location, $sort) {
    $preprocessed_response = array();

        for($i = 0; $i < 20; $i+= 20) {
            $response = search($term, $location, $sort, $i);

            $parsed_response = json_decode($response, true);

            foreach ($parsed_response['businesses'] as $business) {
                $response=array();

                if ($business['location']['coordinate']['longitude'] == null || 
                    $business['location']['coordinate']['latitude'] == null) {
                    $latLng = query_lat_lng($business['location']['address'][0], $business['location']['city'],
                        $business['location']['state_code'], $business['location']['postal_code'],
                        $business['location']['country_code']);
                    $response['longitude'] = $latLng["lng"];
                    $response['latitude'] = $latLng["lat"];
                } else {
                    $response["longitude"] = $business['location']['coordinate']['longitude'];
                    $response["latitude"] = $business['location']['coordinate']['latitude'];
                }

                $response['rating'] = $business['rating'];
                
                $preprocessed_response[] = $response;
            }
        }

        $preprocessed_json = json_encode($preprocessed_response);
        $filepath = "";
        $filename = $filepath . $term . "+" . $location . ".json";
        $file = fopen($filename, "w");
        fwrite($file, $preprocessed_json);
        print($preprocessed_json);
}

function serve_database_result($term, $location) {
    $filepath = "";
    $filename = $filepath . $term . "+" . $location . ".json";
    $file_data = file_get_contents($filename);
    print($file_data);
<<<<<<< HEAD
=======
    /*$json_data = json_decode($file_data, true);
    var_dump($json_data);
    print($json_data);*/
>>>>>>> 5edf00b6b2d6a9002372290ec9657c9b6ede8393
}

/**
 * Queries the API by the input values from the user 
 * 
 * 
 * 
 */
function query_api() {     
    $term = "Food";
    $location = "Seattle";
    $sort = 2;

   if (isset($_GET["term"]) && isset($_GET["location"])) {
        $term = str_replace(" ", "+", $_GET["term"]);
        $location = str_replace(" ", "+", $_GET["location"]);
    }

    $term = strtolower($term);
    $location = strtolower($location);

    if (check_database_for_query($term, $location)) {
        serve_database_result($term, $location);
    } else {
        perform_query($term, $location, $sort);
    }

}

/**
 * User input is handled here 
 */

query_api();
?>