# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and references related issues.

- `symfony/ux-live-component:2.28.0||2.28.1||^2.29`:

  The versions 2.28.0 and 2.28.1 throws a MethodNotAllowedException during using live components.
  Since the version 2.29 the behavior of UrlFactory::createFromPreviousAndProps method has been changed that unmatches the previous one.
