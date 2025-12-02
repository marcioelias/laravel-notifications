# Changelog

All notable changes to `laravel-notifications` will be documented in this file.

## [Unreleased]

## [1.8.2] - 2025-01-XX

### Fixed
- Fixed custom fields migration tag not appearing in `vendor:publish` command
- Moved migration publishing from `boot()` to `bootingPackage()` to ensure early registration
- Improved tag registration timing for better compatibility with Laravel's publish command

## [1.8.1] - 2025-01-XX

### Added
- Automatic detection of Tenancy for Laravel package
- Configurable migrations directory via `migrations_path` config option
- Environment variable `NOTIFICATIONS_MIGRATIONS_PATH` for custom migrations directory
- Support for publishing custom fields migration to tenant directories

### Fixed
- Fixed custom fields migration tag registration issue
- Improved migration publishing to work correctly with `vendor:publish` command

## [1.8.0] - 2025-01-XX

### Added
- Support for custom fields in notifications table
- Method `fillCustomFields()` in Notification model to dynamically add custom fields
- Migration stub for custom fields that can be published by users
- Parameter `customFields` in `sendPush()` method to pass additional data
- Tests for custom fields functionality
- Laravel 12 compatibility

### Changed
- Modified `storeNotification()` method to accept and save custom fields
- Updated Notification model to use `$guarded` instead of `$fillable` to allow dynamic fields
- Updated `illuminate/contracts` to support Laravel 12
- Updated `orchestra/testbench` to support Laravel 12 testing
- Updated CI workflow to test against Laravel 11 and Laravel 12
