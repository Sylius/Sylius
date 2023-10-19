How to add custom filters on the item promotion actions?
========================================================

The "Item percentage discount" and "Item fixed discount" promotion actions can be used to filter which products the promotion is applied to.
By default, you can filter by price, by taxon or by product.

You can also add your own filters, for example, if you've added a brand to your products and would like to apply a discount to a particular brand.

See what steps need to be taken to achieve that:

Create a new item filter
------------------------

You will need a new class ``BrandFilter`` that implements the ``Sylius\Component\Core\Promotion\Filter\FilterInterface`` interface.

The `filter` method will check that items match the criteria from the `$configuration` and returns only those that do.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Promotion\Filter;

    use Sylius\Component\Core\Model\ProductInterface;
    use Sylius\Component\Core\Promotion\Filter\FilterInterface;

    final class BrandFilter implements FilterInterface
    {
        public const EQUAL_CONDITION = 'eq';
        public const NOT_EQUAL_CONDITION = 'neq';

        public function filter(array $items, array $configuration): array
        {
            if (empty($configuration['filters']['brand_filter']['brand'])) {
                return $items;
            }

            $brandValue = $configuration['filters']['brand_filter']['brand'];
            $brandCondition = $configuration['filters']['brand_filter']['brand_condition'] ?? self::EQUAL_CONDITION;

            $filteredItems = [];
            foreach ($items as $item) {
                if ($this->checkBrandCondition($item->getProduct(), $brandValue, $brandCondition)) {
                    $filteredItems[] = $item;
                }
            }

            return $filteredItems;
        }

        private function checkBrandCondition(?ProductInterface $product, string $brandValue, string $brandCondition): bool
        {
            if (!$product instanceof \App\Entity\Product\ProductInterface) {
                return false;
            }

            return match ($brandCondition) {
                self::EQUAL_CONDITION => $brandValue === $product->getBrand(),
                self::NOT_EQUAL_CONDITION => $brandValue !== $product->getBrand(),
                default => false,
            };
        }
    }

And configure it in the ``config/services.yaml``:

.. code-block:: yaml

    # config/services.yaml
    services:
        App\Promotion\Filter\BrandFilter:
            tags: ['sylius.promotion_additional_item_filters']

Prepare a configuration form type for your new filter
-----------------------------------------------------

To be able to configure a promotion action with your new filter you will need a form type for the admin panel.

Create the configuration form type class:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Form\Type\Promotion\Filter;

    use App\Promotion\Filter\BrandFilter;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;

    final class BrandFilterConfigurationType extends AbstractType
    {
        /**
         * @SuppressWarnings(PHPMD.UnusedFormalParameters)
         */
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('brand', TextType::class, [
                    'label' => 'app.promotion_filter.brand',
                ])
                ->add('brand_condition', ChoiceType::class, [
                    'label' => 'app.promotion_filter.brand_condition.label',
                    'choices' => [
                        'app.promotion_filter.brand_condition.eq' => BrandFilter::EQUAL_CONDITION,
                        'app.promotion_filter.brand_condition.neq' => BrandFilter::NOT_EQUAL_CONDITION,
                    ],
                ])
            ;
        }

        public function getBlockPrefix(): string
        {
            return 'app_promotion_action_filter_brand_configuration';
        }
    }


And extend the promotion filter collection form type to add your new filter:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Form\Extension;

    use App\Form\Type\Promotion\Filter\BrandFilterConfigurationType;
    use Sylius\Bundle\PromotionBundle\Form\Type\PromotionFilterCollectionType;
    use Symfony\Component\Form\AbstractTypeExtension;
    use Symfony\Component\Form\FormBuilderInterface;

    final class PromotionFilterCollectionTypeExtension extends AbstractTypeExtension
    {
        /**
         * @SuppressWarnings(PHPMD.UnusedFormalParameters)
         */
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('brand_filter', BrandFilterConfigurationType::class, [
                'label' => false,
                'required' => false,
            ]);
        }

        public static function getExtendedTypes(): iterable
        {
            return [PromotionFilterCollectionType::class];
        }
    }
