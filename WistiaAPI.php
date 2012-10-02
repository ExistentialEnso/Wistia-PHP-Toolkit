<?php
/**
* Wistia PHP Class Library - API Class
*
* @author Thorne N. Melcher <existentialenso@gmail.com>
* @copyright Copyright 2012, Thorne N. Melcher
* @license LGPL v3 (see LICENSE.txt)
*
* Class to represent a connection to Wistia's API. Must pass an API key when constructed. Some functions return objects of
* other types in this library (such as WistiaProject objects or WistiaMedia objects).
*/

include "WistiaMedia.php";
include "WistiaProject.php";

class WistiaAPI {
	private $key;
	
	/**
	 * Constructor function. Username assumed to be "api" as is standard for all accounts.
	 * 
	 * @param string $key Your API key/password.
	 */
	public function __construct($key) {
		$this->key = $key;
	}
	
	/**
	* Creates a WistiaProject with a given name and saves it to your Wistia account.
	*/
	public function createProject($name) {
		$params = array("name"=>$name);
		$curl = curl_init('https://api.wistia.com/v1/projects.json');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);                         
		curl_setopt($curl, CURLOPT_USERPWD, 'api:' . $this->key);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);                    
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);                          
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_POSTFIELDS,http_build_query($params));
		$response = curl_exec($curl);
		
		$project = new WistiaProject($this, json_decode($response));
		
		return $project;
	}
	
	/**
	* Returns the API key associated with this instance.
	*/
	public function getKey() {
		return $this->key;
	}
	
	/**
	 * Fetches a WistiaProject populated with information from the API.
	 * 
	 * @param string $publicid The publicId (different from the regular id) of the project.
	 * @return WistiaProject The requested project.
	 */
	public function getProject($publicid) {
		$curl = curl_init('https://api.wistia.com/v1/projects/'.$publicid.'.json');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);                         
		curl_setopt($curl, CURLOPT_USERPWD, 'api:' . $this->key);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);                    
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);                          
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		$response = curl_exec($curl);
		
		$project = new WistiaProject($this, json_decode($response));
		
		return $project;
	}
	
	/**
	 * Gets all of the projects associated with this API account.
	 * 
	 * @return array Array of WistiaProject objects.
	 */
	public function getProjects() {
		$curl = curl_init('https://api.wistia.com/v1/projects.json');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERPWD, 'api:' . $this->key);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		$response = curl_exec($curl);
		
		$response = json_decode($response);
		$projects = array();
		
		foreach($response as $obj) {
			$p = new WistiaProject($this, $obj);
			array_push($projects, $p);
		}
		
		return $projects;
	}
	
	/**
	 * Gets a WistiaMedia object created from API data.
	 * 
	 * @param string $id The ID of the media.
	 * @return WistiaMedia The created object.
	 */
	public function getMedia($id) {
		$curl = curl_init('https://api.wistia.com/v1/medias/'.$id.'.json');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERPWD, 'api:' . $this->key);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		$response = curl_exec($curl);
		
		$media = new WistiaMedia($this, json_decode($response));
		
		return $media;
	}
}