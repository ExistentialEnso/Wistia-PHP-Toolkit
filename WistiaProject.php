<?php
/**
* Wistia PHP Class Library - API Class
*
* @author Thorne N. Melcher <existentialenso@gmail.com>
* @copyright Copyright 2012, Thorne N. Melcher
*
* Class to represent Wistia Projects, the primary way an account's videos are usually organized. Every video must belong to a project, though, at this
* time, there isn't a way to upload videos directly through the API. However, ->getUploaderCode() can be used to produce embeddable HTML/JavaScript that
* can be used to add an upload button to your site. However, $anonymousCanUpload must be true for this to work (since this doesn't use any sort of
* authentication.
*/

class WistiaProject {
	protected $publicId = null;
	protected $name;
	protected $mediaCount;
	protected $anonymousCanUpload;
	
	protected $videos = array();
	
	protected $api; //WistiaAPI object
	
	/**
	* Can pass a stdObject or array of data, and it will traverse it attempting to parse it into Wistia objects.
	*/
	public function __construct($api, $data=null) {
		$this->api = $api;
	
		if($data != null) {
			foreach($data as $key => $value) {
				if($key == "medias") {
					//"medias" field is itself an array of video file-related information
					foreach($value as $v) {
						$video = new WistiaVideo($api, $v);
						
						array_push($this->videos, $video);
					}
				}
			
				if(property_exists("WistiaProject", $key)) {
					$this->$key = $value;
				}
			}
		}
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
	
	/*
	* Returns an array of WistiaVideo objects associated with the Project.
	*/
	public function getVideos() {
		return $this->videos;
	}
	
	/*
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
	* Set whether or not anonymous users can upload videos to this project. This must be enabled for the embeddable uploader to work. Note that ->save()
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