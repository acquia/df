#!/bin/sh

# Usage ./build.sh ~/Sites/demo.local --working-copy
MAKEFILE='build-df.make'
CALLPATH=`dirname $0`
TARGET=$1
shift

echo '      ____________________________'
echo '     _/\/\/\/\/\____/\/\/\/\/\/\_'
echo '    _/\/\____/\/\__/\/\_________'
echo '   _/\/\____/\/\__/\/\/\/\/\___'
echo '  _/\/\____/\/\__/\/\_________'
echo ' _/\/\/\/\/\____/\/\_________'
echo '____________________________'

if [ -d $TARGET ]; then
  yes "yes" | rm -rf $TARGET
fi

if [[ ! -z "$@" ]]; then
  echo "Running drush make with additional params: $@"
  drush make $CALLPATH/$MAKEFILE $TARGET $@
else
  drush make $CALLPATH/$MAKEFILE $TARGET --concurrency=5
fi

# Rename zurb-foundation to zurb_foundation (d.o. packaging does not allow this via make)
mv $TARGET/profiles/df/themes/contrib/zurb-foundation $TARGET/profiles/df/themes/contrib/zurb_foundation
