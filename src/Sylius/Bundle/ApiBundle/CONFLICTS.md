# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

- `gedmo/doctrine-extensions:3.17.0`:

  This version has a bug, which on Symfony 6.4 leads to a fatal error:
  `[Semantical Error] The annotation "@note" in property Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry::$data was never imported.`
