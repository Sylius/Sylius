# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

- `api-platform/state: >=4.1.0, <=4.1.16`

  this packages are being installed outdated, lets force the latest version, otherwise it breaks the container validation.
  `Invalid service "api_platform.state_provider.parameter.iri_converter": class "ApiPlatform\State\ParameterProvider\IriConverterParameterProvider" doesnot exist. `

- `behat/gherkin:^4.13.0`:

  This version moved files to flatten paths into a PSR-4 structure, which lead to a fatal error:
  `PHP Fatal error:  Uncaught Error: Failed opening required '/home/runner/work/Sylius/Sylius/vendor/behat/gherkin/src/../../../i18n.php' (include_path='.:/usr/share/php') in /home/runner/work/Sylius/Sylius/vendor/behat/gherkin/src/Keywords/CachedArrayKeywords.php:34`
