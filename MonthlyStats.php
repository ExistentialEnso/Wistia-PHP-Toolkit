<?php
/**
* Wistia PHP Class Library - MonthlyStats Class
*
* Class to represent a month of stats for an APIEntity. 
* 
* @author Thorne N. Melcher <tmelcher@portdusk.com>
* @copyright Copyright 2012, Thorne N. Melcher
* @license LGPL v3 (see LICENSE.txt)
* @package Wistia-API-Toolkit
* @version 2.0-b1
*
* @TODO Functions to easily display range (e.g. "01/01/2012-01/31/2012"
*/

namespace wistia;

/**
 * MonthlyStats class definition.
 *
 * @package Wistia
 */
class MonthlyStats extends Stats {
  /**
  * The month (in number form) that these stats are for.
  */
  protected $month;
  
  /**
  * The year that these stats are for.
  */
  protected $year;

  /**
  * Adds a day's of stat values to this object. This is meant for building this object only. The Wistia API only provides 
  * information per day or total, so we must calculate monthly stats ourselves.
  */
  public function addDay($daily_stats) {
    $this->load_count += $daily_stats->getLoadCount();
    $this->play_count += $daily_stats->getPlayCount();
    $this->hours_watched += $daily_stats->getHoursWatched();
  }
  
  /**
  * Gets the month (in number form) that these stats are for.
  *
  * @return integer The month number.
  */
  public function getMonth() {
    return (int) $this->month;
  }
  
  /**
  * Gets the month (in text form) that these stats are for.
  *
  * @return string The name.
  */
  public function getMonthName() {
    return date("F", mktime(0, 0, 0, $this->month, 10))
  }
  
  
  /**
  * Gets the year that these stats are for.
  *
  * @return int The year.
  */
  public function getYear() {
    return (int) $this->year;
  }
  
  /**
  * Sets the month (in int form) that these stats are for.
  *
  * @param int The month number.
  */
  public function setMonth($month) {
    $this->month = (int) $month;
  }
  
  /**
  * Sets the year that these stats are for.
  *
  * @param int The year.
  */
  public function setYear($year) {
    $this->year = (int) $year;
  }
}