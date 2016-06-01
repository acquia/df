api = 2
core = 8.x

; Modules
projects[address][version] = "1.x-dev"
projects[address][type] = "module"
projects[address][subdir] = "contrib"
projects[address][download][type] = "git"
projects[address][download][branch] = "8.x-1.x"

projects[addtoany][type] = "module"
projects[addtoany][subdir] = "contrib"
projects[addtoany][version] = "1.3"

projects[admin_toolbar][version] ="1.x-dev"
projects[admin_toolbar][type] ="module"
projects[admin_toolbar][subdir] ="contrib"
projects[admin_toolbar][download][type] = "git"
projects[admin_toolbar][download][branch] = "8.x-1.x"

projects[better_formats][version] = "1.x-dev"
projects[better_formats][type] = "module"
projects[better_formats][subdir] = "contrib"
projects[better_formats][download][type] = "git"
projects[better_formats][download][branch] = "8.x-1.x"
; [meta] Utilize multiple Better Formats patches in DF without conflict
; http://drupal.org/node/2737757
projects[better_formats][patch][2737757] = "http://drupal.org/files/issues/2737757-2.patch"

projects[block_class][version] = "1.x-dev"
projects[block_class][type] = "module"
projects[block_class][subdir] = "contrib"
projects[block_class][download][type] = "git"
projects[block_class][download][branch] = "8.x-1.x"
; Add support for page manager.
; http://drupal.org/node/2509142
projects[block_class][patch][2509142] = "http://drupal.org/files/issues/add-page-manager-support-2509142-2.patch"

projects[dropzonejs][type] = "module"
projects[dropzonejs][subdir] = "contrib"
projects[dropzonejs][version] = "1.0-alpha1"

projects[composer_manager][type] = "module"
projects[composer_manager][subdir] = "contrib"
projects[composer_manager][version] = "1.0-rc2"

projects[config_devel][version] = "1.x-dev"
projects[config_devel][type] = "module"
projects[config_devel][subdir] = "contrib"
projects[config_devel][download][type] = "git"
projects[config_devel][download][branch] = "8.x-1.x"

projects[config_update][type] = "module"
projects[config_update][subdir] = "contrib"
projects[config_update][version] = "1.1"

projects[config_sync][version] = "1.x-dev"
projects[config_sync][type] = "module"
projects[config_sync][subdir] = "contrib"
projects[config_sync][download][type] = "git"
projects[config_sync][download][branch] = "8.x-1.x"

projects[content_browser][version] = "1.x-dev"
projects[content_browser][type] = "module"
projects[content_browser][subdir] = "contrib"
projects[content_browser][download][type] = "git"
projects[content_browser][download][branch] = "8.x-1.x"

projects[crop][type] = "module"
projects[crop][subdir] = "contrib"
projects[crop][version] = "1.0-rc1"

projects[ctools][type] = "module"
projects[ctools][subdir] = "contrib"
projects[ctools][version] = "3.0-alpha26"

projects[embed][type] = "module"
projects[embed][subdir] = "contrib"
projects[embed][version] = "1.0-rc2"

projects[entity_browser][version] = "1.x-dev"
projects[entity_browser][type] = "module"
projects[entity_browser][subdir] = "contrib"
projects[entity_browser][download][type] = "git"
projects[entity_browser][download][branch] = "8.x-1.x"

projects[entity_embed][type] = "module"
projects[entity_embed][subdir] = "contrib"
projects[entity_embed][version] = "1.0-alpha1"

projects[features][type] = "module"
projects[features][subdir] = "contrib"
projects[features][version] = "3.0-beta3"

projects[field_group][type] = "module"
projects[field_group][subdir] = "contrib"
projects[field_group][version] = "1.0-rc4"

projects[file_browser][version] = "1.x-dev"
projects[file_browser][type] = "module"
projects[file_browser][subdir] = "contrib"
projects[file_browser][download][type] = "git"
projects[file_browser][download][branch] = "8.x-1.x"

projects[file_entity][version] = "2.x-dev"
projects[file_entity][type] = "module"
projects[file_entity][subdir] = "contrib"
projects[file_entity][download][type] = "git"
projects[file_entity][download][branch] = "8.x-2.x"

projects[geocoder][type] = "module"
projects[geocoder][subdir] = "contrib"
projects[geocoder][version] = "2.0-alpha3"

projects[geofield][version] = "1.x-dev"
projects[geofield][type] = "module"
projects[geofield][subdir] = "contrib"
projects[geofield][download][type] = "git"
projects[geofield][download][branch] = "8.x-1.x"

projects[geophp][version] = "1.x-dev"
projects[geophp][type] = "module"
projects[geophp][subdir] = "contrib"
projects[geophp][download][type] = "git"
projects[geophp][download][branch] = "8.x-1.x"

