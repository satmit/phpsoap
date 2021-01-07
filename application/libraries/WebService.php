<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class WebService {

    private $proxy;
    public $request_status;
    public $result = array();
    private $request_ip;

    protected $user_id = 'piews_test@alexion.com';
    protected $password = 'a3S%Lm#8Xi7Y';
    //forexmart webservice

    private $config_keys_allowed = array(
        'url','user_id','password'        
    );

    protected $url;

    protected $server_url = array(

        'test' =>'https://wstest.ahpra.gov.au/pie_int/svc/PractitionerRegistrationSearch/2.0.0/FindRegistrationService.svc?singleWsdl',   
        'uri' =>'https://wstest.ahpra.gov.au/pie_int/svc/PractitionerRegistrationSearch/2.0.0/FindRegistrationService.svc',   

        'live' =>'https://wstest.ahpra.gov.au/pie_int/svc/PractitionerRegistrationSearch/2.0.0/FindRegistrationService.svc?singleWsdl',   
        'live_uri' =>'https://wstest.ahpra.gov.au/pie_int/svc/PractitionerRegistrationSearch/2.0.0/FindRegistrationService.svc',

    );

    public function __construct( $config = array() ){ //WebService
        $ci =& get_instance();
        $this->request_ip = $ci->input->ip_address();
        self::initialize($config);
    }

    /**
     * Initialize WebService with given $config
     */

    protected function initialize( $config ){

        try {

            if( array_key_exists('server', $config) ){
                if( array_key_exists($config['server'], $this->server_url) ){
                    $this->url = $this->server_url[$config['server']];
                }else{
                   $this->url = $this->server_url['test']; 
                }
                                            
            }

            $this->proxy = new SoapClient($this->url, 
                array(
                    'location' => $this->server_url['uri'] ,
                    'uri' => $this->server_url['uri'],
                    'soap_version'   => SOAP_1_2,
                    'trace' => 1,
                    'exceptions'=> true ,
                    // 'features' => SOAP_SINGLE_ELEMENT_ARRAYS 
                    // + SOAP_USE_XSI_ARRAY_TYPE
                )
            );

            return true;

        }catch (SoapFault $e) {
            return array(
                'SOAPError' => true,
                'Message' => $e->getMessage(),
                'LogId' => self::Exception($e,'GetProxy',null)
            );
        }

    }

    public function find_registrations_test( $account_info = array() ){

        $eData = array();

        try {
            
            $login_details = $this->login_details();      


            $NS_ADDR = 'http://www.w3.org/2005/08/addressing';
            $to = new SoapHeader($NS_ADDR, 'To', 'https://wstest.ahpra.gov.au/pie_int/svc/PractitionerRegistrationSearch/2.0.0/FindRegistrationService.svc', false);

            $NS_ADDR = 'http://www.w3.org/2005/08/addressing';
            $Action = new SoapHeader($NS_ADDR, 'Action', 'http://ns.ahpra.gov.au/pie/svc/frs/FindRegistration/2.0.0/FindRegistrationPortType/FindRegistrationsRequest', false);

            $headerbody = array('To' => $to ,'Action' => $Action, $login_details  );

            $this->proxy->__setSoapHeaders($headerbody);


            $parameters = array('ProfessionNumber'=>$account_info['ProfessionNumber']);
            
            $result = $this->proxy->__soapCall('FindRegistrations', array($parameters));


            $this->messagestatus =  $result;
            
            // $this->data = $result;

            // $this->messagecount = $result->AuditDetails->ServiceMessagesCount;


            // if($this->messagecount){
            //     $this->messagestatus = $result->ProfessionNumberReplay->ServiceMessage->Status;
            // }else{
            //     $this->messagestatus = $result->ProfessionNumberReplay->ServiceMessage->Status;
            // }
           
            
        } catch (SoapFault $e)  {

            return array(
                'SOAPError' => true,
                'Message' => $e->getMessage(),
                'LogId' => self::Exception($e,'FindRegistrations',$eData)
            );
        }


    }

    public function find_registrations( $account_info = array() ){

        $eData = array();

        try {
            
            $login_details = $this->login_details();      


            $NS_ADDR = 'http://www.w3.org/2005/08/addressing';
            $to = new SoapHeader($NS_ADDR, 'To', 'https://wstest.ahpra.gov.au/pie_int/svc/PractitionerRegistrationSearch/2.0.0/FindRegistrationService.svc', false);

            $NS_ADDR = 'http://www.w3.org/2005/08/addressing';
            $Action = new SoapHeader($NS_ADDR, 'Action', 'http://ns.ahpra.gov.au/pie/svc/frs/FindRegistration/2.0.0/FindRegistrationPortType/FindRegistrationsRequest', false);

            $headerbody = array('To' => $to ,'Action' => $Action, $login_details  );

            $this->proxy->__setSoapHeaders($headerbody);


            $parameters = array('ProfessionNumber'=>$account_info['ProfessionNumber']);
            
            $result = $this->proxy->__soapCall('FindRegistrations', array($parameters));


            $this->messagecount = $result->AuditDetails->ServiceMessagesCount;

            if($this->messagecount){
                $this->messagestatus = $result->ProfessionNumberReplay->ServiceMessage->Status;
            }else{
                $this->messagestatus = $result->ProfessionNumberReplay->ServiceMessage->Status;
            }
            $this->specialmessage = 'test';
            
        } catch (SoapFault $e)  {

            $this->specialmessage = 'soap fault';
            $this->messagecount  = 1;
            $this->messagestatus = $e->getMessage();
            
            return array(
                'SOAPError' => true,
                'Message' => $e->getMessage(),
                'LogId' => self::Exception($e,'FindRegistrations',$eData)
            );

        }


    }

    /* Login Credentials */

    private function login_details(){

        $NS_ADDR ='http://ns.ahpra.gov.au/pie/xsd/common/CommonCoreElements/2.0.0';

        $service_auth = array(
            'UserId' =>  $this->user_id,
            'Password' =>  $this->password,
        );

        $login_details = new SoapHeader($NS_ADDR,'LoginDetails', $service_auth ,false);  

        return $login_details;

    }

    /**
     * WebService converting object to array data type.
     */

    protected function get_array_object($object){
        $arrayObject = new ArrayObject($object);
        return $arrayObject->getArrayCopy();
    }

    public function __destruct(){
        // unset($this);
    }

    public static function Exception($e, $subject, $eData) {
        //error logging
    }
}