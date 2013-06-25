<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * CI-FedEx Library
 *
 * Copyright (c) 2013-2014 Robert Evans
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * FedEx Address Validation Class
 *
 * Handles address validation
 */

class Fedex_address_validation extends Fedex_driver
{
	private $addressLines = array();
	private $city;
	private $state;
	private $zip;
	private $company;
	private $streetAccuracy = 'medium';
	private $directionalAccuracy = 'loose';
	private $companyNameAccuracy = 'loose';
	private $path_to_wsdl = "application/libraries/fedex/wsdl/AddressValidationService_v2.wsdl"; 
	
	public function setAddressLine($addressLine)
	{
		$this->addressLines[] = $addressLine;
	}
	
	public function setCity($city)
	{
		$this->city = $city;
	}
	
	public function setState($state)
	{
		$this->state = $state;
	}
	
	public function setZip($zip)
	{
		$this->zip = $zip;
	}
	
	public function setCompanyName($company)
	{
		$this->company = $company;
	}
	
	public function setStreetAccuracy($accuracy)
	{
		$this->streetAccuracy = $accuracy;
	}
	
	public function setDirectionalAccuracy($accuracy)
	{
		$this->directionalAccuracy = $accuracy;
	}
	
	public function setCompanyNameAccuracy($accuracy)
	{
		$this->companyNameAccuracy = $accuracy;
	}
	
	public function validateAddress()
	{
		$request = $this->buildRequest();
		//echo "<pre>";
		//print_r($request);
		//echo "</pre>";
		//die();
		$this->sendRequest($request);
	}
	
	public function buildRequest()
	{
		$request['WebAuthenticationDetail'] = array('UserCredential' =>
        	array('Key' => $this->API_KEY, 'Password' => $this->API_PASSWORD));
		$request['ClientDetail'] = array('AccountNumber' => $this->API_ACCOUNT, 'MeterNumber' => $this->API_METER);
		$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Address Validation Request v2 using PHP ***');
		$request['Version'] = array('ServiceId' => 'aval', 'Major' => '2', 'Intermediate' => '0', 'Minor' => '0');
		$request['RequestTimestamp'] = date('c');
		$request['Options'] = array('CheckResidentialStatus' => 1,
                             'MaximumNumberOfMatches' => 5,
                             'StreetAccuracy' => $this->streetAccuracy,
                             'DirectionalAccuracy' => $this->directionalAccuracy,
                             'CompanyNameAccuracy' => $this->companyNameAccuracy,
                             'ConvertToUpperCase' => 1,
                             'RecognizeAlternateCityNames' => 1,
                             'ReturnParsedElements' => 1);
		$request['AddressesToValidate'] = array(0 => array('AddressId' => 'WTC',
                                           'Address' => array('StreetLines' => $this->addressLines,
                                                                              'PostalCode' => $this->zip,
																			  'City' => $this->city,
																			  'StateorProvinceCode' => $this->state,
                                                                              'CompanyName' => $this->company)));
		return $request;
	}
	
	public function sendRequest($request)
	{
		ini_set("soap.wsdl_cache_enabled", "0");

		$client = new SoapClient($this->path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
		
		try 
		{
		    $response = $client ->addressValidation($request);
		
		    if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR')
		    {
		        foreach($response -> AddressResults as $addressResult){
		        	echo 'Address Id: ' . $addressResult->AddressId . Newline;
		        	echo 'Residential Status: ' . $addressResult->ProposedAddressDetails->ResidentialStatus . Newline;
		        	echo 'Delivery Point Address: ' . $addressResult->ProposedAddressDetails->DeliveryPointValidation . Newline;
		        	echo 'Proposed Address:' . Newline;
		        	foreach($addressResult->ProposedAddressDetails->Address as $addressKey => $addressValue){
		        		echo '&nbsp;&nbsp;' . $addressValue . Newline;
		        	}
		        	echo Newline;
		        }
    		}
    		else
		    {		
		        echo "Something Went Wrong";		
				echo "<pre>";
				print_r($request);
				echo "</pre>";
				echo "<hr />";
				echo "<pre>";
				print_r($response);
				echo "</pre>";
		    } 

		} catch (SoapFault $exception) {
    		echo "<pre>";
			print_r($exception);
			echo "</pre>";
		}
	}
}