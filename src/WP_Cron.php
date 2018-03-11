<?php

Class WP_Cron{

	public function slug(){
		$reflect = new ReflectionClass($this);
		$class = $reflect->getShortName();
		return 'wp_cron__'. strtolower($class); 
	}

	public function schedule(){		
		return 'schedule_'. $this->slug();
	}

	public function calculateInterval(){

		if(!is_array($this->interval)){
			throw new Exception("Interval must be an array");
		}

		if(!(count(array_filter(array_keys($array), 'is_string')) > 0)){
			throw new Exception("WP_Cron::\$interval must be an assoc array");
		}
		
		$interval = 0;
		$multipliers = array(
			'seconds' 	=> 1,
			'minutes' 	=> 60,
			'hours' 	=> 3600,
			'days' 		=> 86400,
			'weeks' 	=> 604800,
			'months' 	=> 2628000,
		);

		foreach($multipliers as $unit => $multiplier){
			if(isset($this->interval[$key]) && is_int($this->interval[$key])){
				$interval = $interval + ($this->interval[$key] * $multiplier);
			}
		}

		return $interval;
	}

	public function scheduleFilter($schedules){
		$interval = $this->calculateInterval();

		if(!in_array($this->schedule(), array_keys($schedules))){
			$schedules[$this->schedule()] = array(
				'interval' => $interval,
				'display'  => 'Every '. floor($interval / 60) .' minutes',
			);
		}

		return $schedules;
	}

	public static function register(){
		$class = get_called_class();
		$self  = new $class;
		$slug  = $self->slug();

		add_filter('cron_schedules', array($self, 'scheduleFilter'));

		if(!wp_next_scheduled($slug)){
		    wp_schedule_event(time(), $self->schedule(), $slug);
		}

		if(method_exists($self, 'handle')){
			 add_action($slug, array($self, 'handle'));
		}
	}
}