# UPGRADE FROM `2.2` TO `2.3`

## Dependencies

1. The `knplabs/gaufrette` and `knplabs/knp-gaufrette-bundle` packages have been removed.

   The Gaufrette integration has been unusable as a filesystem adapter.
   Since Sylius 2.0 the default filesystem adapter uses Flysystem instead. 

   If your application depends on the Gaufrette packages directly, require them explicitly in your `composer.json`.
