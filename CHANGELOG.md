## 4.2.0 - 2023-08-14

### Changed
* Added unit tests & removed laminas/laminas-log dependency

### Added
* Unit Tests

### Deprecated
* Nothing

### Removed
* laminas/laminas-log dependency

### Fixed
* Decoupled packages from laminas/laminas-log


## 4.0.1 - 2022-05-31

### Changed
* Updated FormAbstractServiceFactory to implement Laminas\Log\AbstractFactoryInterface

### Added
* Package require laminas/laminas-log

### Deprecated
* Nothing

### Removed
* Nothing

### Fixed
* PHP 8.1 returned error for FormAbstractServiceFactory implements AbstractFactoryInterface


## 4.0.0 - 2022-02-04

### Changed
* Backward compatibility with dotkernel/dot-form version 3.x is broken due to laminas-form update to version 3.x
* Updated composer.json to require laminas/laminas-form version ^3.1.1 due to security vulnerability
* Increase the PHP version to 8.1

### Added
* Nothing

### Deprecated
* Nothing

### Removed
* Nothing

### Fixed
* Nothing


## 3.2.2 - 2022-02-02

### Changed
* Handling of laminas/laminas-form package

### Added
* Full implementation for Laminas\ServiceManager\Factory\AbstractFactoryInterface

### Deprecated
* Nothing

### Removed
* Extension for Laminas\Form\FormElementManager\FormElementManagerV3Polyfill (no longer present in laminas-form package)

### Fixed
* Replaced extension of laminas-form with implementation for Laminas\ServiceManager\Factory\AbstractFactoryInterface


## 3.2.1 - 2022-02-01

### Changed
* Updated composer.json to require laminas/laminas-form version ^3.1.1 due to security vulnerability

### Added
* Nothing

### Deprecated
* Nothing

### Removed
* Nothing

### Fixed
* Nothing


## 3.2.0 - 2020-11-03

### Changed
* Composer 2 compatibility.
* Update composer.json to require version 2 of laminas/laminas-dependency-plugin to be Composer 2 compatible.

### Added
* Nothing

### Deprecated
* Nothing

### Removed
* Nothing

### Fixed
* bump min version of laminas/laminas-dependency-plugin
* bump min version of laminas/laminas-form

## 2.0.0 - 2020-01-30

### Changed
* Laminas and Mezzio migration.

### Added
* Nothing

### Deprecated
* Nothing

### Removed
* Support

### Fixed
* Nothing


## 1.1.1 - 2018-05-14

### Changed
* updated dependencies

### Added
* Nothing

### Deprecated
* Nothing

### Removed
* Nothing

### Fixed
* Nothing


## 1.1.0 - 2017-03-15

### Changed
* updated factories to use PSR11 container

### Added
* Nothing

### Deprecated
* Nothing

### Removed
* Nothing

### Fixed
* Nothing


## 1.0.1 - 2017-03-11

Updated php file headers doc blocks

### Added
* Nothing

### Deprecated
* Nothing

### Removed
* Nothing

### Fixed
* Nothing


## 1.0.0 - 2017-03-09

Initial tagged release

### Added
* Everything

### Deprecated
* Nothing

### Removed
* Nothing

### Fixed
* Nothing
