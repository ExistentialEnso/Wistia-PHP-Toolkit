<?php
/**
* Wistia PHP Class Library - APIEntity Class
*
* Base API entity class that represents behavior common to all API-derived objects.
* 
* @author Thorne N. Melcher <tmelcher@portdusk.com>
* @copyright Copyright 2012, Thorne N. Melcher
* @license LGPL v3 (see LICENSE.txt)
* @package Wistia-API-Toolkit
* @version 2.0-b1
*/

namespace wistia;

class APIEntity {
  public function __construct($data=null) {
    if($data != null) {
			$this->_loadData($data);
		}
  }

  /**
  * Generic data loading function. Account's call() method converts the API's JSON response into a PHP stdObject. This 
  * can then process that into fields on the "real" version of the entitys' class.
  */
  protected function _loadData($data) {
		foreach($data as $key => $value) {
			if(property_exists(get_class($this), $key)) {
				$this->$key = $value;
			}
		}
	}
}