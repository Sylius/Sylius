# UPGRADE FROM `v1.13.X` TO `v1.14.0`

### Constructors signature changes

1. The following ProductBundle constructor signatures have been changed:

   `Sylius\Bundle\ProductBundle\Form\DataTransformer\ProductsToProductAssociationsTransformer`
    ```diff
       use Sylius\Component\Product\Repository\ProductRepositoryInterface;

        public function __construct(
            private readonly FactoryInterface $productAssociationFactory,
        -   private readonly ProductRepositoryInterface $productRepository,
        +   private readonly ?ProductRepositoryInterface $productRepository,
            private readonly RepositoryInterface $productAssociationTypeRepository,
        )
    ```

The second argument has been made optional and passing it to constructor is deprecated since Sylius 1.14 and will be prohibited in 2.0.
