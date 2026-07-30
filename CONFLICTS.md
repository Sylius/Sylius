# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and references related issues.

- `doctrine/orm:2.20.7`:

  This version contains a regression that breaks queries with empty arrays, causing SQL syntax errors when methods like `EntityRepository::findById([])` are called with an empty array.
  This leads to invalid SQL queries like `WHERE t0.id IN ()`.
  The same regression affects 3.5.3, but that release is already excluded by the minimum requirement `^3.6`, so only 2.20.7 (reachable through `^2.20`) needs to be listed here.

  References: https://github.com/doctrine/orm/issues/12245
