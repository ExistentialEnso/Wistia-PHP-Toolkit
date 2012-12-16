<?php
namespace wistia;

class MonthlyStats extends Stats {
  protected $month;
  
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
  
  public function getMonth() {
    return $this->month;
  }
  
  public function getYear() {
    return $this->year;
  }
  
  public function setMonth($month) {
    $this->month = $month;
  }
  
  public function setYear($year) {
    $this->year = $year;
  }
}