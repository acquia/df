#!/usr/bin/env bash

# Define the color scheme.
FG_C='\033[1;37m'
BG_C='\033[42m'
WBG_C='\033[43m'
EBG_C='\033[41m'
NO_C='\033[0m'

# Use: Update DF Lock
#
# Example: ./update-lock.sh 

COMPOSER="$(which composer)"

read -n 1 -p "This will remove vendor and docroot directories. Do you wish to continue? (y/n) " answer
if echo "$answer" | grep -iq "^y" ;then
    echo -e "\nGreat!"
else
    echo -e "\nBye!"
    exit
fi

echo "Updating lock file for df"

echo "Removing old directories"
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} composer nuke && rm -rf composer.lock"
$COMPOSER nuke
rm -rf composer.lock

echo "Ensuring composer is up to date and caches are empty"
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} $COMPOSER clear-cache"
$COMPOSER clear-cache

echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} $COMPOSER self-update"
$COMPOSER self-update

echo "Running composer install"
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} $COMPOSER install"
$COMPOSER install

echo "lock file is generated!"
