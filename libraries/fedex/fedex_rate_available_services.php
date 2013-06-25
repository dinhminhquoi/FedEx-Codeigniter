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
 * FedEx Rate Available Services Class
 *
 * Get rates for available services
 */

class Fedex_rate_availabe_services extends Fedex_driver
{
	private $addressLines = array();
	private $city;
	private $state;
	private $zip;
	private $company;
	private $streetAccuracy = 'medium';
	private $directionalAccuracy = 'loose';
	private $companyNameAccuracy = 'loose';
	private $path_to_wsdl = "application/libraries/fedex/wsdl/RateService_v13.wsdl"; 
	
	public function getRates()
	{
		$request = $this->buildRequest();
		$this->sendRequest($request);
	}
	
	public function buildRequest()
	{
		$request['WebAuthenticationDetail'] = array('UserCredential' =>
        	array('Key' => $this->API_KEY, 'Password' => $this->API_PASSWORD));
		$request['ClientDetail'] = array('AccountNumber' => $this->API_ACCOUNT, 'MeterNumber' => $this->API_METER);
		$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Available Services Request v13 using PHP ***');
		$request['Version'] = array('ServiceId' => 'crs', 'Major' => '13', 'Intermediate' => '0', 'Minor' => '0');
		$request['ReturnTransitAndCommit'] = true;
		$request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COU		RIER, ...
		$request['RequestedShipment']['ShipTimestamp'] = date('c');
		// Service Type and Packaging Type are not passed in the request
		$request['RequestedShipment']['Shipper'] = array('Address'=>getProperty('address1'));
		$request['RequestedShipment']['Recipient'] = array('Address'=>getProperty('address2'));
		
		$request['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER',
                                                        		'Payor' => array(
																	'ResponsibleParty' => array(
																		'AccountNumber' => getProperty('billaccount'		),
																		'Contact' => null,
																		'Address' => array('CountryCode' => 'US'))))		;
																		
		$request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT'; 
		$request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
		$request['RequestedShipment']['PackageCount'] = '2';
		$request['RequestedShipment']['RequestedPackageLineItems'] = array(
			'0' => array(
				'SequenceNumber' => 1,
				'GroupPackageCount' => 1,
				'Weight' => array('Value' => 2.0,
    			'Units' => 'LB'),
    			'Dimensions' => array('Length' => 10,
       			'Width' => 10,
       			'Height' => 3,
       			'Units' => 'IN')),
			'1' => array(
				'SequenceNumber' => 2,
				'GroupPackageCount' => 1,
    			'Weight' => array(
    				'Value' => 5.0,
        			'Units' => 'LB'),
     			'Dimensions' => array('Length' => 20,
        			'Width' => 20,
        			'Height' => 10,
     				'Units' => 'IN')));
		return $request;
	}
	
	public function sendRequest($request)
	{
		
	}

}