<?php 

namespace HundraArtonHundra;

class HundraArtonHundra
{
	public static $paging;
	public static $apiKey;
	public static $endpoint = 'http://developer.118100.se:8080/openapi-1.1/appetizing?query=';
	protected $queryString;

	/**
     * Generate url for the request
     * 
     * @param $query (personal number)
     * @return url and token
     */
	public function buildUrl( $query )
	{
		try
		{
			if( !empty( $query ) )
			{
				$this->queryString = HundraArtonHundra::$endpoint . urlencode( $query ) . '&pageSize='. HundraArtonHundra::$paging .'&key='. HundraArtonHundra::$apiKey .'';
			}
			else
			{
				throw new ApiException( 'Invalid querystring.');  
			}
		}
		catch( ApiException $e )  
		{  
			echo $e->getMessage();
		} 

	}


	/**
     * Get the response from HundraArtonHundra
     * 
     * @return response
     */
	public function response()
	{
		$curl = curl_init();

		curl_setopt_array( $curl, array(
		    CURLOPT_RETURNTRANSFER  => 1,
		    CURLOPT_URL 			=> $this->queryString
		));

		$response = curl_exec( $curl );

		curl_close( $curl );
		
		return $response;		
	}


	/**
     * Search by personal number
     * 
     * @param $query (personal number)
     * @return personaldata
     */
	public function search( $query )
	{
		try
		{
			if( $query )
			{
				$this->buildUrl( $query );
				
				$xmlResponse = simplexml_load_string( $this->response() );
				$personData = array();

				foreach( $xmlResponse as $data ) 
				{
					foreach( $data->personHits as $data ) 
					{
						foreach( $data->person as $person ) 
						{
							$personData[] = (object) array(
							    'name' => (object) array(
							    	'first'	=> (string) $person->individual->name[0],
							    	'last'  => (string) $person->individual->name[1],
							    ),
								'address' => (object) array(
									'name'		=> current( $person->address->street->name ),
									'number'	=> current( $person->address->street->number ),
									'suffix'	=> current( $person->address->street->suffix ),
									'zip'		=> current( $person->address->zip ),
									'city'		=> current( $person->address->city ),
									'country'	=> current( $person->address->country ),
								),
								'phone'	=> (object) array(
									'prefix'   => current( $person->phoneNumber->internationalPrefix ),
									'code' 	   => current( $person->phoneNumber->areaCode ),
									'local'    => current( $person->phoneNumber->localNumber ),
									'number'   => current( $person->phoneNumber->internationalPrefix ) . 
												  current( $person->phoneNumber->areaCode ) . 
												  current( $person->phoneNumber->localNumber ),
								),
							);
						}
					}
				}

				return (object) $personData;
			}
			else
			{
				throw new ApiException( 'No query added.');  
			}
		}
		catch( ApiException $e )  
		{  
			echo $e->getMessage();
		} 
	}
}

// Empty Exception class
class ApiException extends \Exception{};
