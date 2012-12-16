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
  * The number of times a page with video embed was loaded.
  *
  * @var integer
  */
  protected $load_count;
  
  /**
   * The number of video plays.
   * 
   * @var integer
   */
  protected $play_count;
  
  /**
   * The number of hours visitors spent video-watching.
   */
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
  
  /**
   * Gets the number of video plays.
   * 
   * @return integer Play count.
   */
  public function getPlayCount() {
    return $this->play_count;
  }
  
  /**
   * Sets the number of video plays.
   * 
   * @param $play_count integer
   */
  public function setPlayCount($play_count) {
    $this->play_count = $play_count;
  }
  
  /**
   * Gets the number of hours visitors spent video-watching.
   * 
   * @return float Number of hours.
   */
  public function getHoursWatched() {
    return $this->hours_watched;
  }
  
  /**
   * Sets the number of hours visitors spent video-watching.
   * 
   * @param float $hours_watched
   */
  public function setHoursWatched($hours_watched) {
    $this->hours_watched = $hours_watched;
  }
}
