core = 8.x
api = 2

; Defaults
defaults[projects][subdir] = "contrib"

; Profiles
projects[lightning][type] = "profile"
projects[lightning][version] = "1.13"
projects[lightning][subdir] = ""

; Modules
projects[acquia_connector][type] = "module"
projects[acquia_connector][version] = "1.6"

projects[address][type] = "module"
projects[address][download][type] = "git"
projects[address][download][branch] = "8.x-1.x"
projects[address][version] = "1.x-dev"

projects[addtoany][type] = "module"
projects[addtoany][version] = "1.6"

projects[admin_toolbar][type] = "module"
projects[admin_toolbar][version] = "1.17"

projects[adminimal_admin_toolbar][type] = "module"
projects[adminimal_admin_toolbar][version] = "1.3"

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

projects[config_rewrite][type] = "module"
projects[config_rewrite][version] = "1.0-beta1"

projects[config_sync][type] = "module"
projects[config_sync][version] = "1.0-alpha3"

projects[config_tools][type] = "module"
projects[config_tools][download][type] = "git"
projects[config_tools][download][branch] = "8.x-1.x"
projects[config_tools][version] = "1.x-dev"
projects[config_tools][patch][0] = "https://www.drupal.org/files/issues/config_partial_override-8.x-1.x.patch"

projects[config_update][type] = "module"
projects[config_update][version] = "1.2"

projects[content_browser][type] = "module"
projects[content_browser][version] = "1.0-alpha1"
projects[content_browser][patch][0] = "https://www.drupal.org/files/issues/content-browser-recursive-embed-protection.patch"
projects[content_browser][patch][1] = "https://www.drupal.org/files/issues/content-browser-simple-block-table.patch"

projects[crop][type] = "module"
projects[crop][version] = "1.0"

projects[ctools][type] = "module"
projects[ctools][version] = "3.0-alpha27"

projects[dropzonejs][type] = "module"
projects[dropzonejs][version] = "1.0-alpha2"

projects[embed][type] = "module"
projects[embed][version] = "1.0-rc3"

projects[entity][type] = "module"
projects[entity][version] = "1.0-alpha3"

projects[entity_block][type] = "module"
projects[entity_block][download][type] = "git"
projects[entity_block][download][branch] = "8.x-1.x"
projects[entity_block][version] = "1.x-dev"

projects[entity_browser][type] = "module"
projects[entity_browser][version] = "1.0-alpha10"

projects[entity_embed][type] = "module"
projects[entity_embed][version] = "1.0-beta2"

projects[entity_gallery][type] = "module"
projects[entity_gallery][download][type] = "git"
projects[entity_gallery][download][branch] = "8.x-1.x"
projects[entity_gallery][version] = "1.x-dev"

projects[features][type] = "module"
projects[features][version] = "3.0-rc1"

projects[field_formatter_class][type] = "module"
projects[field_formatter_class][version] = "1.0-rc2"

projects[field_group][type] = "module"
projects[field_group][version] = "1.0-rc4"

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

projects[import][type] = "module"
projects[import][version] = "2.0-beta1"

projects[inline_entity_form][type] = "module"
projects[inline_entity_form][version] = "1.0-alpha6"

projects[layout_plugin][type] = "module"
projects[layout_plugin][version] = "1.0-alpha23"

projects[leaflet][type] = "module"
projects[leaflet][download][type] = "git"
projects[leaflet][download][branch] = "8.x-1.x"
projects[leaflet][version] = "1.x-dev"

projects[libraries][type] = "module"
projects[libraries][download][type] = "git"
projects[libraries][download][branch] = "8.x-3.x"
projects[libraries][version] = "3.x-dev"

projects[link_class][type] = "module"
projects[link_class][version] = "1.0"

projects[lite][type] = "module"
projects[lite][version] = "1.3"

projects[media_entity][type] = "module"
projects[media_entity][version] = "1.5"

projects[media_entity_document][type] = "module"
projects[media_entity_document][version] = "1.1"

projects[media_entity_image][type] = "module"
projects[media_entity_image][version] = "1.2"

projects[media_entity_instagram][type] = "module"
projects[media_entity_instagram][version] = "1.2"

projects[media_entity_twitter][type] = "module"
projects[media_entity_twitter][version] = "1.2"

projects[metatag][type] = "module"
projects[metatag][version] = "1.0-beta10"
projects[metatag][patch][0] = "https://www.drupal.org/files/issues/metatag-deactivate_drupalconsole_services_0.patch"

projects[migrate_plus][type] = "module"
projects[migrate_plus][version] = "2.0-beta2"

projects[migrate_source_csv][type] = "module"
projects[migrate_source_csv][version] = "2.0-beta1"

projects[migrate_tools][type] = "module"
projects[migrate_tools][version] = "2.0-beta1"

