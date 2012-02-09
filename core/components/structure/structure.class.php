<?php
/**
 * Structure
 *
 *
 * @package myjournal
 */
/**
 * An helper class to retreive file based chunk.
 *
 * @author Stephane Boulard <lossendae@gmail.com>
 * @package myjournal
 */
class Structure {
    /**
     * @access protected
     * @var array A collection of preprocessed chunk values.
     */
    protected $chunks = array();
    /**
     * @access public
     * @var modX A reference to the modX object.
     */
    public $modx = null;
    /**
     * @access public
     * @var array A collection of properties to adjust Structure behaviour.
     */
    public $config = array();
    protected $structurePath = null;

    /**
     * The Structure Constructor.
     *
     * This method is used to create a new Structure object.
     *
     * @param modX &$modx A reference to the modX object.
     * @param array $config A collection of properties that modify Structure
     * behaviour.
     * @return Structure A unique Structure instance.
     */
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;
		
		/* Only the assets_path is required for this class */
		$assets_path = $this->modx->getOption('structure.assets_path',$config,$this->modx->getOption('assets_path')); 		
		
		$this->config = array_merge(array(						
			'template_path' => $assets_path.'templates/',
			'structure_dir' => 'structure/',
			'tpl_suffix' => '.tpl',
			
			'debug' => true,
        ),$config);

        /* load debugging settings */
        if ($this->modx->getOption('debug',$this->config,false)) {
            error_reporting(E_ALL); ini_set('display_errors',true);
            $this->modx->setLogTarget('HTML');
            $this->modx->setLogLevel(MODX_LOG_LEVEL_ERROR);

            $debugUser = $this->config['debugUser'] == '' ? $this->modx->user->get('username') : 'anonymous';
            $user = $this->modx->getObject('modUser',array('username' => $debugUser));
            if ($user == null) {
                $this->modx->user->set('id',$this->modx->getOption('debugUserId',$this->config,1));
                $this->modx->user->set('username',$debugUser);
            } else {
                $this->modx->user = $user;
            }
        }
    }
	
		
	/**
     * setStructurePath.
     *
     * Create the path to file based chunks
     *
	 * @access public
     * @param $properties array The properties array.
     */
	public function setStructurePath($templateProperties = array()){
		/* Retreive the values for the properties */
		foreach($templateProperties as $k => $v){
			$config[$k] =  $this->convertPath($templateProperties[$k]['value']);
		}
		$this->config = array_merge($this->config, $config);
		$this->structurePath = $this->config['template_path'] . $this->config['template_name'] .'/'. $this->config['structure_dir'];
	}
	
	/**
     * convertPath.
     *
     * Convert string params to path for use in file based chunks
     *
	 * @access private
     * @param $value string the string to convert.
     * @return $value string The converted string.
     */
	private function convertPath($value){	
		$value = str_replace(array(
			'{base_path}',
			'{assets_path}',
			'{template_path}',
		),array(
			$this->modx->getOption('base_path'),
			$this->modx->getOption('assets_path'),
			$this->config['template_path'],
		),$value);
		return $value;
	}
		
	/**
     * Processes the content of a chunk in either of the following ways:
     *
     * Caches the preprocessed chunk content to an array to speed loading
     * times, especially when looping through collections.
     *
     * @access public
     * @param string $name The name of the chunk to process
     * @param array $properties (optional) An array of properties
     * @return string The processed content string
     */
    public function getChunk($name, $properties = array()) {
	
        /* first check internal cache */
        if (!isset($this->chunks[$name])) {
            $chunk = false;		
				$f = $this->structurePath . strtolower($name) . $this->config['tpl_suffix'];
				if (file_exists($f)) {
					$o = file_get_contents($f);
					$chunk = $this->modx->newObject('modChunk');
					$chunk->set('name',$name);
					$chunk->setContent($o);
				} else {
					return 'Structure :  Chunk <strong>'.$name.'</strong> not found in "<i>'.$f.'</i>"<br/>';
				}
				$this->chunks[$name] = $chunk->getContent();
        } else { /* load chunk from cache */
            $o = $this->chunks[$name];
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
        }
        $chunk->setCacheable(false);
        return $chunk->process($properties);
    }
}