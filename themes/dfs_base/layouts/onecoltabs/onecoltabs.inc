<?php

/**
 * implementation of hook_panels_layouts()
 */
// Plugin definition
$plugin = array(
  'title' => t('Single column Tabs'),
  'category' => t('Columns: 1'),
  'icon' => 'onecoltabs.png',
  'theme' => 'fin_onecoltabs',
  'css' => 'onecoltabs.css',
  'regions' => array(
  	'top' => t('Top region'),
  	'tabs'   => t('Tabbed region')
  	),
);