projects[moderation_note][type] = "module"
projects[moderation_note][download][type] = "git"
projects[moderation_note][download][branch] = "8.x-1.x"
projects[moderation_note][version] = "1.x-dev"

projects[moderation_sidebar][type] = "module"
projects[moderation_sidebar][download][type] = "git"
projects[moderation_sidebar][download][branch] = "8.x-1.x"
projects[moderation_sidebar][version] = "1.x-dev"

projects[oauth][type] = "module"
projects[oauth][download][type] = "git"
projects[oauth][download][branch] = "8.x-1.x"
projects[oauth][version] = "1.x-dev"

projects[page_manager][type] = "module"
projects[page_manager][version] = "1.0-alpha24"
projects[page_manager][patch][0] = "https://www.drupal.org/files/issues/page_manager-asort-admin-label-etc-0.patch"
projects[page_manager][patch][1] = "https://www.drupal.org/files/issues/2746149-2.patch"

projects[panelizer][type] = "module"
projects[panelizer][version] = "3.0-alpha2"
projects[panelizer][patch][0] = "https://www.drupal.org/files/issues/panelizer--8.x-3.0-alpha2--54f0ac6e.patch"
projects[panelizer][patch][1] = "https://www.drupal.org/files/issues/2701349-2.patch"
projects[panelizer][patch][2] = "https://www.drupal.org/files/issues/panelizer-ipe-url-root-handling.patch"
projects[panelizer][patch][3] = "https://www.drupal.org/files/issues/2664682-89.patch"
projects[panelizer][patch][4] = "https://www.drupal.org/files/issues/panelizer-panels-ipe-tempstore-id.patch"
projects[panelizer][patch][5] = "https://www.drupal.org/files/issues/panelizer-quickedit-2693163-10.patch"

projects[panels][type] = "module"
projects[panels][version] = "3.0-beta5"
projects[panels][patch][0] = "https://www.drupal.org/files/issues/2793801-9.patch"
projects[panels][patch][1] = "https://www.drupal.org/files/issues/0001-Revert-Issue-2781897-by-lauriii-Title-of-empty-block.patch"

projects[pathauto][type] = "module"
projects[pathauto][version] = "1.0-beta1"

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
projects[scheduled_updates][patch][1] = "https://www.drupal.org/files/issues/scheduled_updates-no-canonical-link-for-update-type.patch"

projects[scheduler][type] = "module"
projects[scheduler][version] = "1.0-alpha2"

projects[search_autocomplete][type] = "module"
projects[search_autocomplete][download][type] = "git"
projects[search_autocomplete][download][branch] = "8.x-1.x"
projects[search_autocomplete][version] = "1.x-dev"

projects[token][type] = "module"
projects[token][version] = "1.0-beta2"

projects[url_embed][type] = "module"
projects[url_embed][download][type] = "git"
projects[url_embed][download][branch] = "8.x-1.x"
projects[url_embed][version] = "1.x-dev"

projects[video_embed_field][type] = "module"
projects[video_embed_field][version] = "1.3"

projects[view_modes_display][version] = "1.0"
projects[view_modes_display][type] = "module"

projects[views_infinite_scroll][type] = "module"
projects[views_infinite_scroll][version] = "1.2"

projects[workbench_moderation][type] = "module"
projects[workbench_moderation][version] = "1.1"
projects[workbench_moderation][patch][0] = "https://www.drupal.org/files/issues/2668006-2.patch"
projects[workbench_moderation][patch][1] = "https://www.drupal.org/files/issues/workbench_moderation-invalid_service_definition.patch"
projects[workbench_moderation][patch][2] = "https://www.drupal.org/files/issues/2685163-21.patch"
projects[workbench_moderation][patch][3] = "https://www.drupal.org/files/issues/workbench_moderation-quickedit-support-2749503-4.patch"

; Themes
projects[adminimal_theme][type] = "theme"
projects[adminimal_theme][version] = "1.2"
projects[adminimal_theme][patch][0] = "https://www.drupal.org/files/issues/2820992-adminial-theme-remove-zoom.patch"

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
libraries[ckeditor-track-changes][download][url] = "https://github.com/loopindex/ckeditor-track-changes/archive/a7df5c6b685385713e8aaf2df3f44c82e3bf823a.zip"
libraries[ckeditor-track-changes][directory_name] = "lite"
; CKLite interferes with content insertion
; http://drupal.org/node/2482879
libraries[ckeditor-track-changes][patch][2482879] = "http://drupal.org/files/issues/cklite-content-insertion-2482879-4.patch"

libraries[slick][destination] = "../../libraries"
libraries[slick][download][type] = "get"
libraries[slick][download][url] = "https://github.com/kenwheeler/slick/archive/1.5.0.zip"
