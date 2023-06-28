# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

- `lexik/jwt-authentication-bundle: ^2.18`

  After bumping to this version ApiBundle starts failing due to requesting a non-existing `api_platform.openapi.factory.legacy` service.
  As we are not using this service across the ApiBundle we added this conflict to unlock the builds, until we investigate the problem.

- `symfony/framework-bundle:6.2.8`:

  This version is missing the service alias `validator.expression`
  which causes ValidatorException exception to be thrown when using `Expression` constraint. 

- `doctrine/orm:2.15.2`:

  This version introduced a bug, which causes the `ForeignKeyConstraintViolationException` exception to not be thrown when trying to delete a resource with a foreign key constraint.
  References: https://github.com/doctrine/orm/issues/10752

- `doctrine/orm:2.15.3`

  This version introduced a bug, causing the bulk editing not to work properly. When deleting two items at once, the second one is deleted and re-added to the database.

- `symfony/validator:5.4.25 || 6.2.12 || 6.3.1`

  This version introduced a bug, causing validation constraints to not work.
  References: https://github.com/symfony/symfony/issues/50780
