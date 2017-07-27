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

//TODO uncomment this whehn on Liv, Don't forget!!!

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
curl_close($ch1);

// map LC name -> GIS ID
// we use javascript to map uni<->LC, so the first step is already taken care of
$lc_json = 'lc_id.json';

$json = file_get_contents($lc_json, false, stream_context_create($arrContextOptions)); 
$lc_gis_map = json_decode($json,true); 



$user_lc = $lc_gis_map[$_POST['localcommittee']];
$program = intval($_POST['interested_in']);

// structure data for GIS
// form structure taken from actual form submission at auth.aiesec.org/user/sign_in
/*
$fields = array(
    'authenticity_token' => htmlspecialchars($gis_token),
    'user[email]' => htmlspecialchars($_POST['email']),
    'user[first_name]' => htmlspecialchars($_POST['first_name']),
    'user[last_name]' => htmlspecialchars($_POST['last_name']),
    'user[password]' => htmlspecialchars($_POST['password']),
    'user[phone]' => htmlspecialchars($_POST['phone']),
    'user[country]' => $configs["country_name"], //'POLAND', // EXAMPLE: 'GERMANY' 
    'user[mc]' => $configs["mc_id"], //'1626', // EXAMPLE: 1596
    'user[lc_input]' => $user_lc,
    'user[lc]' => $user_lc,
    'commit' => 'REGISTER'
    );*/
$fields = array( 'user'=>array(
    'email' => htmlspecialchars($_POST['email']),
    'first_name' => htmlspecialchars($_POST['first_name']),
    'last_name' => htmlspecialchars($_POST['last_name']),
    'password' => htmlspecialchars($_POST['password']),
    'phone' => htmlspecialchars($_POST['phone']),
    'lc' => $user_lc,
    'country_code' => '+52'
    )
    );


// UNCOMMENT HERE: to view the array which will be submitted to GIS
// echo "<h2>Text going to GIS</h2>";
// echo '<pre>';
// print_r($fields);
// echo "</pre>";

//url-ify the data for the POST
$fields_string = "";
foreach($fields as $key=>$value) { $fields_string .= $key.'='.urlencode($value).'&'; }
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


///////////PODIO Start /////////
///////////PODIO Start /////////
///////////PODIO Start /////////
///////////PODIO Start /////////
///////////PODIO Start /////////

//Podio submit
// This is to test the conection with the podio API and the authentication
Podio::setup('aiesec-mexico', $configs_external['podio_key']);


//getting the podio Id for each lc
$lc_podio = 'lc_podio.json';
$json_podio_lc = file_get_contents($lc_podio, false, stream_context_create($arrContextOptions)); 
$lc_podio_map = json_decode($json_podio_lc,true); 
$user_lc_podio = $lc_podio_map[$_POST['localcommittee']];

//getting the podio Id for each lc
$uni_podio = 'universidades_podio_state.json';

//since we had too many insittues we change the podio field that we submit of it depending on the lc
$intitute_name = "instituto-estado";
//The lcs that are citi/university based : Chihuahua, CDMX, GTO, NL
$os = array(27,22,5,8,15,17,18,26,23,25,29,30,21,6,12,1,16);
if (in_array(intval($user_lc_podio), $os)){
$intitute_name = "instituto";
$uni_podio = 'universidades_podio.json';
}

$json_podio_uni = file_get_contents($uni_podio, false, stream_context_create($arrContextOptions)); 
$uni_podio_map = json_decode($json_podio_uni,true); 
$user_uni_podio = $uni_podio_map[$_POST['university']];


//echo strval($user_uni_podio).' fue el que eligio  y el comite era '.$_POST['university'].'<br>';
$podio_id = intval($configs_external['podio_space_ogv_id']);

