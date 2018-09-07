# Changelog

## [1.1.1] - 2018-09-07

### Fixed

- Server error 500 while trying to warmup cache due to `Uncaught Error: Call to undefined method Contao\\ManagerBundle\\HttpKernel\\ContaoCache::getProjectDir() ` while invoking `config_encore.yml`

## [1.1.0] - 2018-08-06

#### Added
* encore support
* removed jquery dependency

## [1.0.1] - 2018-08-06

#### Fixed 
* now correct absolute url is generated for other root pages

## [1.0.0] - 2018-03-29

* Moved to bundle structure
* some refactoring
* Added tests
* updated readme
