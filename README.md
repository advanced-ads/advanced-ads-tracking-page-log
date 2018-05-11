This plugin is meant to help with debugging Advanced Ads Tracking 
by adding page impressions to the debug file.

It only makes sense if you are using Advanced Ads and Advanced Ads Tracking for managing ads.

See

https://wpadvancedads.com/add-ons/tracking
https://wpadvancedads.com/manual/tracking-issues/

## How to enable it?

Set the constant `ADVANCED_ADS_TRACKING_DEBUG` in `wp-config.php` like this:

```
define( 'ADVANCED_ADS_TRACKING_DEBUG', true ); // or set to a specific ad ID to limit logging to a specific ad only
```

When the Tracking add-on and this add-on are enabled, information about ad and page impressions and clicks are being tracked in `wp-content/advanced-ads-tracking.csv`.

### impression tracking methods

The plugin can track page impressions using JavaScript (AJAX, bot safe) or/and PHP.

The default method is JavaScript.

Define `ADVANCED_ADS_TRACKING_DEBUG_METHOD` with the value `ALL` or `PHP` to change the method used.

Depending on the method, you will see the tracked page impressions marked with 
either `pageviewJS` or `pageviewPHP` in the log file.