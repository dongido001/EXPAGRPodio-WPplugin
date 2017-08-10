<?php
//update for epxa endpoint
 
//podio library
include_once 'podio-php-master/PodioAPI.php';
//get Response library
require_once 'getresponse-api-php-master/src/GetResponseAPI3.class.php';
require 'src/vendor/autoload.php';
// require '/wp-config-files/gis_lib/vendor/autoload.php';
// require 'getresponse-api-php-master/lib/src/autoload.php';

//private keys config files
$configs_external = include('wp_login_config.php');
//plugin configs
$configs = include('config.php');

#default is gv for hte campaing in get response
$gr_campaing_id = $configs_external['gr_campaign_ogv_id'];

//captcah verification
////captcah verification
/////captcah verification
/////captcah verification

//TODO uncomment this when on Live, Don't forget!!!

// $recaptcha = new \ReCaptcha\ReCaptcha($configs_external['recaptcha_secret']);

// $resp = $recaptcha->verify($_POST['g-recaptcha-response'], get_client_ip());
// if (!$resp->isSuccess()) {
//     $errors = $resp->getErrorCodes();
//     header("Location: http://aiesec.org.mx/registro_no");

//     return;
// }

//captcah verification
////captcah verification
/////captcah verification
/////captcah verification

/**
* AIESEC GIS Form Submission via cURL
* 
* This is a basic form processor to create new users for the Opportunities Portal
* so you can create and manage a registration form on your country website.
*
* 
*/



// UNCOMMENT HERE: to view the HTML form requested from the GIS
//print $result;


$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'https://auth.aiesec.org/users/sign_in',
    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
    ));
// Send the request & save response to $resp
$result = curl_exec($curl);


// Close request to clear up some resources
curl_close($curl);

// extract token from cURL result
preg_match('/<meta content="(.*)" name="csrf-token" \/>/', $result, $matches);
$gis_token = $matches[1];


// UNCOMMENT HERE: to view HTTP status and errors from curl
// curl_errors($ch1);

//close connection
// curl_close($ch1);

// map LC name -> GIS ID
// we use javascript to map uni<->LC, so the first step is already taken care of
$lc_json = 'lc_id.json';

//wasn't defined anywhere, ...
$arrContextOptions = [];
// die(plugins_url('', __FILE__));
$json = file_get_contents(plugins_url('', __FILE__) .'/' . $lc_json, false, stream_context_create($arrContextOptions)); 
$lc_gis_map = json_decode($json,true); 


$user_lc = $lc_gis_map[$_POST['localcommittee']];
$program = intval($_POST['interested_in']);

$user_lc = $_POST['university'];


$fields = array( 'user'=>array(
    'email' => htmlspecialchars($_POST['email']),
    'first_name' => htmlspecialchars($_POST['first_name']),
    'last_name' => htmlspecialchars($_POST['last_name']),
    'password' => htmlspecialchars($_POST['password']),
    'phone' => htmlspecialchars($_POST['phone']),
    'lc' => $user_lc,
    'country_code' => '+233'
    )
    );

$fields_string = "";
foreach($fields['user'] as $key=>$value) { 

    $fields_string .= $key.'='.urlencode($value).'&'; 
}
rtrim($fields_string, '&');

$innerHTML = "";
// UNCOMMENT THIS BLOCK: to enable real GIS form submission
$fieldsjs = json_encode($fields);

// POST form with curl
$url = "https://auth.aiesec.org/users.json";
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $url);
curl_setopt($ch2, CURLOPT_POST, count($fieldsjs));
curl_setopt($ch2, CURLOPT_POSTFIELDS, $fieldsjs);
curl_setopt($ch2, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json')                                                                       
);      
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
// give cURL the SSL Cert for Salesforce
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false); // TODO: FIX SSL - VERIFYPEER must be set to true
//
// "without peer certificate verification, the server could use any certificate,
// including a self-signed one that was guaranteed to have a CN that matched 
// the server’s host name."
// http://unitstep.net/blog/2009/05/05/using-curl-in-php-to-access-https-ssltls-protected-sites/
// 
// curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, 2);
// curl_setopt($ch2, CURLOPT_CAINFO, getcwd() . "\CACerts\VeriSignClass3PublicPrimaryCertificationAuthority-G5.crt");
$result = curl_exec($ch2);

$ep_id = json_decode($result,true)['person_id']; 

curl_errors($ch2);
// Check if any error occurred
if (curl_errno($ch2)) {

    header("Location: http://aiesec.org.mx/registro_no");
    return;
}


curl_close($ch2);



libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($result);    
libxml_clear_errors();
$selector = new DOMXPath($doc);

$result = $selector->query('//div[@id="error_explanation"]');


$children = $result->item(0)->childNodes;
if (is_iterable($children))
{
    foreach ($children as $child) {
        $tmp_doc = new DOMDocument();
        $tmp_doc->appendChild($tmp_doc->importNode($child,true));  
        $innerHTML .= strip_tags($tmp_doc->saveHTML());
        //$innerHTML.add($tmp_doc->saveHTML());
    }
}

$innerHTML = preg_replace('~[\r\n]+~', '', $innerHTML);
$innerHTML = str_replace(array('"', "'"), '', $innerHTML);




function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
     $ipaddress = getenv('HTTP_FORWARDED');
 else if(getenv('REMOTE_ADDR'))
    $ipaddress = getenv('REMOTE_ADDR');
else
    $ipaddress = 'UNKNOWN';
return $ipaddress;
}

//////////////getresponse ////////////////////////

$data =    array( 
                'fname' => $_POST['first_name'],
                'time' => current_time( 'mysql' ),
                'lname' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'source' => $_POST['source'],
                'interested_in' => $_POST['interested_in'],
                'lead_name' => $user_lc,
                'facebook_id' => $_POST['facebook'],
                'whatsapp' => $_POST['whatsapp'],
            );


header("Location: http://aiesec.org.mx/registro/?thank_you=true");

aiesec_add_data( $data );

function curl_errors($ch)
{
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_errno= curl_errno($ch);

}
