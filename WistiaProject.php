<?php
/**
* Wistia PHP Class Library - API Class
*
* @author Thorne N. Melcher <tmelcher@portdusk.com>
* @copyright Copyright 2012, Thorne N. Melcher
* @license LGPL v3 (see LICENSE.txt)
*
* Class to represent Wistia Projects, the primary way an account's medias are usually organized. Every media must belong to a project, though, at this
* time, there isn't a way to upload medias directly through the API. However, ->getUploaderCode() can be used to produce embeddable HTML/JavaScript that
* can be used to add an upload button to your site. However, $anonymousCanUpload must be true for this to work (since this doesn't use any sort of
* authentication.
*/

class WistiaProject {
	protected $publicId = null;
	protected $name;
	protected $mediaCount;
	protected $anonymousCanUpload;
	protected $anonymousCanDownload;
	
	protected $medias = array();
	
	protected $api; //WistiaAPI object
	
	/**
	* Can pass a stdObject or array of data, and it will traverse it attempting to parse it into Wistia objects.
	*
	* @param WistiaAPI $api
	* @param stdClass $data
	*/
	public function __construct($api, $data=null) {
		$this->api = $api;
	
		if($data != null) {
			$this->_loadData($data);
		}
	}
	
	/**
	 * Returns whether or not anonymous users can upload videos to this project. This must be true for the uploader to be used.
	 * 
	 * @return boolean Whether anonymous users can upload.
	 */
	public function anonymousCanUpload() {
		return (boolean) $this->anonymousCanUpload;
	}
	
	/**
	 * Returns whether or not anonymous users can download videos from this project.
	 * 
	 * @return boolean Whether anonymous users can download.
	 */
	public function anonymousCanDownload() {
		return (boolean) $this->anonymousCanDownload;
	}
	
	/**
	 * Gets the number of media files associated with this project.
	 * 
	 * @return integer The number of files.
	 */
	public function getMediaCount() {
		return (int) $this->mediaCount;
	}
	
	/**
	 * Returns an array of WistiaMedia objects associated with the Project.
	 *
	 * @return array The objects.
	 */
	public function getMedias() {
		if(count($this->medias) != $this->mediaCount) { //happens if Project comes from WistiaAPI->getProjects();
			$response = $this->api->call('projects/'.$this->publicId.'.json');
			
			$this->_loadData($response);
		}
		
		return $this->medias;
	}
	
	/**
	 * Gets the name associated with this project.
	 *
	 * @return string The name.
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Gets the public id (the primary unique identifier in the API) of the project.
	 *
	 * @return string The public id.
	 */
	public function getPublicId() {
		return $this->publicId;
	}

	/**
	* Generates and returns HTML code for an upload button for the project. Displays an error message if 'anonymousCanUpload' is set to false.
	*
	* @return string The HTML code.
	*/
	public function getUploaderCode() {
		ob_start();
		?>
		<div id="wistia-upload-widget" style="width: 500px; height: 75px;"></div>
		<script src="http://static.wistia.com/javascripts/upload_widget.js"></script>
		<script>
		var widget1 = new wistia.UploadWidget({ divId: 'wistia-upload-widget', publicProjectId: '<?php echo $this->publicId?>' });
		</script>
		<?php
		
		$code = ob_get_contents();
		ob_end_clean();
		return $code;
	}
	
	/**
	* Saves changes to the project to Wistia's website. WARNING: This currently only supports saving updates 
	* to existing projects. Use WistiaAPI->createProject() to create new projects.
	*
	* @return stdClass The API response.
	*/
	public function save() {
		if($this->publicId != null) {
			$params = array("anonymousCanUpload"=>((int)$this->anonymousCanUpload), "anonymousCanDownload"=>((int)$this->anonymousCanDownload), "name"=>$this->name);
			$response = $this->api->call('projects/'.$this->publicId.'.json', "PUT", $params);
			
			return $response;
		}
	}
	
	/**
	 * Set whether or not anonymous users can download medias from this project. Note that ->save() must be called for changes to take effect on Wistia's website.
	 * 
	 * @param boolean $anon
	 */
	public function setAnonymousCanDownload($anon) {
		$this->anonymousCanDownload = (bool) $anon;
	}
	
	/**
	* Set whether or not anonymous users can upload medias to this project. This must be enabled for the embeddable uploader to work. Note that ->save()
	* must be called for changes to take effect on Wistia's website.
	*
	* @param boolean $anon
	*/
	public function setAnonymousCanUpload($anon) {
		$this->anonymousCanUpload = (bool) $anon;
	}
	
	/**
	* Sets the name of the project. Note that ->save() must be called for changes to take effect on Wistia's website.
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
			if($key == "medias") {
				//"medias" field is itself an array of media file-related information
				foreach($value as $o) {
					$m = new WistiaMedia($this->api, $o);
		
					array_push($this->medias, $m);
				}
			} else if(property_exists("WistiaProject", $key)) {
				$this->$key = $value;
			}
		}
	}
}