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
$em = \Doctrine\Manager::forge('default');
```

To make it work, you need the following `doctrine` configuration.

``` php
'default' => array(
	'dbal'                        => 'default',
	'proxy_dir'                   => '/tmp',
	'proxy_namespace'             => 'PrOxYnAmEsPaCe',
	'auto_generate_proxy_classes' => true,
	'metadata_path'               => '',
	'metadata_driver'             => 'xml',
	'cache_driver'                => 'array',
),
```

You can also use the `Setup` class to auto configure the `Configuration` object.

``` php
	'default' => [
		'dbal'            => 'default',
		'auto_config'     => true,
		'dev_mode'        => \Fuel::$env === \Fuel::DEVELOPMENT,
		'proxy_dir'       => '/tmp',
		'metadata_path'   => '',
		'metadata_driver' => 'xml',
		'cache_driver'    => 'array',
	],
```


**Note:** this package uses [indigophp/fuel-dbal](https://github.com/indigophp/fuel-dbal) for connections. Check the package documentation.


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
