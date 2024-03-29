<?php
/**
 * Structure build script
 *
 * @package structure
 * @subpackage build
 */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* define package */
define('PKG_NAME','Structure');
define('PKG_NAMESPACE','structure');
define('PKG_VERSION','1.0.0');
define('PKG_RELEASE','beta1');

function getSnippetContent($path, $name, $debug = false) {
	$filename = $path . $name .'.php';
    $o = file_get_contents($filename);
    $o = str_replace('<?php','',$o);
    $o = str_replace('?>','',$o);
    $o = trim($o);
    return $o;
}

$root = dirname(dirname(__FILE__)).'/';
$sources= array (
    'debug' => false,
    'root' => $root,
    'files' => $root .'files/',
    'build' => $root .'_build/',
	'data' => $root .'_build/data/',
    'resolvers' => $root . '_build/resolvers/',
    'core' => $root.'core/components/'.PKG_NAMESPACE,
    'snippets' => $root.'core/components/'.PKG_NAMESPACE.'/',
	'plugins' => $root.'core/components/'.PKG_NAMESPACE.'/elements/plugins/',
    'assets' => $root.'assets/components/'.PKG_NAMESPACE,
	'lexicon' => $root . 'core/components/'.PKG_NAMESPACE.'/lexicon/',
    'docs' => $root.'core/components/'.PKG_NAMESPACE.'/docs/',
    'model' => $root.'core/components/'.PKG_NAMESPACE.'/model/',
);
unset($root);

require_once dirname(__FILE__) . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
echo XPDO_CLI_MODE ? '' : '<pre>';
$modx->setLogTarget('ECHO');

$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAMESPACE,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAMESPACE,false,true,'{core_path}components/'.PKG_NAMESPACE.'/');
$modx->getService('lexicon','modLexicon');
$modx->lexicon->load('structure:default,mgr');

/* load system settings */
// $modx->log(modX::LOG_LEVEL_INFO,'Packaging in System Settings...');
// $settings = include $sources['data'].'transport.settings.php';
// if (empty($settings)) $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in settings.');
// $attributes= array(
    // xPDOTransport::UNIQUE_KEY => 'key',
    // xPDOTransport::PRESERVE_KEYS => true,
    // xPDOTransport::UPDATE_OBJECT => false,
// );
// foreach ($settings as $setting) {
    // $vehicle = $builder->createVehicle($setting,$attributes);
    // $builder->putVehicle($vehicle);
// }
// $modx->log(modX::LOG_LEVEL_INFO,'<strong>Packaged in '.count($settings).' system settings.</strong>'); flush();
// unset($settings,$setting,$attributes);

/* create category */
$category= $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category',PKG_NAME);
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in category.'); flush();

/* add snippets */
$modx->log(modX::LOG_LEVEL_INFO,'Adding snippets...');
$snippets = include $sources['data'].'transport.snippets.php';
if (empty($snippets)) $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in snippets.');
$category->addMany($snippets);
$modx->log( xPDO::LOG_LEVEL_INFO, '<strong>Packaged in '. count( $snippets ) .' snippets.</strong>' ); flush();

/* create category vehicle */
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Children' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'category',
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
                'Snippets' => array(
                    xPDOTransport::PRESERVE_KEYS => false,
                    xPDOTransport::UPDATE_OBJECT => true,
                    xPDOTransport::UNIQUE_KEY => 'name',
                ),
            ),
        ),
        'Snippets' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
    ),
);
$vehicle = $builder->createVehicle($category,$attr);

$modx->log(modX::LOG_LEVEL_INFO, 'Adding file resolvers to category...');

$vehicle->resolve('file',array(
    'source' => $sources['core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));

$modx->log(modX::LOG_LEVEL_INFO,'Packaged in resolvers.'); flush();
$builder->putVehicle($vehicle);

/* Pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
    // 'setup-options' => array(
        // 'source' => $sources['build'].'setup.options.php',
    // ),
));
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in package attributes.'); flush();

$modx->log(modX::LOG_LEVEL_INFO,'Packing...'); flush();
$builder->pack();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");

exit ();