projects[geolocation][version] = "1.x-dev"
projects[geolocation][type] = "module"
projects[geolocation][subdir] = "contrib"
projects[geolocation][download][type] = "git"
projects[geolocation][download][branch] = "8.x-1.x"

projects[google_analytics][type] = "module"
projects[google_analytics][subdir] = "contrib"
projects[google_analytics][version] = "2.0"

projects[image_widget_crop][type] = "module"
projects[image_widget_crop][subdir] = "contrib"
projects[image_widget_crop][version] = "1.0"

projects[import][version] = "2.x-dev"
projects[import][type] = "module"
projects[import][subdir] = "contrib"
projects[import][download][type] = "git"
projects[import][download][branch] = "8.x-2.x"

projects[inline_entity_form][version] = "1.x-dev"
projects[inline_entity_form][type] = "module"
projects[inline_entity_form][subdir] = "contrib"
projects[inline_entity_form][download][type] = "git"
projects[inline_entity_form][download][branch] = "8.x-1.x"

projects[layout_plugin][type] = "module"
projects[layout_plugin][subdir] = "contrib"
projects[layout_plugin][version] = "1.0-alpha22"

projects[leaflet][version] = "1.x-dev"
projects[leaflet][type] = "module"
projects[leaflet][subdir] = "contrib"
projects[leaflet][download][type] = "git"
projects[leaflet][download][branch] = "8.x-1.x"

projects[libraries][version] = "3.x-dev"
projects[libraries][type] = "module"
projects[libraries][subdir] = "contrib"
projects[libraries][download][type] = "git"
projects[libraries][download][branch] = "8.x-3.x"

projects[lite][version] = "1.x-dev"
projects[lite][type] = "module"
projects[lite][subdir] = "contrib"
projects[lite][download][type] = "git"
projects[lite][download][branch] = "8.x-1.x"
; Lite uses the wrong path
; https://www.drupal.org/node/2718189
projects[lite][patch][2718189] = "https://www.drupal.org/files/issues/2718189-3.patch"

projects[moderate_mmenu][version] = "1.x-dev"
projects[moderate_mmenu][type] = "module"
projects[moderate_mmenu][subdir] = "contrib"
projects[moderate_mmenu][download][type] = "git"
projects[moderate_mmenu][download][branch] = "8.x-1.x"

projects[migrate_plus][type] = "module"
projects[migrate_plus][subdir] = "contrib"
projects[migrate_plus][version] = "2.0-beta1"
; Update migrate plus migrate URL plugin and SourcePluginExtension to work with 8.1.0
; https://www.drupal.org/node/2714357
projects[migrate_plus][patch][2714357] = "https://www.drupal.org/files/issues/migrate_url_sourcepluginextension-2714357-1.patch"

projects[migrate_source_csv][version] = "2.x-dev"
projects[migrate_source_csv][type] = "module"
projects[migrate_source_csv][subdir] = "contrib"
projects[migrate_source_csv][download][type] = "git"
projects[migrate_source_csv][download][branch] = "8.x-2.x"

projects[migrate_tools][type] = "module"
projects[migrate_tools][subdir] = "contrib"
projects[migrate_tools][version] = "2.0-beta1"

projects[oauth][version] = "1.x-dev"
projects[oauth][type] = "module"
projects[oauth][subdir] = "contrib"
projects[oauth][download][type] = "git"
projects[oauth][download][branch] = "8.x-1.x"

projects[page_manager][type] = "module"
projects[page_manager][subdir] = "contrib"
projects[page_manager][version] = "1.0-alpha23"
; Rename the core "Page with blocks" admin_label and sort display variants
; https://www.drupal.org/node/2503947
projects[page_manager][patch][2503947] = "https://www.drupal.org/files/issues/page_manager-asort-admin-label-etc-0.patch"

projects[panels][version] = "3.x-dev"
projects[panels][type] = "module"
projects[panels][subdir] = "contrib"
projects[panels][download][type] = "git"
projects[panels][download][branch] = "8.x-3.x"
; Allow other modules to disable the IPE based on custom logic
; https://www.drupal.org/node/2667754
projects[panels][patch][2667754] = "https://www.drupal.org/files/issues/2667754-3.patch"
; Editing layouts via IPE affects other users editing content until custom is selected
; https://www.drupal.org/node/2701433
projects[panels][patch][2701433] = "https://www.drupal.org/files/issues/bandaid.patch"
; IPE's cancel button is easy to mistake for a button to close the tray (rather than throw away all changes).
; https://www.drupal.org/node/2636490
projects[panels][patch][2636490] = "https://www.drupal.org/files/issues/panels-ipe-esc-focus-fixes-2636490-9.patch"

projects[panelizer][type] = "module"
projects[panelizer][subdir] = "contrib"
projects[panelizer][version] = "3.0-alpha2"
; Explicitly set the Panels IPE URL root when saving in Panelizer
; https://www.drupal.org/node/2700597
projects[panelizer][patch][2700597] = "https://www.drupal.org/files/issues/panelizer-ipe-url-root-handling.patch"

