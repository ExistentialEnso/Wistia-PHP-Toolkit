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
	
	protected $medias = array();
	
	protected $api; //WistiaAPI object
	
	/**
	* Can pass a stdObject or array of data, and it will traverse it attempting to parse it into Wistia objects.
	*/
	public function __construct($api, $data=null) {
		$this->api = $api;
	
		if($data != null) {
			foreach($data as $key => $value) {
				if($key == "medias") {
					//"medias" field is itself an array of media file-related information
					foreach($value as $o) {
						$m = new WistiaMedia($api, $o);
						
						array_push($this->medias, $m);
					}
				} else if(property_exists("WistiaProject", $key)) {
					$this->$key = $value;
				}
			}
		}
	}
	
	/**
	 * Gets the number of media files associated with this project.
	 */
	public function getMediaCount() {
		return $this->mediaCount;
	}
	
	/**
	 * Gets the name associated with this project.
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Gets the public id (the primary unique identifier in the API) of the project.
	 */
	public function getPublicId() {
		return $this->publicId;
	}

	/**
	* Generates and returns code for an upload button for the project. Displays an error message if 'anonymousCanUpload' is set to false.
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
	* Returns an array of WistiaMedia objects associated with the Project.
	*/
	public function getMedias() {
		return $this->medias;
	}
	
	/**
	* Saves changes to the project to Wistia's website.
	*/
	public function save() {
		if($this->publicId != null) {
			$params = array("anonymousCanUpload"=>((int)$this->anonymousCanUpload), "name"=>$this->name);
			$curl = curl_init('https://api.wistia.com/v1/projects/'.$this->publicId.'.json');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);                         
			curl_setopt($curl, CURLOPT_USERPWD, 'api:' . $this->api->getKey());
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);                    
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);                          
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($curl, CURLOPT_POSTFIELDS,http_build_query($params));
			$response = curl_exec($curl);
			
			return json_decode($response);
		}
	}
	
	/**
	* Set whether or not anonymous users can upload medias to this project. This must be enabled for the embeddable uploader to work. Note that ->save()
	* must be called for changes to take effect on Wistia's website.
	*/
	public function setAnonymousCanUpload($anon) {
		$this->anonymousCanUpload = (bool) $anon;
	}
	
	/**
	* Sets the name of the project. Note that ->save() must be called for changes to take ffect on Wistia's website.
	*/
	public function setName($name) {
		$this->name = $name;
	}
}