#!/bin/bash
# @file
# Common functionality for df distribution.

#
# Ensures that the right drush version is installed.
#
function df_ensure_drush() {
	# This function is re-entrant.
	if [ -r "$TRAVIS_BUILD_DIR/../drupal_ti-df-drush-installed" ]
	then
		return
	fi

	drupal_ti_ensure_drush

	# Download addon.
	drush dl -y drupalorg_drush-7.x-1.x-dev --destination=$HOME/.drush
	drush cc drush

	touch "$TRAVIS_BUILD_DIR/../drupal_ti-df-drush-installed"
}

#
# Ensures that the distribution is linked into the Drupal code base.
# @todo Move to upstream.
#
function df_ensure_distribution() {
	# Ensure we are in the right directory.
	mkdir -p "$DRUPAL_TI_DRUPAL_BASE"
	cd "$DRUPAL_TI_DRUPAL_BASE"

	# This function is re-entrant.
	if [ -L "$DRUPAL_TI_DISTRIBUTION_NAME" ]
	then
		return
	fi

	# Find absolute path to module.
	MODULE_DIR=$(cd "$TRAVIS_BUILD_DIR"; pwd)

	# Point distribution into the drupal installation directory.
	ln -sf "$MODULE_DIR" "$DRUPAL_TI_DISTRIBUTION_NAME"
}

#
# Ensures that the distribution is build.
#
function df_build_distribution() {
    # Ensure we are in the right directory.
    cd "$DRUPAL_TI_DRUPAL_BASE"

    # Build Codebase
    mkdir -p drupal/profiles
    mv df drupal/profiles/df
    git clone --branch 8.x-1.x https://github.com/acquia/lightning.git drupal/profiles/lightning

    # Build the current branch.
    df_header Building Demo Framework from current branch
    cd drupal
    drush make --yes profiles/df/drupal-org-core.make --prepare-install
    drush make --yes profiles/df/drupal-org.make --no-core --contrib-destination=profiles/df
    drush make --yes profiles/lightning/drupal-org.make --no-core --contrib-destination=profiles/lightning

    df_header Prepare files directories
    mkdir -p sites/default/private/files
    mkdir -p sites/default/private/temp

    # Install third-party libraries.
    df_header Installing third-party libraries
    php "profiles/df/modules/contrib/composer_manager/scripts/init.php"
    composer drupal-rebuild
    composer update -n --verbose

    # Verify that all the .make files will work on Drupal.org but ignore errors.
    df_header Verifying .make file
    drush verify-makefile profiles/df/drupal-org.make || true
}

#
# Overwrite functions/drupal.sh install function.
# @todo Upstream tries to check for drupal dir existing, which fails
#				this. Use a file instead.
#
function drupal_ti_ensure_drupal() {
	# This function is re-entrant.
	if [ -r "$TRAVIS_BUILD_DIR/../drupal_ti-drupal-installed" ]
	then
		return
	fi

	# HHVM env is broken: https://github.com/travis-ci/travis-ci/issues/2523.
	PHP_VERSION=`phpenv version-name`
	if [ "$PHP_VERSION" = "hhvm" ]
	then
		# Create sendmail command, which links to /bin/true for HHVM.
		BIN_DIR="$TRAVIS_BUILD_DIR/../drupal_travis/bin"
		mkdir -p "$BIN_DIR"
		ln -s $(which true) "$BIN_DIR/sendmail"
		export PATH="$BIN_DIR:$PATH"
	fi

	# Create database and install Drupal.
	mysql -e "create database $DRUPAL_TI_DB"

	mkdir -p "$DRUPAL_TI_DRUPAL_BASE"
	cd "$DRUPAL_TI_DRUPAL_BASE"

	drupal_ti_install_drupal
	touch "$TRAVIS_BUILD_DIR/../drupal_ti-drupal-installed"
}

#
# Overwrite environments/drupal-8.sh install function
#
function drupal_ti_install_drupal() {
	# Do the site install.
	df_header Installing Drupal
	cd drupal
	php -d sendmail_path=$(which true) ~/.composer/vendor/bin/drush.php --yes site-install "$DRUPAL_TI_INSTALL_PROFILE" --db-url="$DRUPAL_TI_DB_URL" --account-name=admin --account-pass=admin --site-mail=admin@example.com --site-name="Demo Framework"
	drush config-set -y system.file path.private "sites/default/private/files"
	drush config-set -y system.file path.temporary "sites/default/private/temp"

	# If a scenario has been specified, enable it.
  if [[ "$SCENARIO" != none ]]
  then
    df_header Enabling scenario
    drush es $SCENARIO
  fi

	# Switch to the Demo Framework platform built from Git (if we aren't there already).
	cd ../drupal
}

#
# Use some optimized settings to make tests faster.
#
function df_optimize() {
	# Optimize OpCache
	if [[ $TRAVIS_PHP_VERSION > "5.4" ]]; then
		echo "opcache.memory_consumption=128" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
	fi

	# Enable APC
	if [[ $TRAVIS_PHP_VERSION < "5.5" ]]; then
		echo "extension=apc.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
		echo "apc.shm_size=256M" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
	fi

	# Increase the MySQL connection timeout on the PHP end.
	echo "mysql.connect_timeout=3000" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
	echo "default_socket_timeout=3000" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini

	# Increase the MySQL server timetout and packet size.
	mysql -e "SET GLOBAL wait_timeout = 36000;"
	mysql -e "SET GLOBAL max_allowed_packet = 33554432;"
}

#
# Prints a message about the section of the script.
#
function df_header() {
	echo
	echo "** $@"
	echo
}
