#!/bin/bash
# @file
# Behat integration - Install step.

set -e $DRUPAL_TI_DEBUG

# Ensure drush webserver can be started for PHP 5.3 / hhvm.
drupal_ti_ensure_php_for_drush_webserver

# Ensure that drush and addons are installed.
df_header Installing Drush
export DRUPAL_TI_DRUSH_VERSION="drush/drush:8.1.2"
df_ensure_drush

# Ensure the distribution is linked into the code base.
# @todo Use drupal_ti_ensure_distribution once available.
df_ensure_distribution

# Build the distribution via drush make files.
df_build_distribution

# Install the behat extension.
df_header Installing Behat
cd "$DRUPAL_TI_DRUPAL_DIR/$DRUPAL_TI_BEHAT_DIR"
composer install --no-interaction --prefer-source --dev

# Disable sendmail
# @todo Move upstream.
echo sendmail_path=`which true` >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini

# Use some optimized PHP and mysql settings.
df_optimize
