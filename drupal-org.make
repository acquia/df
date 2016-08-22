core = 8.x
api = 2

; Defaults
defaults[projects][subdir] = "contrib"

; Profiles
projects[lightning][type] = "profile"
projects[lightning][version] = "1.03"
projects[lightning][subdir] = ""
projects[lightning][patch][0] = "https://www.drupal.org/files/issues/add-view-check-2772949-2_0.patch"

; Modules
projects[acquia_connector][type] = "module"
projects[acquia_connector][version] = "1.3"

projects[address][type] = "module"
projects[address][version] = "1.0-beta3"

projects[addtoany][type] = "module"
projects[addtoany][version] = "1.4"

projects[admin_toolbar][type] = "module"
projects[admin_toolbar][download][type] = "git"
projects[admin_toolbar][download][branch] = "8.x-1.x"
projects[admin_toolbar][version] = "1.x-dev"

projects[adminimal_admin_toolbar][type] = "module"
projects[adminimal_admin_toolbar][download][type] = "git"
projects[adminimal_admin_toolbar][download][branch] = "8.x-1.x"
projects[adminimal_admin_toolbar][version] = "1.x-dev"

projects[better_formats][type] = "module"
projects[better_formats][download][type] = "git"
projects[better_formats][download][branch] = "8.x-1.x"
projects[better_formats][version] = "1.x-dev"

projects[block_class][type] = "module"
projects[block_class][download][type] = "git"
projects[block_class][download][branch] = "8.x-1.x"
projects[block_class][version] = "1.x-dev"
projects[block_class][patch][0] = "https://www.drupal.org/files/issues/add-page-manager-support-2509142-2.patch"

projects[config_devel][type] = "module"
projects[config_devel][download][type] = "git"
projects[config_devel][download][branch] = "8.x-1.x"
projects[config_devel][version] = "1.x-dev"

projects[config_sync][type] = "module"
projects[config_sync][download][type] = "git"
projects[config_sync][download][branch] = "8.x-1.x"
projects[config_sync][version] = "1.x-dev"

projects[config_update][type] = "module"
projects[config_update][version] = "1.1"

projects[content_browser][type] = "module"
projects[content_browser][version] = "1.0-alpha1"
projects[content_browser][patch][0] = "https://www.drupal.org/files/issues/content-browser-recursive-embed-protection.patch"

projects[crop][type] = "module"
projects[crop][version] = "1.0"

projects[ctools][type] = "module"
projects[ctools][version] = "3.0-alpha26"

projects[dropzonejs][type] = "module"
projects[dropzonejs][version] = "1.0-alpha2"

projects[embed][type] = "module"
projects[embed][version] = "1.0-rc3"

projects[entity][type] = "module"
projects[entity][version] = "1.0-alpha3"

projects[entity_browser][type] = "module"
projects[entity_browser][version] = "1.0-alpha7"

projects[entity_embed][type] = "module"
projects[entity_embed][version] = "1.0-alpha3"

projects[features][type] = "module"
projects[features][version] = "3.0-beta7"

projects[field_group][type] = "module"
projects[field_group][version] = "1.0-rc4"

projects[file_browser][type] = "module"
projects[file_browser][version] = "1.0-alpha1"

projects[file_entity][type] = "module"
projects[file_entity][version] = "2.0-beta2"

projects[geocoder][type] = "module"
projects[geocoder][version] = "2.0-alpha4"

projects[geofield][type] = "module"
projects[geofield][download][type] = "git"
projects[geofield][download][branch] = "8.x-1.x"
projects[geofield][version] = "1.x-dev"

projects[geolocation][type] = "module"
projects[geolocation][download][type] = "git"
projects[geolocation][download][branch] = "8.x-1.x"
projects[geolocation][version] = "1.x-dev"

projects[geophp][type] = "module"
projects[geophp][download][type] = "git"
projects[geophp][download][branch] = "8.x-1.x"
projects[geophp][version] = "1.x-dev"

