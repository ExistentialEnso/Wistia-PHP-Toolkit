<?php
/**
* Wistia PHP Class Library - API Class
*
* Class to represent medias hosted on Wistia. Medias cannot be created using the API and must be made through WistiaProject's getUploaderCode() function, 
* which allows you to embed an uploader for that project (and all medias "belong" to a project).
* 
* @author Thorne N. Melcher <tmelcher@portdusk.com>
* @copyright Copyright 2012, Thorne N. Melcher
* @license LGPL v3 (see LICENSE.txt)
* @package Wistia-API-Toolkit
* @version 2.0-b1
*/

namespace wistia;

/**
 * Media class definition.
 *
 * @package Wistia
 */
class Media extends APIEntity {
  /**
   * The unique ID of this media.
   * 
   * @var integer
   */
  protected $id;
  
  /**
   * The display name of this media. Defaults to the file name at upload.
   * 
   * @var string
   */
  protected $name;
  
  /**
   * A description of this media.
   * 
   * @var string
   */
  protected $description;
  
  /**
   * The duration of this media (in seconds or pages)
   * 
   * @var integer
   */
  protected $duration;
  
  /**
   * The embed code for this media.
   * 
   * @var string
   */
  protected $embedCode = "";
  
  /**
   * This media's type.
   * 
   * @var string
   */
  protected $type;
  
  /**
   * When this media was created.
   * 
   * @var string
   */
  protected $created;
  
  /**
   * When this media was last updated.
   * 
   * @var string
   */
  protected $updated;
  
  /**
   * This media's hashed ID value, used for iframes and the JS api.
   * 
   * @var string
   */
  protected $hashed_id;
  
  /**
   * The WistiaAccount object used to communicate with the API.
   * 
   * @var WistiaAccount
   */
  protected $account;

  /**
  * Constructor function. Optional second parameter lets you pre-populate the object with information.
  * 
  * @param WistiaAccount $account
  * @param stdClass $data
  */
  public function __construct($account, $data=null) {
    $this->account = $account;
  
    parent::__construct($data);
  }
  
  /**
  * Deletes the media from Wistia. Use with caution!
  */
  public function delete() {
    return $this->account->call("medias/".$this->hashed_id.".json", "DELETE");
  }
  
  /**
   * Gets the date/time that the Media was first created (i.e. uploaded to Wistia.)
   *
   * @return string When it was created.
   */
  public function getCreated() {
    return $this->created;
  }
  
  /**
   * Gets the description associated with the Media.
   *
   * @return string The description.
   */
  public function getDescription() {
    return $this->description;
  }
  
  /**
  * Returns the duration of the media (in seconds).
  *
  * @return int The duration.
  */
  public function getDuration() {
    return $this->duration;
  }
  
  /**
  * Gets the HTML code to embed the media on a page.
  *
  * @return string The HTML.
  */
  public function getEmbedCode() {
    // Media objects loaded secondarily through loading a project won't have all data.
    if($this->id != "" && $this->embedCode == "") {      
      $response = $this->api->call("medias/".$this->id.".json");
      $this->_loadData($response);
    }
    
    return $this->embedCode;
  }
  
  /**
   * Gets the Media's hashed ID, used for iframe embeds and the JavaScript API.
   *
   * @return string The hashed ID.
   */
  public function getHashedId() {
    return $this->hashed_id;
  }
  
  /**
   * Gets the media's ID, used for calls to the data API.
   *
   * @return int The ID.
   */
  public function getId() {
    return $this->id;
  }
  
  /**
   * Gets the media's name (default: filename at upload).
   *
   * @return string The name.
   */
  public function getName() {
    return $this->name;  
  }
  
  /**
   * Gets the media's type ("Video", "Image", "Audio", "Swf", "MicrosoftOfficeDocument", "PdfDocument", or "UnknownType").
   *
   * @return string The type.
   */
  public function getType() {
    return $this->type;
  }
  
  /**
   * Gets the date/time that the media was last updated.
   *
   * @return string The update time.
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
  
  public function getStats($start_date=null, $end_date=null) {
    $response = $this->call("medias/".$this->hashed_id.".json");
    
    $stats = new Stats($response);
    
    return $stats;
  }
}