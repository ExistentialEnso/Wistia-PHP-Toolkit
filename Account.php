<?php
/**
* Wistia PHP Class Library - Account Class
*
* Class to represent a connection to Wistia's API for a particular account. Must pass an API key when constructed. 
* Some functions return objects of other types in this library (such as WistiaProject objects or WistiaMedia objects).
* 
* @author Thorne N. Melcher <tmelcher@portdusk.com>
* @copyright Copyright 2012, Thorne N. Melcher
* @license LGPL v3 (see LICENSE.txt)
* @package Wistia-API-Toolkit
* @version 2.0-b1
*/

namespace wistia;

/**
 * Account class definition.
 *
 * @package Wistia
 */
class Account extends APIEntity {
  /**
   * The API key used for authenticating to the API
   * 
   * @var string
   */
  protected $key;
  
  /**
   * The numeric ID of this Wistia account.
   * 
   * @var integer
   */
  protected $id;
  
  /**
   * The name of this account.
   * 
   * @var string
   */
  protected $name;
  
  /**
   * This account's main Wistia URL
   * 
   * @var string
   */
  protected $url;
  
  /**
   * Constructor function. Username assumed to be "api" as is standard for all accounts.
   * 
   * @param string $key Your API key/password.
   * @param stdObj $data Data to populate fields with.
   */
  public function __construct($key, $data = null) {
    parent::__construct($data);
    
    $this->key = $key;
    
    if($data == null) {
      $this->_loadFields();
    }
  }
  
  /**
   * Master, generic API call function that processes all calls to the API using cURL.
   * 
   * @param string $file Location of call. Is appended to Wistia base URL (https://api.wistia.com/v1/).
   * @param string $method HTTP method to use (e.g. GET, POST, PUT). Defaults to GET.
   * @param array $params Parameters to pass with the call. Array of key=>value pairs.
   * @return stdClass The information returned by the API call.
   */
  public function call($file, $method="GET", $params=null) {
    $url = 'https://api.wistia.com/v1/' . $file;
  
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERPWD, 'api:' . $this->key);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    
    // If not a GET request, set the CUSTOMREQUEST cURL field
    if($method != "GET") curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    
    // Add params (if necessary)
    if($params != null) {
      // POST requests go in the POSTFIELDS field, otherwise just add a query string to our URL.
      if($method == "POST") curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
      else $url . "?" . http_build_query($params);
    }
    
    curl_setopt($curl, CURLOPT_URL, $url);
    
    $response = curl_exec($curl);
    
    return json_decode($response);
  }
  
  /**
  * Creates a WistiaProject with a given name and saves it to your Wistia account.
  *
  * @param string $name The project's name.
  * @return wistia\Project The newly created project.
  */
  public function createProject($name) {
    $params = array("name"=>$name);
    $response = $this->call("projects.json", "POST", $params);
    
    $project = new Project($this, $response);
    
    return $project;
  }
  
  /**
   * Gets the numeric ID of this account.
   * 
   * @return integer The ID.
   */
  public function getId() {
    return $this->id;
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
   * Gets the name of this account.
   * 
   * @return string The name.
   */
  public function getName() {
    return $this->name;
  }
  
  /**
   * Fetches a WistiaProject populated with information from the API.
   * 
   * @param string $publicid The publicId (different from the regular id) of the project.
   * @return wistia\Project The requested project.
   */
  public function getProject($publicid) {
    $response = $this->call("projects/".$publicid.".json");
    
    $project = new Project($this, $response);
    
    return $project;
  }
  
  /**
   * Gets all of the projects associated with this API account.
   * 
   * @return array<wistia\Project> Array of project objects.
   */
  public function getProjects($recursive=false) {
    $response = $this->call("projects.json");
    $projects = array();
    
    // Turn each block of data into a Project item for our array
    foreach($response as $obj) {
      $p = new Project($this, $obj);
      
      // This will force the lazy loading of the media to kick in, querying the API for data
      if($recursive) $p->getMedias();
      
      array_push($projects, $p);
    }
    
    return $projects;
  }
  
  /**
   * Gets a WistiaMedia object created from API data based on an ID value.
   * 
   * @param string $id The ID of the media.
   * @return wistia\Media The created object.
   */
  public function getMedia($id) {
    $response = $this->call("medias/".$id.".json");
    
    $media = new Media($this, $response);
    
    return $media;
  }
  
  public function getMedias() {
  
  }

  /**
   * Gets the all-time stats for this account.
   * 
   * @return Stats The stats in objective form.
   */
  public function getStats() {
    $response = $this->call("stats/account.json");
    
    $stats = new Stats($response);
    
    return $stats;
  }
  
  /**
   * Gets the stats for one day for this account. Can be DateTime object, string, or UNIX timestamp int.
   * 
   * @param DateTime/string/int $date
   * @return \wistia\DailyStats The stats in objective form.
   */
  public function getDailyStats($date) {
    if(is_object($date)) $date = $date->format("Y-m-d"); //if a DateTime object is provided
    if($date != intval($date)) strtotime($date); //convert to timestamp if necessary
    $date = date("Y-m-d", $date); //create a properly-formatted date for the API call
  
    $response = $this->call("stats/account/by_date.json", "GET", array("start_date"=>$date, "end_date"=>$date));
    
    $stats = new DailyStats($response[0]);
    
    return $stats;
  }
  
  public function getMonthlyStats($month, $year) {
    $lastDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
  
    $response = $this->call("stats/account/by_date.json", "GET", array("start_date"=>$year."-".$month."-01", "end_date"=>$year."-".$month."-".$lastDay));
  
    $stats = new MonthlyStats();
    $stats->setMonth($month);
    $stats->setYear($year);
    
    foreach($response as $r) {
      $stats->addDay(new DailyStats($r));
    }
    
    return $stats;
  }
  
  /**
   * Gets the main account URL associated with this account.
   */
  public function getURL() {
    return $this->url;  
  }
  
  public function _loadFields() {
    $response = $this->call("account.json");
    $this->_loadData($response);
  }
}
