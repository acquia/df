api = 2
core = 8.x

; Drupal Core
projects[drupal][type] = "core"
projects[drupal][version] = "8.2.x-dev"
projects[drupal][download][type] = "git"
projects[drupal][download][branch] = "8.2.x"

; Add authentication support for Views
; https://www.drupal.org/node/2228141
projects[drupal][patch][2228141] = "http://drupal.org/files/issues/add_authentication-2228141-54.patch"

; Make MigratePluginManager::getDefinitions() work cleanly
; https://www.drupal.org/node/2700693
projects[drupal][patch][2703669] = "https://www.drupal.org/files/issues/make-2700693-4.patch"

; Quickedit cant edit images
; https://www.drupal.org/node/2635712
projects[drupal][patch][2635712] = "http://drupal.org/files/issues/2635712-14.patch"

; Can we test RefreshLess with simplytest.me?
; https://www.drupal.org/node/2695717
projects[drupal][patch][2695717] = "https://www.drupal.org/files/issues/refreshless-alpha3-core-patch-2695717-7.patch"

; Provide a workaroud to load additional profiles via settings.php
; https://www.drupal.org/node/2743197
projects[drupal][patch][2743197] = "https://www.drupal.org/files/issues/2743197-load_additional_profiles_via_settings-2.patch"
