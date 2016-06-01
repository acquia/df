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

# Rename zurb-foundation to zurb_foundation (d.o. packaging does not allow this via make)
mv profiles/df/themes/contrib/zurb-foundation profiles/df/themes/contrib/zurb_foundation

# Install the profile.
if [ ! -r sites/default/settings.php ]; then
  echo "No DB connection for docroot: $TARGET. Ensure settings.php is configured before installation."
else
  drush si df --account-name=admin --account-pass=presales -y
  # Enable optional scenario.
  if [ ! -z "$SCENARIO" ]; then
    drush es $SCENARIO
  fi
fi

# Create the drushrc.php file and symlink to a writeable location
cd sites/default
if [ ! -r ./drushrc.php ]; then
  ln -s ./files/.drushrc ./drushrc.php
fi

cd -
