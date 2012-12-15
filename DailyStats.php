<?php
/**
* Wistia stats can either be all-time or for a specific day. This handles behavior specific to stats that only represent
* a single day's worth of information.
*
* @author Thorne N. Melcher <tmelcher@portdusk.com>
* @copyright Copyright 2012, Thorne N. Melcher
* @license LGPL v3 (see LICENSE.txt)
* @package Wistia-API-Toolkit
* @version 2.0-b1
*/

namespace wistia;

class DailyStats extends Stats {
  protected $date;

  public function getDate() {
    return $this->date;
  }
  
  public function setDate($date) {
    $this->date = $date;
  }
}