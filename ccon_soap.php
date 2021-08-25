<?php
class ccon_soap{
    public $params;
    public $proc;

    function __construct($hSoap,$lSoap,$pSoap){
        $this->hSoap = $hSoap;
        $this->lSoap = $lSoap;
        $this->pSoap = $pSoap;
    }

    function soap_blina($params,$proc){
        $result='';
        $this->params = $params;
        $this->proc = $proc;
        $client = new \SoapClient($this->hSoap,array('login' => $this->lSoap,'password' => $this->pSoap,'soap_version' => SOAP_1_2,'encoding' => 'UTF-8','trace' => true,));
        try{
            $result = $client->__soapCall($this->proc, array($this->params));
        }
        catch(Exception $e){
            echo($client->__getLastResponse());
            echo PHP_EOL;
            echo($client->__getLastRequest());
        }
        return $result;
    }
}
