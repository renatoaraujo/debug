# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [0.5.1] - 2016-09-02
### Fixed
- Removed PHP 5.3 and 5.4 from Travis configuration file.
- Changed the PHP version requirement of composer.json from 5.3 to 5.5

### Updated 
- Changed the setUp method from DebugTest.php with correct var on line 43

### Updated

## [0.5.0] - 2016-09-01
### Added
- PHPUnit tests
- Travis CI integration
- Codeclimate integration
- Build for test coverage

### Updated
- README including Travis and Codeclimate badges
- .gitignore with directory and files to be ignored

## [0.1.0] - 2016-09-01
### Added
- Debug class with dump methods following formats (var_dump, print_r, json, log and console)