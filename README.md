# Rootnav 

[![](https://img.shields.io/packagist/v/heimrichhannot/contao-rootnav-bundle.svg)](https://packagist.org/packages/heimrichhannot/contao-rootnav-bundle)
[![](https://img.shields.io/packagist/dt/heimrichhannot/contao-rootnav-bundle.svg)](https://packagist.org/packages/heimrichhannot/contao-rootnav-bundle)


Provides a navigation to navigate between website roots. Therefore its working best in multi domain setup.

## Features

* Frontend Module allows creating a navigation containing root pages
* [Encore Bundle](https://github.com/heimrichhannot/contao-encore-bundle) support

## Requirements

* PHP >= 7.4
* Contao >= 4.9

## Install

* Install with composer

```
composer require heimrichhannot/contao-rootnav-bundle
```
* update the database

## Usage

Just create the frontendmodule of type 'Root navigation', configure it and include it in your layout or where you want to display it. 

### Encore support

If you use this bundle together with encore bundle, you need to active `contao-rootnav-bundle` as Active entry in your page configuration, if you want to use the default mobile view.

## Migration

If you upgrade from rootnav module to rootnav bundle, please note that the frontend module type has changes. For automatic upgrade we provice a migration command do the renaming in the datasebase automatically for you.

```
php vendor/bin/contao-console huh:rootnav:migration
```

