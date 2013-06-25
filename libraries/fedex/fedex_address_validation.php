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
	
	
	public function setAddressLine($addressLine)
	{
		$this->addressLines[] = $addressLine;
	}
	
	public function setCity($city)
	{
	}
	
	public function setState($state)
	{
	}
	
	public function setZip($zip)
	{
	}
	
	public function validateAddress()
	{
	}
	
	public function buildRequest()
	{
		$request['WebAuthenticationDetail'] = array('UserCredential' =>
        	array('Key' => SELF::API_KEY, 'Password' => SELF::API_PASSWORD));
		$request['ClientDetail'] = array('AccountNumber' => SELF::API_ACCOUNT, 'MeterNumber' => SELF::API_METER);
		$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Address Validation Request v2 using PHP ***');
		$request['Version'] = array('ServiceId' => 'aval', 'Major' => '2', 'Intermediate' => '0', 'Minor' => '0');
		$request['RequestTimestamp'] = date('c');
		
		return $request;
	}
}