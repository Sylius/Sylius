# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

- `api-platform/jsonld: ^4.1.1`

  API Platform introduced changes in version 4.1.1 that modify API responses, potentially breaking compatibility with our current implementation.  
  To ensure stable behavior, we have added this conflict until we can verify and adapt to the changes.