projects[pathauto][type] = "module"
projects[pathauto][subdir] = "contrib"
projects[pathauto][version] = "1.0-alpha3"

projects[refreshless][type] = "module"
projects[refreshless][subdir] = "contrib"
projects[refreshless][version] = "1.0-alpha3"

projects[restui][version] = "1.x-dev"
projects[restui][type] = "module"
projects[restui][subdir] = "contrib"
projects[restui][download][type] = "git"
projects[restui][download][branch] = "8.x-1.x"

projects[scenarios][version] = "2.x-dev"
projects[scenarios][type] = "module"
projects[scenarios][subdir] = "contrib"
projects[scenarios][download][type] = "git"
projects[scenarios][download][branch] = "8.x-2.x"

projects[scheduler][type] = "module"
projects[scheduler][subdir] = "contrib"
projects[scheduler][version] = "1.0-alpha1"

projects[token][type] = "module"
projects[token][subdir] = "contrib"
projects[token][version] = "1.0-alpha2"

projects[url_embed][version] = "1.x-dev"
projects[url_embed][type] = "module"
projects[url_embed][subdir] = "contrib"
projects[url_embed][download][type] = "git"
projects[url_embed][download][branch] = "8.x-1.x"
; Provide field formatters for core's URL field type
; https://www.drupal.org/node/2546204
projects[url_embed][patch][2546204] = "https://www.drupal.org/files/issues/2546204-6.patch"

projects[views_infinite_scroll][version] = "1.0"
projects[views_infinite_scroll][type] = "module"
projects[views_infinite_scroll][subdir] = "contrib"

projects[workbench_moderation][type] = "module"
projects[workbench_moderation][subdir] = "contrib"
projects[workbench_moderation][version] = "1.0"
; Provide logical access control for when IPE should be applied to moderated nodes
; https://www.drupal.org/node/2668006
projects[workbench_moderation][patch][2668006] = "https://www.drupal.org/files/issues/2668006-2.patch"
; Replace the workbench moderation form with a block
; https://www.drupal.org/node/2685163
projects[workbench_moderation][patch][2685163] = "https://www.drupal.org/files/issues/2685163-19.patch"
;allow for latest revision to be used in a views_infinite_scroll
;https://www.drupal.org/node/2702041
projects[workbench_moderation][patch][2702041] = "https://www.drupal.org/files/issues/2702041-20.patch"


; Libraries
libraries[dropzone][destination] = "../../libraries"
libraries[dropzone][download][type] = "get"
libraries[dropzone][download][url] = "https://github.com/enyo/dropzone/archive/v4.2.0.zip"

libraries[fontawesome-iconpicker][destination] = "../../libraries"
libraries[fontawesome-iconpicker][download][type] = "get"
libraries[fontawesome-iconpicker][download][url] = "https://github.com/mjolnic/fontawesome-iconpicker/archive/1.2.0.zip"

libraries[masonry][destination] = "../../libraries"
libraries[masonry][download][type] = "get"
libraries[masonry][download][url] = "https://github.com/desandro/masonry/archive/v3.3.1.zip"

libraries[imagesloaded][destination] = "../../libraries"
libraries[imagesloaded][download][type] = "get"
libraries[imagesloaded][download][url] = "https://github.com/desandro/imagesloaded/archive/v3.1.8.zip"

libraries[ckeditor-track-changes][download][type] = "get"
libraries[ckeditor-track-changes][download][url] = "https://github.com/loopindex/ckeditor-track-changes/archive/fe309f5f5ea98ce739bf21e06f34d1d935d0c0ad.zip"
libraries[ckeditor-track-changes][directory_name] = "lite"
; CKLite interferes with content insertion
; http://drupal.org/node/2482879
libraries[ckeditor-track-changes][patch][2482879] = "http://drupal.org/files/issues/cklite-content-insertion-2482879-4.patch"

libraries[slick][destination] = "../../libraries"
libraries[slick][download][type] = "get"
libraries[slick][download][url] = "https://github.com/kenwheeler/slick/archive/1.5.0.zip"

; Themes
projects[adminimal_theme][version] = "1.1"
projects[adminimal_theme][type] = "theme"
projects[adminimal_theme][subdir] = "contrib"
projects[adminimal_theme][download][type] = "git"
projects[adminimal_theme][download][branch] = "8.x-1.x"
projects[adminimal_theme][patch][2733651] = "https://www.drupal.org/files/issues/adminimal-merged-patch-from-2680689-and-2733517.patch"

projects[zurb-foundation][version] = "6.x-dev"
projects[zurb-foundation][type] = "theme"
projects[zurb-foundation][subdir] = "contrib"
projects[zurb-foundation][download][type] = "git"
projects[zurb-foundation][download][branch] = "8.x-6.x"
