How to add custom filters on the promotion actions?
========================================================

Promotion actions such as "Item fixed discount" and "Item percentage discount" have built-in filters: by price range, by taxons and by products

You can add your own filters, for example, if you've added a brand to your products and would like to apply a discount to a particular brand.

See what steps need to be taken to achieve that:

Create a new action filter
--------------------------

You will need a new class ``ProductVariantFilter`` that implements the ``Sylius\Component\Core\Promotion\Filter\FilterInterface`` interface.

The `filter` method will check that items match the criteria from the `$configuration` and returns only those that do.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Promotion\Filter;

    use Sylius\Component\Core\Model\ProductVariantInterface;
    use Sylius\Component\Core\Promotion\Filter\FilterInterface;

    final class ProductVariantFilter implements FilterInterface
    {
        public function filter(array $items, array $configuration): array
        {
            if (empty($configuration['filters']['product_variant_filter']['product_variants'])) {
                return $items;
            }

            $filteredItems = [];
            foreach ($items as $item) {
                if ($this->hasProductValidTaxon($item->getProduct(), $configuration['filters']['product_variant_filter']['product_variants'])) {
                    $filteredItems[] = $item;
                }
            }

            return $filteredItems;
        }

        /**
         * @param string[] $variantCodes
         */
        private function hasProductWithProductVariant(ProductInterface $product, array $variantCodes): bool
        {
            foreach ($product->getVariants() as $variant) {
                if (in_array($variant->getCode(), $variantCodes, true)) {
                    return true;
                }
            }

            return false;
        }
    }

And configure it in the ``config/services.yaml``:

.. code-block:: yaml

    # config/services.yaml
    services:
        App\Promotion\Filter\ProductVariantFilter:
            tags: ['sylius.promotion_filter']

Prepare a configuration form type for your new filter
-----------------------------------------------------

To be able to configure a promotion action with your new filter you will need a form type for the admin panel.

Create the configuration form type class:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Form\Type\Promotion\Filter;

    use App\Promotion\Filter\ProductVariantFilter;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;

    final class ProductVariantFilterConfigurationType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
           $builder->add('variant', ProductVariantChoiceType, [
                'product' => $options['product'],
            ]);
        }

        public function getBlockPrefix(): string
        {
            return 'app_promotion_action_filter_product_variant_configuration';
        }
    }


And extend the promotion filter collection form type to add your new filter:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Form\Extension\Promotion;

    use App\Form\Type\Promotion\Filter\BrandFilterConfigurationType;
    use Sylius\Bundle\PromotionBundle\Form\Type\PromotionFilterCollectionType;
    use Symfony\Component\Form\AbstractTypeExtension;
    use Symfony\Component\Form\FormBuilderInterface;

    final class PromotionFilterCollectionTypeExtension extends AbstractTypeExtension
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('product_variant_filter', ProductVariantFilterConfigurationType::class, [
                'label' => false,
                'required' => false,
            ]);
        }

        public static function getExtendedTypes(): iterable
        {
            return [PromotionFilterCollectionType::class];
        }
    }
