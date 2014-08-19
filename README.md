# Fuel Doctrine

[![Build Status](https://travis-ci.org/indigophp/fuel-doctrine.svg?branch=develop)](https://travis-ci.org/indigophp/fuel-doctrine)
[![Latest Stable Version](https://poser.pugx.org/indigophp/fuel-doctrine/v/stable.png)](https://packagist.org/packages/indigophp/fuel-doctrine)
[![Total Downloads](https://poser.pugx.org/indigophp/fuel-doctrine/downloads.png)](https://packagist.org/packages/indigophp/fuel-doctrine)
[![License](https://poser.pugx.org/indigophp/fuel-doctrine/license.png)](https://packagist.org/packages/indigophp/fuel-doctrine)
[![Dependency Status](http://www.versioneye.com/user/projects/53f01cd113bb060798000815/badge.svg?style=flat)](http://www.versioneye.com/user/projects/53f01cd113bb060798000815)

**This package is a wrapper around [doctrine/doctrine2](https://github.com/doctrine/doctrine2) package.**


## Install

Via Composer

``` json
{
    "require": {
        "indigophp/fuel-doctrine": "@stable"
    }
}
```


## Usage

``` php
$manager = \Doctrine\Manager::forge('default');

$em = $manager->getEntityManager();
```


## Configuration

To make it work, you need the following `doctrine` configuration.

``` php
	'dbal'                        => 'default',
	'proxy_dir'                   => '/tmp',
	'proxy_namespace'             => 'PrOxYnAmEsPaCe',
	'auto_generate_proxy_classes' => true,
	'mappings'                    => array(
		'mapping' => array(
			'type'   => 'xml',
			'dir'    => '/mypath',
			'prefix' => 'MyPrefix',
		),
	),
	'cache_driver'                => 'array',
```

You can also use the `Setup` class to auto configure the `Configuration` object.

``` php
	'dbal'            => 'default',
	'auto_config'     => true,
	'dev_mode'        => \Fuel::$env === \Fuel::DEVELOPMENT,
	'proxy_dir'       => '/tmp',
	'cache_driver'    => 'array',
```


### Multiple managers

By default you have one manager (`default`). If you would like use multiple managers, you have to add a key `managers` to your doctrine config, and set your configurations there. You can also set global configurations in the config root. Make sure to set `auto_mapping` to `false`.

``` php
	'auto_mapping'    => false,
	'dbal'            => 'default',
	'managers'        => array(
		'default'   => array(),
		'aditional' => array()
	),
```


**Note:** This package uses [indigophp/fuel-dbal](https://github.com/indigophp/fuel-dbal) for connections. Check the package documentation.


## Running `doctrine` commands

Doctrine comes with a CLI tool by default, however it is a bit hard use it the official way (`cli-config.php` in the project root folder). So I wrapped it in an `oil` command. It is working, but it is still just a hack ("oil" and "r" or "refine" are just removed from the argument list), so use it with caution.

Example:
``` bash
oil r doctrine orm:schema-tool:drop --force
oil r doctrine orm:schema-tool:create
```

General syntax:
``` bash
oil r doctrine [command]
```

If you want to use a specific Manager instance put a DB env var before the command:
``` bash
DB=my_doctrine_instance oil r doctrine [command]
```

**Note:** Make sure the `doctrine` package is loaded in fuel otherwise the task will not work.


## Testing

``` bash
$ codecept run
```


## Contributing

Please see [CONTRIBUTING](https://github.com/indigophp/fuel-doctrine/blob/develop/CONTRIBUTING.md) for details.


## Credits

- [Márk Sági-Kazár](https://github.com/sagikazarmark)
- [aspendigital](https://github.com/aspendigital/fuel-doctrine2)
- [All Contributors](https://github.com/indigophp/fuel-doctrine/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/indigophp/fuel-doctrine/blob/develop/LICENSE) for more information.
