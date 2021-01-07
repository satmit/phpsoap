<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}
	public function info()
	{
		phpinfo();
	}
	public function getapi(){
	

		try{

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


	    	$NS_ADDR ='http://ns.ahpra.gov.au/pie/xsd/common/CommonCoreElements/2.0.0';

	        $service_auth = array(
	            'UserId' =>  'piews_test@alexion.com',
	            'Password' =>  'a3S%Lm#8Xi7Y',
	        );

	        $login_details = new SoapHeader($NS_ADDR,'LoginDetails', $service_auth ,false);  

	        $NS_ADDR = 'http://www.w3.org/2005/08/addressing';
	        $to = new SoapHeader($NS_ADDR, 'To', 'https://wstest.ahpra.gov.au/pie_int/svc/PractitionerRegistrationSearch/2.0.0/FindRegistrationService.svc', false);

	        $NS_ADDR = 'http://www.w3.org/2005/08/addressing';

	        $Action = new SoapHeader($NS_ADDR, 'Action', 'http://ns.ahpra.gov.au/pie/svc/frs/FindRegistration/2.0.0/FindRegistrationPortType/FindRegistrationsRequest', false);

	        $headerbody = array('To' => $to ,'Action' => $Action, $login_details  );

	        $client->__setSoapHeaders($headerbody);


	        $parameters = array('ProfessionNumber'=>$args['profession_number']);
	        
	        $result = $client->__soapCall('FindRegistrations', array($parameters));


	        echo '------------result--------------------';

	   		echo "Request :<br>", htmlentities($client->__getLastRequest()), "<br>";

	        echo '------------result--------------------';


			echo "Response:\n" . $client->__getLastResponse() . "\n";


		} catch ( SoapFault $e ) { // Do NOT try and catch "Exception" here
		    
			echo  $e->getMessage();


			echo "RESPONSE:\n" . htmlentities(str_ireplace('><', ">\n<", $client->__getLastResponse())) . "\n";
			
			$response = strtr( $client->__getLastResponse(), ['</soap:' => '</', '<soap:' => '<']);
			$output = json_decode(json_encode(simplexml_load_string($response)));
			var_dump($output);


		}

	}

}
