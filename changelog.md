# Changelog
All notable changes to the Annotaction project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.2.3] - 2018-09-05
### Fixed
- Check if annotation exists in Loader (allows for non-Action classes to live in same place)

## [0.2.2] - 2018-08-14
### Changed
- Move Tests PSR-4 namespace to autoload-dev

## [0.2.1] - 2018-08-14
### Added
- Travis CI config for automated testing
- .gitattributes file for removing unneeded files on production downloads

### Changed
- Removed Request object from Action stub

## [0.2.0] - 2018-08-11
### Added
- Create Action directory on boot if it doesn't exist

## [0.1.0] - 2018-08-11
- Initial beta launch
