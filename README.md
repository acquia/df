# Demo Framework (DF)
[![Travis build status](https://api.travis-ci.org/acquia/df.svg?branch=8.x-2.x)](https://travis-ci.org/acquia/df) [![Scrutinizer code quality](https://scrutinizer-ci.com/g/acquia/df/badges/quality-score.png?b=8.x-2.x)](https://scrutinizer-ci.com/g/acquia/df)

Demo Framework is a distribution consisting of modules, themes and libraries. It highlights powerful features created by the Drupal community. It is intended to be used as a starterkit for promoting enterprise-ready solutions.

Demo Framework is powered by [Lightning](https://www.drupal.org/project/lightning).

## Installing Demo Framework

The preferred way to install Demo Framework is using our [Composer-based project template][template]. It's easy!

If you don't want to use Composer, you can install Demo Framework the traditional way by downloading a tarball from our [drupal.org project page](https://www.drupal.org/project/df).

Before installing Demo Framework via drupal.org or after building it from scratch using Drush Make, you must download a small number of PHP libraries that cannot currently be packaged automatically due to limitations with both drupal.org and Drush.

  ``composer require "commerceguys/intl: ~0.7" "commerceguys/addressing: ~1.0" "commerceguys/zone: ~1.0" "embed/embed: ~2.2"``

A build script is also provided that wraps the composer install command and moves everything into a target directory as well.

  ``./build.sh ~/Destination``

You can add in commands for composer here. We suggest using the --no-dev option unless you want to run behat and have DF manage your version of Drush used on the site.

  ``./build.sh ~/Destination --no-dev``

At this point, you will need to prepare your settings.php file just as you would for a normal Drupal install.

We recommend Acquia Dev Desktop running ``PHP 5_6`` and using the ``Import local Drupal site...`` function.

Now use the ``site-install`` command to install Drupal with the DF installation profile.

  ``drush si df``

Enable a DF Scenario using the ``enable-scenario`` command.

  ``drush es dfs_tec``

If everything worked correctly, you should see console output that some migrations ran.

You may now login to your site.

  ``drush uli -l http://mysite.dd``

You may also reset the content of a DF Scenario if it is enabled.

  ``drush rs dfs_tec``
  
### Using the Zurb Foundation Sub Theme

To motify the CSS/JS you must use the scss files. You will find various different SCSS files in SCSS directory that root. There are specifc ones for the theme in base & layout. All the variables are set in _settings.scss, you will also be able to override variables there.

To compile scss you will need a few things installed on your machine:
- NPM [IF you need to install](http://blog.npmjs.org/post/85484771375/how-to-install-npm)
- Bower ``npm install -g bower``

Then you will need to run:
- ``npm install``
- ``bower install``

if you need to update the vendor js, I added in some gulp files that make that easy.
- ``gulp copy`` will copy the bower_component files for zurb and motion ui
- ``gulp concat`` will concatenate all the files into a single vendor.all.js file and put it in your js/ folder where its already being called by drupal

Once that is installed, start the gulp file which will watch for scss changes:
- ``npm start``

## Running Tests
These instructions assume you have used Composer to install Lightning. Once you
have it up and running, follow these steps to execute all of Lightning's Behat
tests:

### Behat
    $ cd MYPROJECT
    $ ./bin/drupal behat:init http://YOUR.DF.SITE --merge=../docroot/profiles/df/tests/behat.yml
    $ ./bin/drupal behat:include ../docroot/profiles/df/tests/features --with-subcontexts=../docroot/profiles/df/tests/features/bootstrap --with-subcontexts=../docroot/profiles/df/src/DFExtension/Context
    $ ./bin/behat --config ./docroot/sites/default/files/behat.yml

If necessary, you can edit ```docroot/sites/default/files/behat.yml``` to match
your environment, but generally you will not need to do this.

## Resources

Please file issues in our [drupal.org issue queue][issue_queue].

[issue_queue]: https://www.drupal.org/project/issues/df "Demo Framework Issue Queue"
[template]: https://github.com/acquia/df-project "Composer-based project template"
[d.o_semver]: https://www.drupal.org/node/1612910
[df_composer_project]: https://github.com/acquia/df-project
