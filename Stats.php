<?php
/**
* Wistia PHP Class Library - Stats Class
*
* Class to represent stats about an API entity. Parent clas should be used for stats representing all-time data. One day
* stats can be represented with DailyStats, which includes a date field.
* 
* @author Thorne N. Melcher <tmelcher@portdusk.com>
* @copyright Copyright 2012, Thorne N. Melcher
* @license LGPL v3 (see LICENSE.txt)
* @package Wistia-API-Toolkit
* @version 2.0-b1
*/

namespace wistia;

class Stats extends APIEntity {
  /**
  * The number of times a page with the video embedded was loaded.
  *
  * @var integer
  */
  protected $load_count;
  
  protected $play_count;
  
  protected $hours_watched;
  
  public function __construct($data) {
    parent::__construct($data);
  }
  
  public function getLoadCount() {
    return $this->load_count;
  }
  
  public function setLoadCount($load_count) {
    $this->load_count = $load_count;
  }
  
  public function getPlayCount() {
    return $this->play_count;
  }
  
  public function setPlayCount($play_count) {
    $this->play_count = $play_count;
  }
  
  public function getHoursWatched() {
    return $this->hours_watched;
  }
  
  public function setHoursWatched($hours_watched) {
    $this->hours_watched = $hours_watched;
  }
}