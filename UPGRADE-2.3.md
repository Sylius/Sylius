# UPGRADE FROM `2.2` TO `2.3`

## Dependencies

1. The `behat/transliterator` package has been removed from the core dependencies.

   All slug generation now uses `symfony/string` (`Symfony\Component\String\Slugger\SluggerInterface`) instead of `Behat\Transliterator\Transliterator`.

   The following classes have been updated — if you have extended or decorated them, update your constructor accordingly:

   - `Sylius\Component\Product\Generator\SlugGenerator`:

     ```diff
     -public function __construct()
     +public function __construct(private SluggerInterface $slugger)
     ```

   - `Sylius\Component\Taxonomy\Generator\TaxonSlugGenerator`:

     ```diff
     -public function __construct()
     +public function __construct(private SluggerInterface $slugger)
     ```

   - `Sylius\Bundle\AdminBundle\Generator\TaxonSlugGenerator`:

     ```diff
      public function __construct(
          private BaseTaxonSlugGeneratorInterface $slugGenerator,
     +    private SluggerInterface $slugger,
      )
     ```

   The `StringInflector::nameToSlug()` method has been removed from `Sylius\Component\Core\Formatter\StringInflector`.
   Use the `slugger` service directly instead:

   ```diff
   -use Sylius\Component\Core\Formatter\StringInflector;
   +use Symfony\Component\String\Slugger\SluggerInterface;

   -$slug = StringInflector::nameToSlug($value);
   +$slug = $this->slugger->slug($value)->lower()->toString();
   ```

2. The `knplabs/gaufrette` and `knplabs/knp-gaufrette-bundle` packages have been removed.

   The Gaufrette integration has been unusable as a filesystem adapter.
   Since Sylius 2.0 the default filesystem adapter uses Flysystem instead. 

   If your application depends on the Gaufrette packages directly, require them explicitly in your `composer.json`.
