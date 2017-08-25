<?php

namespace Acquia\DF\Composer;

use Acquia\DF\IniEncoder;
use Composer\Package\PackageInterface;
use Composer\Script\Event;
use Composer\Util\ProcessExecutor;
use Symfony\Component\Yaml\Yaml;

/**
 * Generates Drush make files for drupal.org's ancient packaging system.
 */
class Package {

  /**
   * Script entry point.
   *
   * @param \Composer\Script\Event $event
   *   The script event.
   */
  public static function execute(Event $event) {
    $composer = $event->getComposer();
    $encoder = new IniEncoder();

    // Convert the lock file to a make file using Drush's make-convert command.
    $bin_dir = $composer->getConfig()->get('bin-dir');
    $make = NULL;
    $executor = new ProcessExecutor();
    $executor->execute($bin_dir . '/drush make-convert composer.lock', $make);
    $make = Yaml::parse($make);

    // Include any drupal-library packages in the make file.
    $libraries = $composer
      ->getRepositoryManager()
      ->getLocalRepository()
      ->getPackages();

    // Retrieve a list of all libraries.
    $libraries = array_filter($libraries, function (PackageInterface $package) {
      return in_array($package->getType(), array('drupal-library', 'bower-asset', 'npm-asset'));
    });

    // Build a whitelist of libraries which are approved for distribution via
    // drupal.org.
    // Composer supports adding libraries from anywhere, while drupal.org only
    // allows distributions to include libraries that have been specifically
    // approved. See the Packaging Whitelist for a list of approved libraries
    // (https://www.drupal.org/packaging-whitelist).
    $whitelist = ['dropzone', 'masonry', 'imagesloaded', 'ckeditor-track-changes', 'slick', 'lightbox2', 'cropper'];

    foreach ($libraries as $library) {
      // Libraries definitions converted from composer.lock are in the form of
      // 'vendor/library-name'. The vendor name must be removed in order to
      // support drush make.
      $old_key = $library->getName();

      // Drush make-convert doesn't currently include dependencies of libraries
      // that are automatically detected and downloaded by Composer via
      // asset-packagist in $make. However, these packages *are* detected by
      // Composer and included in $libraries.
      // Since we don't care about or want to process dependencies, we check to
      // see if they are in $make and otherwise ignore them.
      if (isset($make['libraries'][$old_key])) {
        $new_key = basename($old_key);
        $make['libraries'][$new_key] = $make['libraries'][$old_key];
        unset($make['libraries'][$old_key]);

        // Libraries must be located in the root 'libraries' folder.
        $make['libraries'][$new_key]['destination'] = "../../libraries";

        // Remove any libraries that are not specifically whitelisted above.
        // Users will need to download these libraries manually.
        if (!in_array($new_key, $whitelist)) {
          unset($make['libraries'][$new_key]);
        }
      }
    }

    // The doctrine/inflector library is included with Drupal core and does not
    // need to be added to the make file. It is pinned to a specific version for
    // Composer-based workflows in order to support PHP < 7 which is still used
    // in the development, testing and deployment of DF.
    if (isset($make['libraries']['doctrine/inflector'])) {
      unset($make['libraries']['doctrine/inflector']);
    }

    // The ckeditor-track-changes library is designed to be renamed, after
    // download, to 'lite'.
    if (isset($make['libraries']['ckeditor-track-changes'])) {
      $make['libraries']['ckeditor-track-changes']['directory_name'] = 'lite';
    }

    // The slick library is designed to be renamed, after download, to
    // 'slick-carousel'.
    if (isset($make['libraries']['slick'])) {
      $make['libraries']['slick']['directory_name'] = 'slick-carousel';
    }

    // The Zurb Foundation theme had its shortname changed from
    // 'zurb-foundation' to 'zurb_foundation' on drupal.org. Unfortunately,
    // drupal.org requires the old shortname to be used when retrieving the
    // theme using drush make.
    if (isset($make['projects']['zurb_foundation'])) {
      $make['projects']['zurb-foundation'] = $make['projects']['zurb_foundation'];
      unset($make['projects']['zurb_foundation']);
    }

    // Set default settings for projects and libraries.
    $make['defaults']['projects']['subdir'] = 'contrib';

    if (isset($make['projects']['drupal'])) {
      // Always use drupal.org's core repository, or patches will not apply.
      $make['projects']['drupal']['download']['url'] = 'https://git.drupal.org/project/drupal.git';

      $core = [
        'api' => 2,
        'core' => '8.x',
        'projects' => [
          'drupal' => [
            'type' => 'core',
            'version' => $make['projects']['drupal']['download']['tag'],
          ],
        ],
      ];

      if (isset($make['projects']['drupal']['patch'])) {
        $core['projects']['drupal']['patch'] = $make['projects']['drupal']['patch'];
      }

      file_put_contents('drupal-org-core.make', $encoder->encode($core));
      unset($make['projects']['drupal']);
    }

    // The Lightning profile is a unique project that has a number of
    // non-standard properties such as its library name, version number and
    // project type. These need to be altered or set manually.
    if (isset($make['libraries']['acquia/lightning'])) {
      // The Lightning profile is automatically detected as a library during
      // conversion from composer.lock and receives the wrong name.
      // Copy the library into a project which uses the correct name.
      $make['projects']['lightning'] = $make['libraries']['acquia/lightning'];

      // Identify the project as a 'profile'.
      $make['projects']['lightning']['type'] = 'profile';

      // Override the default 'contrib' subdirectory set above in order to
      // prevent the profile from being placed in a 'contrib' folder.
      $make['projects']['lightning']['subdir'] = '""';

      // Fix the download tag which is incorrectly set to the download branch.
      $make['projects']['lightning']['download']['tag'] = $make['projects']['lightning']['download']['branch'];
      unset($make['projects']['lightning']['download']['branch']);

      // Remove the 'acquia/lightning' library.
      unset($make['libraries']['acquia/lightning']);
    }

    foreach ($make['projects'] as $key => &$project) {
      if ($project['download']['type'] == 'git') {
        if (isset($project['download']['tag'])) {
          $tag = $project['download']['tag'];

          // The Lightning project uses semantic versioning.
          if ($key == 'lightning') {
            $sem_ver = explode('.', $tag);

            list($major, $minor, $patch) = $sem_ver;

            $tag = "$major.$minor$patch";
          }
          else {
            preg_match('/\d+\.x-\d+\.0/', $tag, $match);
            $tag = str_replace($match, str_replace('x-', NULL, $match), $tag);
            preg_match('/\d+\.\d+\.0/', $tag, $match);
            $tag = str_replace($match, substr($match[0], 0, -2), $tag);
          }

          // Set the project version to the altered tag and remove the download
          // information.
          $project['version'] = $tag;
          unset($project['download']);
        }
      }
    }

    file_put_contents('drupal-org.make', $encoder->encode($make));
  }

}
