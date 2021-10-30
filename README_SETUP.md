# Setup Instructions

### Place the `site` folder in the location of the website.
In general, the contents of `site` should be located in `public_html`, so that the site resides in the webserver root.

### Place `hiddenfiles` on the webserver in a private location.
It is recommended to palce `hiddenfiles` in the folder directly above `public_html`.

### Add the `hiddenfiles` folder to PHP's `include_path`

It is necessary to set the PHP `include_path` variable to include the path to hiddenfiles, so that all php scripts can easily access include/require these. Do this by adding the following line to the `public_html/.htaccess` file:
```
php_value include_path ".:/thepathto/hiddenfiles/"
```

### Place the SQL database loginfiles in the `hiddenfiles/sql_users` directory.

### Configure the cronjobs as specified in `hiddenfiles/cronjobs/cronjobs_overview.txt`

### Configure appropriate `administratie/.htaccess` and `administratie/.htpasswd` files for the Administration pages
The Administration / CMS pages need password protection from the public. Ideally, configure your .htaccess file so that the CMS area of the website is additionally only available from your home country using GeoIP.

### Configure `hiddenfiles/website_configuration.php` with your website-specific details.
