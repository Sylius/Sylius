# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

- `lexik/jwt-authentication-bundle: ^2.18`

  After bumping to this version ApiBundle starts failing due to requesting a non-existing `api_platform.openapi.factory.legacy` service.
  As we are not using this service across the ApiBundle we added this conflict to unlock the builds, until we investigate the problem.

- `symfony/framework-bundle:6.2.8`:

  This version is missing the service alias `validator.expression`
  which causes ValidatorException exception to be thrown when using `Expression` constraint. 
