<?php

/*
 * This file is part of the Fuel Doctrine package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Fuel\Doctrine;

use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\DriverChain;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\ORMException;

/**
 * Mapping Driver
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Mapping
{
	/**
	 * Map driver types to class names
	 *
	 * @var array
	 */
	protected static $drivers = array(
		'php'             => 'PHPDriver',
		'simplified_xml'  => 'SimplifiedXmlDriver',
		'simplified_yaml' => 'SimplifiedYamlDriver',
		'xml'             => 'XmlDriver',
		'yml'             => 'YamlDriver'
	);

	/**
	 * Default extension
	 *
	 * @var string
	 */
	protected $extension = 'dcm';

	/**
	 * Default config path
	 *
	 * @var string
	 */
	protected $configPath = 'config/doctrine';

	/**
	 * Default class path
	 *
	 * @var string
	 */
	protected $classPath = 'classes';

	/**
	 * Object name
	 *
	 * @var string
	 */
	protected $objectName = 'Entity';

	/**
	 * Mapping info
	 *
	 * @var array
	 */
	protected $mappingInfo = array();

	/**
	 * Makes sure there is a first load before registering
	 *
	 * @var boolean
	 */
	protected $isLoaded = false;

	/**
	 * Auto Mapping
	 *
	 * @var boolean
	 */
	protected $autoMapping = false;

	/**
	 * Creates a new Mapping object
	 *
	 * @param array   $mappingInfo
	 * @param boolean $autoMapping
	 */
	public function __construct(array $mappingInfo, $autoMapping = false)
	{
		$this->mappingInfo = $mappingInfo;
		$this->autoMapping = $autoMapping;
	}

	/**
	 * Returns default extension
	 *
	 * @return string
	 */
	public function getExtension()
	{
		return $this->extension;
	}

	/**
	 * Returns default config path
	 *
	 * @return string
	 */
	public function getConfigPath()
	{
		return $this->configPath;
	}

	/**
	 * Returns default class path
	 *
	 * @return string
	 */
	public function getClassPath()
	{
		return $this->classPath;
	}

	/**
	 * Returns default object name
	 *
	 * @return string
	 */
	public function getObjectName()
	{
		return $this->objectName;
	}

	/**
	 * Loads mapping info
	 */
	protected function loadMappingInfo()
	{
		$this->mappingInfo = $this->parseMappingInfo($this->mappingInfo, $this->autoMapping);

		$this->isLoaded = true;
	}

	/**
	 * Parses mapping info
	 *
	 * @param array   $mappingInfo
	 * @param boolean $autoMapping
	 *
	 * @return array
	 */
	public function parseMappingInfo(array $mappingInfo, $autoMapping)
	{
		if ($autoMapping)
		{
			$mappingInfo = $this->autoLoadMappingInfo($mappingInfo);
		}

		$registerMapping = array();

		foreach ($mappingInfo as $mappingName => $mappingConfig)
		{
			// This is from symfony DoctrineBundle, should be reviewed
			if (is_array($mappingConfig) === false or \Arr::get($mappingConfig, 'mapping', true) === false)
			{
				continue;
			}

			$mappingConfig = array_replace(array(
				'dir'    => false,
				'type'   => false,
				'prefix' => false,
			), $mappingConfig);

			if (isset($mappingConfig['is_component']) === false)
			{
				$mappingConfig['is_component'] = false;

				if (is_dir($mappingConfig['dir']) === false)
				{
					$mappingConfig['is_component'] = (\Package::loaded($mappingName) or \Module::loaded($mappingName));
				}
			}

			if ($mappingConfig['is_component'])
			{
				$mappingConfig = static::getComponentDefaults($mappingName, $mappingConfig);

				if (empty($mappingConfig))
				{
					continue;
				}
			}

			$registerMapping[$mappingName] = $mappingConfig;
		}

		return $registerMapping;
	}

	/**
	 * Generates auto mapping information
	 *
	 * @return array
	 */
	protected function autoLoadMappingInfo(array $mappingInfo)
	{
		foreach (\Package::loaded() as $package => $path)
		{
			$package .= '::package';

			\Arr::set($mappingInfo[$package], array(
				'is_component' => true,
			));
		}

		foreach (\Module::loaded() as $module => $path)
		{
			$module .= '::module';

			\Arr::set($mappingInfo[$module], array(
				'is_component' => true,
			));
		}

		\Arr::set($mappingInfo['app'], array(
			'is_component' => true,
		));

		return $mappingInfo;
	}

	/**
	 * Returns default settings for components
	 *
	 * @param string $mappingName
	 * @param array  $mappingConfig
	 *
	 * @return array
	 */
	protected function getComponentDefaults($mappingName, array $mappingConfig)
	{
		if (strpos($mappingName, '::'))
		{
			list($componentName, $componentType) = explode('::', $mappingName);
		}
		else
		{
			$componentName = $mappingName;

			$componentType = $this->detectComponentType($componentName);

			if ($componentType === false and $componentName === 'app')
			{
				$componentType = 'app';
			}
		}

		if (($componentPath = $this->getComponentPath($componentName, $componentType)) === false)
		{
			return false;
		}

		$configPath = $mappingConfig['dir'];

		if ($configPath === false)
		{
			$configPath = $this->getConfigPath();
		}

		if ($mappingConfig['type'] === false)
		{
			$mappingConfig['type'] = static::detectMetadataDriver($componentPath, $configPath);
		}

		if ($mappingConfig['type'] === false)
		{
			return false;
		}

		if ($mappingConfig['dir'] === false)
		{
			if (in_array($mappingConfig['type'], array('annotation', 'staticphp')))
			{
				$mappingConfig['dir'] = $this->getClassPath().DS.$this->getObjectName();
			}
			else
			{
				$mappingConfig['dir'] = $configPath;
			}
		}

		if (is_array($mappingConfig['dir']))
		{
			foreach ($mappingConfig['dir'] as &$path)
			{
				$path = $componentPath . $path;
			}
		}
		else
		{
			$mappingConfig['dir'] = $componentPath . $mappingConfig['dir'];
		}

		if ($mappingConfig['prefix'] === false)
		{
			$mappingConfig['prefix'] = $this->detectComponentNamespace($componentName, $componentType);
		}

		// Set this to false to prevent reinitialization on subsequent load calls
		$mappingConfig['is_component'] = false;

		return $mappingConfig;
	}

	/**
	 * Detects component type from name
	 *
	 * @param string $componentName
	 *
	 * @return string
	 */
	protected function detectComponentType($componentName)
	{
		if (\Package::loaded($componentName))
		{
			return 'package';
		}
		elseif (\Module::loaded($componentName))
		{
			return 'module';
		}

		return false;
	}

	/**
	 * Returns a path based on component type
	 *
	 * @param string $componentName
	 * @param string $componentType
	 *
	 * @return string
	 */
	public function getComponentPath($componentName, $componentType = 'app')
	{
		switch ($componentType)
		{
			case 'package':
				return \Package::exists($componentName);
				break;

			case 'module':
				return \Module::exists($componentName);
				break;

			case 'app':
				return APPPATH;
				break;

			default:
				return false;
				break;
		}
	}

	/**
	 * Detects which metadata driver to use for the supplied directory
	 *
	 * @param string       $dir        A directory path
	 * @param string|array $configPath Config path or paths
	 *
	 * @return string|null A metadata driver short name, if one can be detected
	 */
	protected function detectMetadataDriver($dir, $configPath)
	{
		$extension = $this->getExtension();

		foreach ((array) $configPath as $cPath)
		{
			$path = $dir.DS.$cPath.DS;

			if (($files = glob($path.'*.'.$extension.'.xml')) && count($files))
			{
				return 'xml';
			}
			elseif (($files = glob($path.'*.'.$extension.'.yml')) && count($files))
			{
				return 'yml';
			}
			elseif (($files = glob($path.'*.'.$extension.'.php')) && count($files))
			{
				return 'php';
			}
		}

		if (is_dir($dir.DS.$this->getClassPath().DS.$this->getObjectName()))
		{
			return 'annotation';
		}

		return false;
	}

	/**
	 * Detects component namespace
	 *
	 * @param string $componentName
	 * @param string $componentType
	 *
	 * @return string
	 */
	protected function detectComponentNamespace($componentName, $componentType)
	{
		if ($componentType === 'app')
		{
			return '';
		}

		return trim(str_replace(array('_', '-'), '\\', \Inflector::classify($componentName)), '\\');
	}

	/**
	 * Registers mapping in the Configuration
	 *
	 * @param Configuration $config
	 */
	public function registerMapping(Configuration $config)
	{
		$driverChain = new DriverChain;
		$aliasMap = array();

		if ($this->isLoaded === false)
		{
			$this->loadMappingInfo($this->mappingInfo, $this->autoMapping);
		}

		foreach ($this->mappingInfo as $mappingName => $mappingConfig)
		{
			if ($mappingConfig['type'] === 'annotation')
			{
				$driver = $config->newDefaultAnnotationDriver($mappingConfig['dir']);
				// Annotations are needed to be registered, thanks Doctrine
				// $driver = new AnnotationDriver(
				// 	new CachedReader(
				// 		new AnnotationReader,
				// 		$config->getMetadataCacheImpl()
				// 	),
				// 	$mappingConfig['dir']
				// );
			}
			else
			{
				$driver = $this->createMetadataDriver($mappingConfig['type'], $mappingConfig['dir']);
			}

			if (empty($mappingConfig['prefix']) or count($this->mappingInfo) === 1)
			{
				$driverChain->setDefaultDriver($driver);
			}
			else
			{
				$driverChain->addDriver($driver, $mappingConfig['prefix']);
			}

			if (isset($mappingConfig['alias']))
			{
				$aliasMap[$mappingConfig['alias']] = $mappingConfig['prefix'];
			}
		}

		$config->setMetadataDriverImpl($driverChain);
		$config->setEntityNamespaces($aliasMap);
	}


	/**
	 * Creates a new Mapping Driver object
	 *
	 * @param string       $driver Driver name or class name
	 * @param string|array $path   Path used for metadata driver
	 *
	 * @return Doctrine\Common\Persistence\Mapping\Driver\MappingDriver
	 */
	public static function createMetadataDriver($driver, $path)
	{
		if (array_key_exists($driver, static::$drivers))
		{
			$class = 'Doctrine\\ORM\\Mapping\\Driver\\' . static::$drivers[$driver];

			return new $class($path);
		}

		throw new ORMException('Invalid metadata driver: ' . $driver);
	}
}
