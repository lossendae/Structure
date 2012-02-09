<?php
/**
 * Snippet tpl
 *
 * @package structure
 */
/**
 * Sructure
 *
 * Filebased helper snippet
 *
 * @author Stephane Boulard <lossendae@gmail.com>
 * @package structure
 */
$structure = $modx->getService('structure','Structure',$modx->getOption('structure.core_path',null,$modx->getOption('core_path').'components/structure/'),$scriptProperties);
if (!($structure instanceof Structure)) return 'Structure could not be loaded';

/* Get the template properties for the current resource */
$templateProperties = $modx->resource->getOne('Template')->get('properties');

/* Set the template name if it's not provided by the properties set */
if(!array_key_exists('template_name', $templateProperties)){
	$templateProperties['template_name']['value'] = strtolower($modx->resource->getOne('Template')->get('templatename'));
}

/* Do we have to listen for specific class_keys to override a placehoder name */
$override = false;
if(array_key_exists('class_keys', $templateProperties)){
	$classKeys = explode(",", $templateProperties['class_keys']['value']);
	$currentClassKey = $modx->resource->get('class_key');
	if(in_array($currentClassKey, $classKeys)){
		$classKey = strtolower($currentClassKey);
		
		/* List of placeholders that should be overriden when using the current class_key */
		if(array_key_exists($classKey, $templateProperties)){
			$phListToOverride = explode(",", $templateProperties[$classKey]['value']);
			$override = true;
		}		
	}
}

/* Path to template tpl files */
$structure->setStructurePath($templateProperties);

/* The current placeholder name */
$name = $scriptProperties['name'];

/* Do we ovveride the current placeholder */
if($override && in_array($name, $phListToOverride)){
	$name = $templateProperties[$name]['value'];
}

/* Process the chunk */
$output = $structure->getChunk($name);
return $output;