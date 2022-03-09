How to add a custom catalog promotion scope?
============================================

Adding a new, custom catalog promotion scope to your shop may become a quite helpful extension to your own Catalog Promotions.
You can imagine for instance, that you have some custom way of aggregating products, or any other method of filtering them.
These products that will fulfill your specific scope will become eligible for actions of Catalog Promotion, and as we know
cheaper Products attract more customers.
Let's try implementing the new **Catalog Promotion Scope** in this cookbook, that will work with Products that contains a phrase.

.. note::

    If you are familiar with **Cart Promotions** and you know how **Cart Promotion Rules** work,
    then the Catalog Promotion Scope should look familiar, as the concept of them is quite similar.

Create a new catalog promotion scope
------------------------------------

We should start from creating a provider that will return for us all of eligible product variants. Let's declare the service:

.. code-block:: yaml

    # config/services.yaml

    App\Checker\InByPhraseScopeVariantsChecker:
        arguments:
            - '@sylius.repository.product_variant'
        tags:
            - { name: 'sylius.catalog_promotion.variant_checker', type: 'by_phrase' }

.. note::

    Please take a note on tag of Checker, thanks to it those services are working properly.

And the code for the checker itself:

.. code-block:: php

    <?php

    namespace App\Checker;

    use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
    use Webmozart\Assert\Assert;
    use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;

    class InByPhraseScopeVariantsChecker implements VariantInScopeCheckerInterface
    {
        public const TYPE = 'by_phrase';

        private ProductVariantRepositoryInterface $productVariantRepository;

        public function __construct(ProductVariantRepositoryInterface $productVariantRepository)
        {
            $this->productVariantRepository = $productVariantRepository;
        }

        public function inScope(CatalogPromotionScopeInterface $scope, ProductVariantInterface $productVariant): bool
        {
            $configuration = $scope->getConfiguration();
            Assert::keyExists($configuration, 'phrase', 'This scope should have configured phrase');

            return str_contains($productVariant->getName(), $configuration['phrase']);
        }
    }

Now the Catalog Promotion should work with your new Scope for programmatically and API created resource.

Validation
----------

As your new Scope requires only basic syntactical validation, it's recommended to configure it on the Form type, rather
than in the custom validator.

Prepare a configuration form type for your new scope
----------------------------------------------------

To be able to configure a Catalog Promotion with your new Scope you will need a form type for the admin panel.
With current implementation first you need to create a twig template for new Scope:

.. code-block:: twig

    {# templates/bundles/SyliusAdminBundle/CatalogPromotion/Scope/by_phrase.html.twig #}

    {% form_theme field '@SyliusAdmin/Form/theme.html.twig' %}

    {{ form_row(field.phrase, {}) }}

Now let's create a form type and declare it service:

.. code-block:: yaml

    # config/services.yaml

    App\Form\Type\CatalogPromotionScope\ByPhraseScopeConfigurationType:
        arguments:
            - '@sylius.repository.product_variant'
        tags:
            - { name: 'sylius.catalog_promotion.scope_configuration_type', key: 'by_phrase' }
            - { name: 'form.type' }

.. code-block:: php

    <?php

    namespace App\Form\Type\CatalogPromotionScope;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Validator\Constraints\NotBlank;

    final class ByPhraseScopeConfigurationType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('phrase', TextType::class, [
                'label' => 'Phrase',
                'constraints' => [
                    new NotBlank(['groups' => ['sylius']]),
                ],
            ]);
        }

        public function getBlockPrefix(): string
        {
            return 'sylius_catalog_promotion_scope_by_phrase_configuration';
        }
    }

And with current implementation, there is also a need to override a ``default.html.twig`` template with key that is first in alphabetical order.
In our case - we have a template ``by_phrase.html.twig`` which is first before out of the box ``for_products``, ``for_variants`` and ``for_taxons`` templates:

.. code-block:: html+twig

    {# templates/bundles/SyliusAdminBundle/CatalogPromotion/Scope/default.html.twig #}

    {% include 'bundles/SyliusAdminBundle/CatalogPromotion/Scope/by_phrase.html.twig' %}

.. note::

    This overriding will be suspect of change, so there won't be need for declaring ``default.html.twig`` template anymore.

.. note::
    There is a need to define translation key in the proper format for every catalog promotion scope as they are used in form types
    to properly display different scopes. The required type is: ``sylius.form.catalog_promotion.scope.TYPE`` where ``TYPE`` is the catalog promotion scope type.

Prepare a scope template for show page of catalog promotion
-----------------------------------------------------------

The last thing is to create a template to display our new scope properly. Remember to name it the same as the scope type.

.. code-block:: html+twig

    {# templates/bundles/SyliusAdminBundle/CatalogPromotion/Show/Scope/by_phrase.html.twig #}

    <table class="ui very basic celled table">
        <tbody>
        <tr>
            <td class="five wide"><strong class="gray text">Type</strong></td>
            <td>By phrase</td>
        </tr>
        <tr>
            <td class="five wide"><strong class="gray text">Phrase</strong></td>
            <td>{{ scope.configuration.phrase }}</td>
        </tr>
        </tbody>
    </table>


That's all. You will now be able to choose the new scope while creating or editing a catalog promotion.

Learn more
----------

* :doc:`Customization Guide </customization/index>`
* :doc:`Catalog Promotion Concept Book </book/products/catalog_promotions>`
