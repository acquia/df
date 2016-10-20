<?php

/**
 * Implements hook_drush_command().
 */
function df_tools_image_drush_command() {
  $items = [];

  $items['df_seed_derivatives'] = array(
    'description' => "Create image derivatives",
    'aliases' => array('df-sd', 'seed_derivatives'),
    'options' => array(
      'styles' => array(
        'description' => 'A comma separated list of all image styles needing generation, leaving this blank will use all styles',
        'example_value' => 'small,medium,thumbnail',
      ),
      'exclude' => array(
        'description' => 'A comma separated list of all image styles to be excluded. Leaving this blank will use all styles from the "styles" parameter.',
        'example_value' => 'large',
      ),
      'dir' => array(
        'description' => 'Set to the file path (relative to your public files directory) under which all files will have an image derivative created. Alternatively, set to "public" to just have your public files directory looked into with no recursion. With no option set, the default will run through every single file.',
        'example_value' => 'field/image',
      ),
    ),
    'examples' => array(
      'drush seed_derivatives' => 'Creates image derivatives for every single image style for every file within the public file system.',
      'drush seed_derivatives --styles=large,medium --dir=field' => 'Creates large and medium image derivatives for every single image stored underneath the "field" directory in the public file directory.',
      'drush seed_derivatives --exclude=thumbnail --dir=public' => 'Creates image derivatives for every single image style except "thumbnail" for every file within the public file system directory but not in subdirectories.',
    ),
    'core' => array('8'),
  );

  return $items;
}

/**
 * Command callback to perform the file migration.
 *
 * The options for this script are styles, exclude and dir. The styles and exclude
 * options will explicitly set or prohibit image styles to be created. The default
 * is that all styles will be used.
 * The dir parameter may be used to specify paths under the public files directory
 * that processing may occur on. The default for this is all paths and the special
 * path "public" will only process the public files directory without recursion.
 */
function drush_df_tools_image_df_seed_derivatives() {
    $dir = drush_get_option('dir');
    $excludes = explode(",", drush_get_option('exclude'));
    $includes = explode(",", drush_get_option('styles'));
    df_tools_image_seed_derivatives($dir, $excludes, $includes);
}
