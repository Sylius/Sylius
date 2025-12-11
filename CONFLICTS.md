# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and references related issues.

- `api-platform/jsonld: ^4.1.1`

  API Platform introduced changes in version 4.1.1 that modify API responses, potentially breaking compatibility with our current implementation.  
  To ensure stable behavior, we have added this conflict until we can verify and adapt to the changes.

- `behat/gherkin:^4.13.0`:

  This version moved files to flatten paths into a PSR-4 structure, which lead to a fatal error:
  `PHP Fatal error:  Uncaught Error: Failed opening required '/home/runner/work/Sylius/Sylius/vendor/behat/gherkin/src/../../../i18n.php' (include_path='.:/usr/share/php') in /home/runner/work/Sylius/Sylius/vendor/behat/gherkin/src/Keywords/CachedArrayKeywords.php:34`

- `symfony/ux-live-component:2.28.0||2.28.1||^2.29`:

  The versions 2.28.0 and 2.28.1 throws a MethodNotAllowedException during using live components.
  Since the version 2.29 the behavior of UrlFactory::createFromPreviousAndProps method has been changed that unmatches the previous one.

- `api-platform/metadata:>=4.1.0 <4.2.0` and `api-platform/serializer:>=4.1.0 <4.2.0`:

  In API Platform 4.1.24+, the `api-platform/serializer` package calls `getNativeType()` method on
  `ApiPlatform\Metadata\ApiProperty` which doesn't exist in the `api-platform/metadata` package of the
  same 4.1.x branch, causing an "undefined method" error when custom serializers are used with API Platform's
  `ObjectNormalizer`.

  Error: `Attempted to call an undefined method named "getNativeType" of class "ApiPlatform\Metadata\ApiProperty".`

  This is a version mismatch issue between API Platform 4.1.x packages. The conflict excludes the entire 4.1.x
  series, allowing only 4.0.x (stable, without this bug) or 4.2.x+ (where all packages are consistent).

  Related issues:
    - https://github.com/api-platform/core/issues/7404
    - https://github.com/api-platform/api-platform/issues/2934
