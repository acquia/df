#!/bin/sh

# Use: Run this file install Demo Framework and optionally automatically enable
# a scenario.
# Example: ./install.sh ../d8df dfs_fin

TARGET=$1
SCENARIO=$2

# Move into the target directory.
if [ ! -r $TARGET ]; then
  mkdir -p $TARGET
fi
cd $TARGET

# Setup Composer Manager and attempt to install dependencies.
php profiles/df/modules/contrib/composer_manager/scripts/init.php
composer drupal-update
composer dumpautoload

# Install the profile.
if [ ! -r sites/default/settings.php ]; then
  echo "No DB connection for docroot: $TARGET. Ensure settings.php is configured before installation."
else
  # Add profile discovery for Lightning.
  chmod 777 sites/default/settings.php
  cat profiles/df/sub-profile.txt >> sites/default/settings.php
  # Install Demo Framework.
  drush si df --account-name=admin --account-pass=presales -y
  # Enable optional scenario.
  if [ ! -z "$SCENARIO" ]; then
    drush es $SCENARIO
  fi
  # Clean our mess up.
  chmod 544 sites/default/settings.php
fi

# Create the drushrc.php file and symlink to a writeable location
cd sites/default
if [ ! -r ./drushrc.php ]; then
  ln -s ./files/.drushrc ./drushrc.php
fi

cd -