try {

//OGV
 if ($program == 1){
    Podio::authenticate_with_app(intval($configs_external['podio_space_ogv_id']),$configs_external['podio_space_ogv_key']);
    $podio_id = intval($configs_external['podio_space_ogv_id']);
    $gr_campaing_id = $configs_external['gr_campaign_ogv_id'] ; 
}
//OGT
else if ($program == 2){
    Podio::authenticate_with_app(intval($configs_external['podio_space_ogt_id']),$configs_external['podio_space_ogt_key']);
    $podio_id = intval($configs_external['podio_space_ogt_id']);
    $gr_campaing_id = $configs_external['gr_campaign_ogt_id'];
}
//OGE
else {
    Podio::authenticate_with_app(intval($configs_external['podio_space_oge_id']), $configs_external['podio_space_oge_key']);
    $podio_id = intval($configs_external['podio_space_oge_id']);
    $gr_campaing_id = $configs_external['gr_campaign_oge_id'];
}



$fields = new PodioItemFieldCollection(array(
  new PodioTextItemField(array("external_id" => "titulo", "values" => ($_POST['first_name'] ) )),
  new PodioTextItemField(array("external_id" => "apellido", "values" => $_POST['last_name'])),
  new PodioTextItemField(array("external_id" => "correo", "values" => $_POST['email'])),
  new PodioTextItemField(array("external_id" => "numero-telefonico", "values" => $_POST['phone'])),

  new PodioCategoryItemField(array("external_id" => "comite-local", "values" => intval($user_lc_podio))),
  new PodioCategoryItemField(array("external_id" => $intitute_name, "values" => intval($user_uni_podio))),
  new PodioCategoryItemField(array("external_id" => "fuente", "values" => intval($_POST['source'])))
  ));

// Create the item object with fields
// Be sure to add an app or podio-php won't know where to create the item
$item = new PodioItem(array(
  'app' => new PodioApp($podio_id), // Attach to app with app_id=123
  'fields' => $fields
  ));

// Save the new item
$item->save();


}
catch (PodioError $e) {
  // Something went wrong. Examine $e->body['error_description'] for a description of the error.
   
    header("Location: http://aiesec.org.mx/registro_no");
}


////////PODIO END /////////
////////PODIO END /////////
////////PODIO END /////////
////////PODIO END /////////
////////PODIO END /////////
////////PODIO END /////////


// function is_iterable($var)
// {
//     return $var !== null 
//     && (is_array($var) 
//         || $var instanceof Traversable 
//         || $var instanceof Iterator 
//         || $var instanceof IteratorAggregate
//         );
// }



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

////////////////GET RESPONSE 
///////////////////GET RESPONSE 
///////////////////GET RESPONSE 
///////////////////GET RESPONSE 
///////////////////GET RESPONSE 
////////////////GET RESPONSE 

$getresponse = new GetResponse($configs_external['gr_id']);

$getresponse->enterprise_domain =$configs_external['gr_api_domain'];

$getresponse->api_url = $configs_external['gp_api_url']; //

$gr_data = array(
    'name'              => $_POST['first_name'].' '.$_POST['last_name'],
    'email'             => $_POST['email'],
    'dayOfCycle'        => 0,
    'campaign'          => array(
        'campaignId' => ($gr_campaing_id)), 
    'ipAddress'         => get_client_ip(),
    'customFieldValues' => array(
        array('customFieldId' => 'zU3k6', //universidad
            'value' => array(
                $_POST['university']
                )),
        array('customFieldId' => 'zU3vv', //expa_id
            'value' => array(
                strval($ep_id)
                )
            ),
        array('customFieldId' => 'zU3kZ', //telefono
            'value' => array(
                '+52'.$_POST['phone']
                )
            ),
        array('customFieldId' => 'zU3kb', //nombre
            'value' => array(
                $_POST['first_name']
                )
            ),
        array('customFieldId' => 'zU3vI', //apellido
            'value' => array(
                $_POST['last_name']
                )
            ),
        array('customFieldId' => 'zU3kg', //comite
            'value' => array(
                $_POST['localcommittee']
                )
            )
        )
    );


$getresponse->addContact($gr_data);

////////////////getresponse ////////////////////////

header("Location: http://aiesec.org.mx/registro/?thank_you=true");

function curl_errors($ch)
{
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_errno= curl_errno($ch);

}
?>



