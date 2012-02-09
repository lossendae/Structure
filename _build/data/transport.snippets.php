<?php
/**
* @package structure
* @subpackage build
*/
$snippets = array();

$snippets[0]= $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
    'id' => 0,
    'name' => 'tpl',
    'description' => 'Helper snippet for filebased chunk related to the current template',
    'snippet' => getSnippetContent($sources['snippets'], 'snippet.tpl'),
),'',true,true);
// $properties = include $sources['build'].'properties/properties.cliche.php';
// $snippets[0]->setProperties($properties);
// unset($properties, $content);

return $snippets;