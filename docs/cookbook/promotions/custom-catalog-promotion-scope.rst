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

     App\Provider\ByPhraseVariantsProvider:
        arguments:
            - '@sylius.repository.product_variant'
        tags:
            - { name: 'sylius.catalog_promotion.variants_provider', type: 'by_phrase' }

.. note::

    Please take a note on tag of Provider, thanks to it those services are working properly.

And the code for the provider itself:

.. code-block:: php

    <?php

    namespace App\Provider;

    use Sylius\Bundle\CoreBundle\Provider\VariantsProviderInterface;
    use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
    use Webmozart\Assert\Assert;
    use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;

    class ByPhraseVariantsProvider implements VariantsProviderInterface
    {
        public const TYPE = 'by_phrase';

        private ProductVariantRepositoryInterface $productVariantRepository;

        public function __construct(ProductVariantRepositoryInterface $productVariantRepository)
        {
            $this->productVariantRepository = $productVariantRepository;
        }

        public function supports(CatalogPromotionScopeInterface $catalogPromotionScopeType): bool
        {
            return $catalogPromotionScopeType->getType() === self::TYPE;
        }

        public function provideEligibleVariants(CatalogPromotionScopeInterface $scope): array
        {
            $configuration = $scope->getConfiguration();
            Assert::keyExists($configuration, 'phrase', 'This rule should have configured phrase');

            return $this->productVariantRepository->findByPhrase($configuration['phrase'], 'en_US');
        }
    }

.. note::

    In this example there is hardcoded locale in ``->findByPhrase($configuration['amount'], 'en_US')`` but you can use LocaleContextInterface
    or extend the code from this cookbook to e.g. consume key ``localeCode`` from configuration.

Now the Catalog Promotion should work with your new Scope for programmatically and API created resource.

Prepare a custom validator for the new scope
--------------------------------------------

We can start with configuration, declare our basic validator for this particular scope:

.. code-block:: yaml

    # config/services.yaml

    App\Validator\CatalogPromotionScope\ByPhraseScopeValidator:
        tags:
            - { name: 'sylius.catalog_promotion.scope_validator', key: 'by_phrase' }

In this validator we will check only the case for the ``phrase`` key to exist. But you can also extend it with your own
keys to check as well as their corresponding values.

.. code-block:: php

    <?php

    namespace App\Validator\CatalogPromotionScope;

    use Sylius\Bundle\CoreBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface;
    use Sylius\Bundle\CoreBundle\Validator\Constraints\CatalogPromotionScope;
    use Symfony\Component\Validator\Constraint;
    use Symfony\Component\Validator\Context\ExecutionContextInterface;
    use Webmozart\Assert\Assert;

    class ByPhraseScopeValidator implements ScopeValidatorInterface
    {
        public function validate(array $configuration, Constraint $constraint, ExecutionContextInterface $context): void
        {
            /** @var CatalogPromotionScope $constraint */
            Assert::isInstanceOf($constraint, CatalogPromotionScope::class);

            if (!array_key_exists('phrase', $configuration) || empty($configuration['phrase'])) {
                $context->buildViolation('There is no phrase provided')->atPath('configuration.phrase')->addViolation();
            }
        }
    }

Alright, we have a working basic validation, and our new type of scope exists, can be created, and edited
programmatically or by API. Let's now prepare the UI part of this new feature.

Prepare a configuration form type for your new scope
----------------------------------------------------

To be able to configure a Catalog Promotion with your new Scope you will need a form type for the admin panel.
With current implementation first you need to create a twig template for new Scope:

.. code-block:: html

    # templates/bundles/SyliusAdminBundle/CatalogPromotion/Scope/by_phrase.html.twig

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

    class ByPhraseScopeConfigurationType extends AbstractType
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
