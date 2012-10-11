<?php
/**
* Wistia PHP Class Library - API Class
*
* @author Thorne N. Melcher <tmelcher@portdusk.com>
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
	 * Master, generic API call function that processes all calls to the API.
	 * 
	 * @param string $file Location of call. Is appended to Wistia base URL (https://api.wistia.com/v1/).
	 * @param string $method HTTP method to use (e.g. GET, POST, PUT). Defaults to GET.
	 * @param array $params Parameters to pass with the call. Array of key=>value pairs.
	 */
	public function call($file, $method="GET", $params=null) {
		$curl = curl_init('https://api.wistia.com/v1/' . $file);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERPWD, 'api:' . $this->key);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		
		if($method != "GET") curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		if($params != null) curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
		
		$response = curl_exec($curl);
		
		return json_decode($response);
	}
	
	/**
	* Creates a WistiaProject with a given name and saves it to your Wistia account.
	*
	* @param string $name The project's name.
	* @return WistiaProject The newly created project.
	*/
	public function createProject($name) {
		$params = array("name"=>$name);
		$response = $this->call("projects.json", "POST", $params);
		
		$project = new WistiaProject($this, $response);
		
		return $project;
	}
	
	/**
	* Returns the API key associated with this instance.
	*
	* @return string The API key.
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
		$response = $this->call("projects/".$publicid.".json");
		
		$project = new WistiaProject($this, $response);
		
		return $project;
	}
	
	/**
	 * Gets all of the projects associated with this API account.
	 * 
	 * @return array Array of WistiaProject objects.
	 */
	public function getProjects() {
		$response = $this->call("projects.json");
		$projects = array();
		
		foreach($response as $obj) {
			$p = new WistiaProject($this, $obj);
			array_push($projects, $p);
		}
		
		return $projects;
	}
	
	/**
	 * Gets a WistiaMedia object created from API data based on an ID value.
	 * 
	 * @param string $id The ID of the media.
	 * @return WistiaMedia The created object.
	 */
	public function getMedia($id) {
		$response = $this->call("medias/".$id.".json");
		
		$media = new WistiaMedia($this, $response);
		
		return $media;
	}
}