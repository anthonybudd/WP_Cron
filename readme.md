# WP_Cron

<p align="center"><img src="https://ideea.co.uk/static/wp_cron.png"></p>

### A better API into the WordPress Cron system.
The WordPress con API is very verbose and equaly as unuserfrienly. WP_Cron is a simple class that you can extend to

```php
Class DoThisEveryHour extends WP_Cron{

    public $interval = [
        'seconds'   => 60,
        'minutes'   => 29,
        'hours'     => .5,

        'weeks'     => 0,
        'months'    => 0,
    ];
    
    public function handle(){
        update_option('Last_WP_Cron', date('Y-m-d H:i:s'));
    }
}

DoThisEveryHour::register();
```
