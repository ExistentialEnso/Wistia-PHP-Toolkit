<?php
/**
* Wistia PHP Class Library - API Class
*
* @author Thorne N. Melcher <tmelcher@portdusk.com>
* @copyright Copyright 2012, Thorne N. Melcher
* @license LGPL v3 (see LICENSE.txt)
*
* Class to represent medias hosted on Wistia. Medias cannot be created using the API and must be made through WistiaProject's getUploaderCode() function, 
* which allows you to embed an uploader for that project (and all medias "belong" to a project).
*/
class WistiaMedia {
	protected $id;
	protected $name;
	protected $description;
	protected $duration;
	protected $embedCode = "";
	protected $type;
	protected $created;
	protected $updated;
	protected $hashed_id;
	
	protected $api; //WistiaAPI object

	/**
	* Constructor function. Optional second parameter lets you pre-populate the object with information.
	*/
	public function __construct($api, $data=null) {
		$this->api = $api;
	
		if($data!=null) {
			$this->_loadData($data);
		}
	}
	
	/**
	 * Gets the date/time that the Media was first created (i.e. uploaded to Wistia.)
	 */
	public function getCreated() {
		return $this->created;
	}
	
	/**
	 * Gets the description associated with the Media.
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	* Returns the duration of the media (in seconds).
	*/
	public function getDuration() {
		return $this->duration;
	}
	
	/**
	* Gets the code to embed the media on a page.
	*/
	public function getEmbedCode() {
		// WistiaMedia objects loaded secondarily through loading a project won't have all data.
		if($this->id != "" && $this->embedCode == "") {			
			$response = $this->api->call("medias/".$this->id.".json");
			$this->_loadData($response);
		}
		
		return $this->embedCode;
	}
	
	/**
	 * Gets the Media's hashed ID, used for iframe embeds and the JavaScript API.
	 */
	public function getHashedId() {
		return $this->hashed_id;
	}
	
	/**
	 * Gets the media's ID, used for calls to the data API.
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Gets the media's name (default: filename at upload).
	 */
	public function getName() {
		return $this->name;	
	}
	
	/**
	 * Gets the media's type ("Video", "Image", "Audio", "Swf", "MicrosoftOfficeDocument", "PdfDocument", or "UnknownType").
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * Gets the date/time that the media was last updated.
	 */
	public function getUpdated() {
		return $this->updated;	
	}
	
	/**
	 * Changes the description of this Media.
	 * 
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}
	
	/**
	 * Changes the name of this Media. Is set to file name at upload by default.
	 * 
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}
	
	/**
	 * Private function that processes stdClass data into a WistiaProject object.
	 * 
	 * @param stdClass $data
	 */
	private function _loadData($data) {
		foreach($data as $key => $value) {
			if(property_exists("WistiaMedia", $key)) {
				$this->$key = $value;
			}
		}
	}
}