projects[google_analytics][type] = "module"
projects[google_analytics][version] = "2.1"

projects[image_effects][type] = "module"
projects[image_effects][version] = "1.0-alpha2"

projects[image_widget_crop][type] = "module"
projects[image_widget_crop][version] = "1.0"

projects[import][type] = "module"
projects[import][download][type] = "git"
projects[import][download][branch] = "8.x-2.x"
projects[import][version] = "2.x-dev"

projects[inline_entity_form][type] = "module"
projects[inline_entity_form][version] = "1.0-alpha6"

projects[layout_plugin][type] = "module"
projects[layout_plugin][version] = "1.0-alpha22"

projects[leaflet][type] = "module"
projects[leaflet][download][type] = "git"
projects[leaflet][download][branch] = "8.x-1.x"
projects[leaflet][version] = "1.x-dev"

projects[libraries][type] = "module"
projects[libraries][download][type] = "git"
projects[libraries][download][branch] = "8.x-3.x"
projects[libraries][version] = "3.x-dev"

projects[lite][type] = "module"
projects[lite][download][type] = "git"
projects[lite][download][branch] = "8.x-1.x"
projects[lite][version] = "1.x-dev"

projects[media_entity][type] = "module"
projects[media_entity][version] = "1.3"

projects[media_entity_document][type] = "module"
projects[media_entity_document][version] = "1.1"

projects[media_entity_image][type] = "module"
projects[media_entity_image][version] = "1.2"

projects[media_entity_instagram][type] = "module"
projects[media_entity_instagram][version] = "1.2"

projects[media_entity_twitter][type] = "module"
projects[media_entity_twitter][version] = "1.2"

projects[metatag][type] = "module"
projects[metatag][version] = "1.0-beta9"

projects[migrate_plus][type] = "module"
projects[migrate_plus][version] = "2.0-beta2"

projects[migrate_source_csv][type] = "module"
projects[migrate_source_csv][download][type] = "git"
projects[migrate_source_csv][download][branch] = "8.x-2.x"
projects[migrate_source_csv][version] = "2.x-dev"

projects[migrate_tools][type] = "module"
projects[migrate_tools][version] = "2.0-beta1"

projects[moderate_mmenu][type] = "module"
projects[moderate_mmenu][download][type] = "git"
projects[moderate_mmenu][download][branch] = "8.x-1.x"
projects[moderate_mmenu][version] = "1.x-dev"

projects[oauth][type] = "module"
projects[oauth][download][type] = "git"
projects[oauth][download][branch] = "8.x-1.x"
projects[oauth][version] = "1.x-dev"

projects[page_manager][type] = "module"
projects[page_manager][version] = "1.0-alpha23"
projects[page_manager][patch][0] = "https://www.drupal.org/files/issues/page_manager--alpha23-09e79ff--2747739-3.patch"
projects[page_manager][patch][1] = "https://www.drupal.org/files/issues/page_manager-asort-admin-label-etc-0.patch"
projects[page_manager][patch][2] = "https://www.drupal.org/files/issues/2746149-2.patch"

projects[panelizer][type] = "module"
projects[panelizer][version] = "3.0-alpha2"
projects[panelizer][patch][0] = "https://www.drupal.org/files/issues/panelizer--alpha2-1ddfa61--2747739-5.patch"
projects[panelizer][patch][1] = "https://www.drupal.org/files/issues/2701349-2.patch"
projects[panelizer][patch][2] = "https://www.drupal.org/files/issues/panelizer-ipe-url-root-handling.patch"
projects[panelizer][patch][3] = "https://www.drupal.org/files/issues/2664682-49.patch"
projects[panelizer][patch][4] = "https://www.drupal.org/files/issues/2546204-6.patch"
projects[panelizer][patch][5] = "https://www.drupal.org/files/issues/panelizer-quickedit-2693163-2.patch"
projects[panelizer][patch][6] = "https://www.drupal.org/files/issues/check-for-build-array-keys-2760051_1.patch"

