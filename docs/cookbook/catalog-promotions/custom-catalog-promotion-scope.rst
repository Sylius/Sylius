How to add a custom catalog promotion scope?
============================================

Adding a new, custom catalog promotion scope to your shop should become a quite helpful extension to your own Catalog Promotions.
You can imagine for instance, that you have some custom way of aggregating products, or any other method of filtering them.
These products that will fulfill your specific scope will become eligible for actions of Catalog Promotion, and as we know
cheaper Products attract more customers.
Let's try implementing the new **Catalog Promotion Scope** in this cookbook, that will work with Products that contains a phrase.

.. note::

    If you are familiar with **Cart Promotions** and you know how **Cart Promotion Rules** work,
    than the Catalog Promotion Scope should look familiar, as the concept of them is quite similar.

Create a new catalog promotion scope
------------------------------------

The new Scope needs to be declared somewhere, it would be nice to extend the current interface first:

.. code-block:: php

    <?php

    namespace App\Model;

    use Sylius\Component\Core\Model\CatalogPromotionScopeInterface as BaseCatalogPromotionScopeInterface;

    interface CatalogPromotionScopeInterface extends BaseCatalogPromotionScopeInterface
    {
        public const TYPE_BY_PHRASE = 'by_phrase';
    }

Now lets declare the basic validator service, with added additional Scope. This first validation is necessary to check
if the Scope is added to Catalog Promotion and also if the mandatory key fields exist. We will also declare a more
atomic validator for our Scope:

.. code-block:: yaml

    # config/services.yaml

    Sylius\Bundle\CoreBundle\Validator\Constraints\CatalogPromotionScopeValidator:
        arguments:
            - [
                !php/const Sylius\Component\Core\Model\CatalogPromotionScopeInterface::TYPE_FOR_TAXONS,
                !php/const Sylius\Component\Core\Model\CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS,
                !php/const App\Model\CatalogPromotionScopeInterface::TYPE_BY_PHRASE
            ]
            - !tagged_iterator { tag: 'sylius.catalog_promotion.scope_validator', index_by: 'key' }
        tags:
            - { name: 'validator.constraint_validator', alias: 'sylius_catalog_promotion_scope'}

Also as we are in this config file we can declare our Validator for this particular Scope:

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
                $context->buildViolation("There is no phrase provided")->atPath('configuration.phrase')->addViolation();
            }
        }
    }

Alright now we have a working basic validation, and our new type Scope exists. We should now create a Provider that will return
for us all of eligible product variants. We can start with config:

.. code-block:: yaml

    # config/services.yaml

     App\Provider\ByPhraseVariantsProvider:
        arguments:
            - '@sylius.repository.product_variant'
        tags:
            - { name: 'sylius.catalog_promotion.variants_provider' }

.. note::

    Please take a note on tags of Validator and Provider, thanks to them declared those services are working properly.

And the code for the provider itself:

.. code-block:: php

    <?php

    namespace App\Provider;

    use Sylius\Bundle\CoreBundle\Provider\VariantsProviderInterface;
    use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
    use Webmozart\Assert\Assert;
    use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;

    class ByPhraseVariantsProvider implements VariantsProviderInterface
    {
        private ProductVariantRepositoryInterface $productVariantRepository;

        public function __construct(ProductVariantRepositoryInterface $productVariantRepository)
        {
            $this->productVariantRepository = $productVariantRepository;
        }

        public function supports(CatalogPromotionScopeInterface $catalogPromotionScopeType): bool
        {
            return $catalogPromotionScopeType->getType() === \App\Model\CatalogPromotionScopeInterface::TYPE_BY_PHRASE;
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
Lets now prepare a validation for UI part by using form types.

Prepare a configuration form type for your new scope
----------------------------------------------------

To be able to configure a Catalog Promotion with your new Scope you will need a form type for the admin panel.
With current implementation first you need to create a twig template for new Scope:

.. code-block:: html

    # templates/bundles/SyliusAdminBundle/CatalogPromotion/Scope/by_phrase.html.twig
    #BLA BLA CODE TODO AT THE MOMENT
    {% form_theme field '@SyliusAdmin/Form/theme.html.twig' %}

    {{ form_row(field.taxons, {'remote_url': path('sylius_admin_ajax_taxon_by_name_phrase'), 'load_edit_url': path('sylius_admin_ajax_taxon_by_code')}) }}

Now let's create a form type and declare it service:

.. code-block:: yaml

    # config/services.yaml

     App\Form\Type\CatalogPromotionScope\ByPhraseScopeConfigurationType:
        arguments:
            - '@sylius.repository.product_variant'
        tags:
            - { name: 'sylius.catalog_promotion.scope_configuration_type', key: '%sylius.catalog_promotion.scope.by_phrase%' }
            - { name: 'form.type' }

.. code-block:: php


That's all. You will now be able to choose the new Scope while creating a new Catalog Promotion.

#end todo

Learn more
----------

* :doc:`Customization Guide </customization/index>`
* :doc:`Catalog Promotion Concept Book </book/products/catalog_promotions>`
