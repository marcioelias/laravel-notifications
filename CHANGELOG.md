# Changelog

All notable changes to `laravel-notifications` will be documented in this file.

## [Unreleased]

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
