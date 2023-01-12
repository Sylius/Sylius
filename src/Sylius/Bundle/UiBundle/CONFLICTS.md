# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

- `doctrine/annotations:^2.0`:

  This version results in the following error:
  `Error: Call to undefined method Doctrine\Common\Annotations\AnnotationRegistry::registerLoader()`
   when running tests with `phpunit:8.5.x`
