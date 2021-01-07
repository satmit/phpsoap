<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class soap extends CI_Controller {



	public function index()
	{

    try {

      if($_GET['profession_number']){
        $args['profession_number'] = $_GET['profession_number'];  
      }

      $client = new SoapClient('https://wstest.ahpra.gov.au/pie_int/svc/PractitionerRegistrationSearch/2.0.0/FindRegistrationService.svc?singleWsdl', 
          array(
              'location' => 'https://wstest.ahpra.gov.au/pie_int/svc/PractitionerRegistrationSearch/2.0.0/FindRegistrationService.svc',
              'uri' => 'https://wstest.ahpra.gov.au/pie_int/svc/PractitionerRegistrationSearch/2.0.0/FindRegistrationService.svc',

              'soap_version'   => SOAP_1_2,

              'trace' => 1,
              'exceptions'=> true ,
              'features' => SOAP_SINGLE_ELEMENT_ARRAYS + SOAP_USE_XSI_ARRAY_TYPE
          )
      );


      $ns ='http://ns.ahpra.gov.au/pie/xsd/common/CommonCoreElements/2.0.0';

      $service_auth = array(
          'UserId' =>  'piews_test@alexion.com',
          'Password' =>  'a3S%Lm#8Xi7Y',
      );

      $login_details = new SoapHeader($ns,'LoginDetails', $service_auth ,false);  

      $wsa = 'http://www.w3.org/2005/08/addressing';

      $Action = new SoapHeader($wsa,'Action', 'http://ns.ahpra.gov.au/pie/svc/frs/FindRegistration/2.0.0/FindRegistrationPortType/FindRegistrationsRequest' ,false);  

      $To = new SoapHeader($wsa,'To', 'https://wstest.ahpra.gov.au/pie_int/svc/PractitionerRegistrationSearch/2.0.0/FindRegistrationService.svc' ,false);  


      $headers_Arr = array('To'=>$To, 'Action'=>$Action, $login_details);

      $client->__setSoapHeaders($headers_Arr);

      $paramaters = array('ProfessionNumber'=> $args['profession_number']);

      $result = $client->__soapCall('FindRegistrations', array($paramaters));

      echo '------------result--------------------';

      echo "Request :<br>", htmlentities($client->__getLastRequest()), "<br>";

      echo '------------result--------------------';

      echo "Response:\n" . $client->__getLastResponse() . "\n";


    } catch (SoapFault $e) {
      echo  $e->getMessage();
    }


	}

  public function get_registrations(){

    if($_GET['profession_number']){
      $args['profession_number'] = $_GET['profession_number'];  
    }

    $this->load->library('WebService');

        $webservice_config = array(
            'server' => 'test'
        );

        $WebService = new WebService($webservice_config);
        //NMW0001450867


        $account_info = array(
            'ProfessionNumber' => $args['profession_number']
        );

        // var_dump($account_info);

        $WebService->find_registrations_test($account_info);

        var_dump($WebService->messagestatus);

    
  }
}
