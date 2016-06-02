# Geocoder 8.x-2.x

This is the Geocoder module for Drupal 8 rewritten using the Geocoder PHP library.

# Requirements
* [Composer](https://getcomposer.org/)
* [Drush](http://drush.org)
* [Geophp module](https://drupal.org/project/geophp) >= 8.x-1.x
* [Geofield](https://drupal.org/project/geofield) >= 8.x-1.x

# Installation
* Install the contrib module [Composer Manager](https://www.drupal.org/project/composer_manager).
* Read the installation documentation of Composer Manager. (See [[#2405811]](https://www.drupal.org/node/2405811))
* Run ```composer drupal-rebuild``` then ```composer drupal-update``` on the root of your site.
* Enable the module: ```drush en geocoder```
* Enable the submodules ```geocoder_geofield``` or ```geocoder_field```.

# Features
* Provides a widget to geocode a field from a string into a geographic data format such as WKT, GeoJson, etc etc...
* The Provider and Dumper plugins are extendable through a custom module,
* All successful requests are cached by default.

# API

## Get a list of available Provider plugins

```php
\Drupal\geocoder\Geocoder::getPlugins('provider')
```

## Get a list of available Dumper plugins

```php
\Drupal\geocoder\Geocoder::getPlugins('dumper')
```

## Geocode a string

```php
$plugins = array('geonames', 'googlemaps', 'bingmaps');
$address = '1600 Amphitheatre Parkway Mountain View, CA 94043';
$options = array(
  'geonames' => array(), // array of options
  'googlemaps' => array(), // array of options
  'bingmaps' => array(), // array of options
);

$addressCollection = \Drupal\geocoder\Geocoder::geocode($plugins, $address, $options);
// or
$addressCollection = geocode($plugins, $address, $options);
```

## Reverse geocode coordinates

```php
$plugins = array('freegeoip', 'geonames', 'googlemaps', 'bingmaps');
$address = '1600 Amphitheatre Parkway Mountain View, CA 94043';
$options = array(
  'freegeoip' => array(), // array of options
  'geonames' => array(), // array of options
  'googlemaps' => array(), // array of options
  'bingmaps' => array(), // array of options
);

$addressCollection = \Drupal\geocoder\Geocoder::reverse($plugins, $address, $options);
// or
$addressCollection = reverse($plugins, $address, $options);
```

## Return format

Both ```Geocoder::geocode()``` and ```Geocoder::reverse()``` and both ```reverse()``` and ```geocode()``` returns the same object: ```Geocoder\Model\AddressCollection```, which is itself composed of ```Geocoder\Model\Address```.

You can transform those objects into arrays. Example:

```php
$plugins = array('geonames', 'googlemaps', 'bingmaps');
$address = '1600 Amphitheatre Parkway Mountain View, CA 94043';
$options = array(
  'geonames' => array(), // array of options
  'googlemaps' => array(), // array of options
  'bingmaps' => array(), // array of options
);

$addressCollection = \Drupal\geocoder\Geocoder::geocode($plugins, $address, $options);
$address_array = $addressCollection->first()->toArray();

// You can play a bit more with the API

$addressCollection = \Drupal\geocoder\Geocoder::geocode($plugins, $address, $options);
$latitude = $addressCollection->first()->getCoordinates()->getLatitude();
$longitude = $addressCollection->first()->getCoordinates()->getLongitude();
```

You can also convert these to different formats using the Dumper plugins.
Get the list of available Dumper by doing:

```php
\Drupal\geocoder\Geocoder::getPlugins('dumper')
```

Here's an example on how to use a Dumper

```php
$plugins = array('geonames', 'googlemaps', 'bingmaps');
$address = '1600 Amphitheatre Parkway Mountain View, CA 94043';

$addressCollection = \Drupal\geocoder\Geocoder::geocode($plugins, $address);
$geojson = \Drupal\geocoder\Geocoder::getPlugin('dumper', 'geojson')->dump($addressCollection->first());
```

There's also a dumper for GeoPHP, here's how to use it

```php
$plugins = array('geonames', 'googlemaps', 'bingmaps');
$address = '1600 Amphitheatre Parkway Mountain View, CA 94043';

$addressCollection = \Drupal\geocoder\Geocoder::geocode($plugins, $address);
$geometry = \Drupal\geocoder\Geocoder::getPlugin('dumper', 'geometry')->dump($addressCollection->first());
```

# Links
* [Composer](https://getcomposer.org/)
* [Drush](http://drush.org)
* [Geocoder PHP library](http://geocoder-php.org/)
* [Geocoder module](https://www.drupal.org/project/geocoder)
