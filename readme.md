# WP_Cron

<p align="center"><img src="https://ideea.co.uk/static/wp_cron.png"></p>

### Clean API into WordPress's Cron System
WP_Cron is a simple and easy to use class for defining cron events in WordPress. Define a class extending WP_Cron, set the frequency of the cron event using the $every property and then write the code you want to execute in the handle() method.


```php
<?php

Class UpdateLondonWeather extends WP_Cron{

    public $every = [
        'seconds'   => 60,
        'minutes'   => 59,
        'hours'     => 1,
    ];
    
    public function handle(){
        $response = file_get_contents('http://api.openweathermap.org/data/2.5/weather?id=2172797');
        $json     = json_decode($response);
        if($json === NULL && json_last_error() !== JSON_ERROR_NONE){
            return;
        }
        if(isset($json->weather[0]->description)){
            update_option('london_weather', $json->weather[0]->description);
        }
    }
}

UpdateLondonWeather::register();
```
