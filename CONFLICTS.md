# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and references related issues.

- `symfony/ux-live-component:2.28.0||2.28.1||^2.29`:

  The versions 2.28.0 and 2.28.1 throws a MethodNotAllowedException during using live components.
  Since the version 2.29 the behavior of UrlFactory::createFromPreviousAndProps method has been changed that unmatches the previous one.

- `doctrine/orm:2.20.7||3.5.3`:

  These versions contain a regression that breaks queries with empty arrays, causing SQL syntax errors when methods like `EntityRepository::findById([])` are called with an empty array.
  This leads to invalid SQL queries like `WHERE t0.id IN ()`.

  References: https://github.com/doctrine/orm/issues/12245

- `api-platform/serializer:4.2.17`:

  This version introduces a `api_platform_input` context flag (PR #7779) that causes input DTOs (command classes) to be
  denormalized through API Platform's `AbstractItemNormalizer` instead of Symfony's `ObjectNormalizer`. This exposes a
  pre-existing bug in `AbstractItemNormalizer::instantiateObject()` (missing `continue` statement) that causes only the
  first missing constructor argument to be reported instead of all of them.

  References: https://github.com/api-platform/core/pull/7779

- `api-platform/symfony:4.3.16`:

  This version registers the `api_platform.openapi.name_converter` service as a child of
  `serializer.name_converter.metadata_aware.abstract`, which does not exist on Symfony 6.4. Container compilation then
  fails in `ResolveChildDefinitionsPass` with *Parent definition "serializer.name_converter.metadata_aware.abstract" does
  not exist*, so the application cannot boot and every `bin/console` command and the whole test suite break.
  The conflict is temporary and keeps the integration on 4.3.15 until a fixed `api-platform/symfony` release is tagged.

  References: https://github.com/api-platform/core/pull/8386