projects[panels][type] = "module"
projects[panels][version] = "3.0-beta4"
projects[panels][patch][0] = "https://www.drupal.org/files/issues/panels--beta4-4c4a3a7--2747739-17--binary.patch"
projects[panels][patch][1] = "https://www.drupal.org/files/issues/2667754-3.patch"
projects[panels][patch][2] = "https://www.drupal.org/files/issues/bandaid.patch"
projects[panels][patch][3] = "https://www.drupal.org/files/issues/2697587-48.patch"
projects[panels][patch][4] = "https://www.drupal.org/files/issues/panels-ipe-esc-focus-fixes-2636490-9.patch"

projects[pathauto][type] = "module"
projects[pathauto][version] = "1.0-alpha3"

projects[refreshless][type] = "module"
projects[refreshless][version] = "1.0-alpha3"

projects[replicate][type] = "module"
projects[replicate][download][type] = "git"
projects[replicate][download][branch] = "8.x-1.x"
projects[replicate][version] = "1.x-dev"

projects[restui][type] = "module"
projects[restui][download][type] = "git"
projects[restui][download][branch] = "8.x-1.x"
projects[restui][version] = "1.x-dev"

projects[scenarios][type] = "module"
projects[scenarios][download][type] = "git"
projects[scenarios][download][branch] = "8.x-2.x"
projects[scenarios][version] = "2.x-dev"

projects[scheduled_updates][type] = "module"
projects[scheduled_updates][version] = "1.0-alpha5"
projects[scheduled_updates][patch][0] = "https://www.drupal.org/files/issues/schedule_updates-save_type-2674874-2.patch"

projects[scheduler][type] = "module"
projects[scheduler][version] = "1.0-alpha1"

projects[token][type] = "module"
projects[token][version] = "1.0-beta1"

projects[url_embed][type] = "module"
projects[url_embed][download][type] = "git"
projects[url_embed][download][branch] = "8.x-1.x"
projects[url_embed][version] = "1.x-dev"

projects[video_embed_field][type] = "module"
projects[video_embed_field][version] = "1.3"

projects[views_infinite_scroll][type] = "module"
projects[views_infinite_scroll][version] = "1.2"

projects[workbench_moderation][type] = "module"
projects[workbench_moderation][version] = "1.1"
projects[workbench_moderation][patch][0] = "https://www.drupal.org/files/issues/2668006-2.patch"
projects[workbench_moderation][patch][1] = "https://www.drupal.org/files/issues/2685163-19.patch"
projects[workbench_moderation][patch][2] = "https://www.drupal.org/files/issues/workbench_moderation-quickedit-support.patch"

; Themes
projects[adminimal_theme][type] = "theme"
projects[adminimal_theme][version] = "1.1"

projects[zurb-foundation][type] = "theme"
projects[zurb-foundation][download][type] = "git"
projects[zurb-foundation][download][branch] = "8.x-6.x"
projects[zurb-foundation][version] = "6.x-dev"

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

libraries[ckeditor-track-changes][destination] = "../../libraries"
libraries[ckeditor-track-changes][download][type] = "get"
libraries[ckeditor-track-changes][download][url] = "https://github.com/loopindex/ckeditor-track-changes/archive/fe309f5f5ea98ce739bf21e06f34d1d935d0c0ad.zip"
libraries[ckeditor-track-changes][directory_name] = "lite"
; CKLite interferes with content insertion
; http://drupal.org/node/2482879
libraries[ckeditor-track-changes][patch][2482879] = "http://drupal.org/files/issues/cklite-content-insertion-2482879-4.patch"

libraries[slick][destination] = "../../libraries"
libraries[slick][download][type] = "get"
libraries[slick][download][url] = "https://github.com/kenwheeler/slick/archive/1.5.0.zip"
