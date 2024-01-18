# Changelog

## [Unreleased] - 2024-01-18
- Changed: implement encore contracts
- Changed: some code refactoring
- Fixed: edit module not working

## [1.4.0] - 2022-09-30
- Fixed: js
- Updated: add entry automatically

## [1.3.2] - 2020-07-22
- fixed non-root pages leading to exception

## [1.3.1] - 2020-04-17
- fixed root page url was used instead of index page url

## [1.3.0] - 2020-02-17
- fixed wrong local was used when a page is only available in another locale then the current
- update encore configuration, increased optional encore bundle dependency to 1.5

## [1.2.0] - 2019-11-26
- added migration command
- some refactoring

## [1.1.1] - 2018-09-07
- fixed Server error 500 while trying to warmup cache due to `Uncaught Error: Call to undefined method Contao\\ManagerBundle\\HttpKernel\\ContaoCache::getProjectDir() ` while invoking `config_encore.yml`

## [1.1.0] - 2018-08-06
- added encore support
- removed jquery dependency

## [1.0.1] - 2018-08-06 
- fixed now correct absolute url is generated for other root pages

## [1.0.0] - 2018-03-29
- Moved to bundle structure
- some refactoring
- Added tests
- updated readme
