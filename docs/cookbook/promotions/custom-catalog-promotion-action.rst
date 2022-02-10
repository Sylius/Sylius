How to add a custom catalog promotion action?
=============================================

Adding a new, custom catalog promotion action to your shop may become a quite helpful extension to your own Catalog Promotions.
You can create your own calculator tailored to your product catalog to attract as many people as possible.

Let's try to implement the new **Catalog Promotion Action** in this cookbook that will lower the price of the product
or product variant to a specific value.

.. note::

    If you are familiar with **Cart Promotions** and you know how **Cart Promotion Actions** work,
    then the Catalog Promotion Action should look familiar, as the concept of them is quite similar.

Create a new catalog promotion action
-------------------------------------

We should start from creating a calculator that will return a proper price for given channel pricing. Let's declare the service:

.. code-block:: yaml

    # config/services.yaml

     App\Calculator\FixedPriceCalculator:
        tags:
            - { name: 'sylius.catalog_promotion.price_calculator', type: 'fixed_price' }

.. note::

    Please take a note on a declared tag of calculator, it is necessary for this service to be taken into account.

And the code for the calculator itself:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Calculator;

    use App\Model\CatalogPromotionActionInterface;
    use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\ActionBasedPriceCalculatorInterface;
    use Sylius\Component\Core\Model\ChannelPricingInterface;
    use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface as BaseCatalogPromotionActionInterface;

    final class FixedPriceCalculator implements ActionBasedPriceCalculatorInterface
    {
        public const TYPE = 'fixed_price';

        public function supports(BaseCatalogPromotionActionInterface $action): bool
        {
            return $action->getType() === self::TYPE;
        }

        public function calculate(ChannelPricingInterface $channelPricing, BaseCatalogPromotionActionInterface $action): int
        {
            if (!isset($action->getConfiguration()[$channelPricing->getChannelCode()])) {
                return $channelPricing->getPrice();
            }

            $price = $action->getConfiguration()[$channelPricing->getChannelCode()]['price'];

            $minimumPrice = $this->provideMinimumPrice($channelPricing);
            if ($price < $minimumPrice) {
                return $minimumPrice;
            }

            return $price;
        }

        private function provideMinimumPrice(ChannelPricingInterface $channelPricing): int
        {
            if ($channelPricing->getMinimumPrice() <= 0) {
                return 0;
            }

            return $channelPricing->getMinimumPrice();
        }
    }

Now the catalog promotion should work with your new action for resources created both programmatically and via API.
Let's now prepare a custom validator for the newly created action.

Prepare a custom validator for the new action
---------------------------------------------

We can start with configuration, declare our basic validator for this particular action:

.. code-block:: yaml

    # config/services.yaml

    App\Validator\CatalogPromotionAction\FixedPriceActionValidator:
        arguments:
            - '@sylius.repository.channel'
        tags:
            - { name: 'sylius.catalog_promotion.action_validator', key: 'fixed_price' }

In this validator, we will check the provided configuration for necessary data and if the configured channels exist.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Validator\CatalogPromotionAction;

    use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionAction\ActionValidatorInterface;
    use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionAction;
    use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
    use Symfony\Component\Validator\Constraint;
    use Symfony\Component\Validator\Context\ExecutionContextInterface;
    use Webmozart\Assert\Assert;

    final class FixedPriceActionValidator implements ActionValidatorInterface
    {
        private ChannelRepositoryInterface $channelRepository;

        public function __construct(ChannelRepositoryInterface $channelRepository)
        {
            $this->channelRepository = $channelRepository;
        }

        public function validate(array $configuration, Constraint $constraint, ExecutionContextInterface $context): void
        {
            /** @var CatalogPromotionAction $constraint */
            Assert::isInstanceOf($constraint, CatalogPromotionAction::class);

            if (empty($configuration)) {
                $context->buildViolation('There is no configuration provided.')->atPath('configuration')->addViolation();

                return;
            }

            foreach ($configuration as $channelCode => $channelConfiguration) {
                if (null === $this->channelRepository->findOneBy(['code' => $channelCode])) {
                    $context->buildViolation('The provided channel is not valid.')->atPath('configuration')->addViolation();

                    return;
                }
            }
        }
    }

Alright, we have a working basic validation, and our new type of action exists, can be created, and edited
programmatically or by API. Let's now prepare the UI part of this new feature.

Prepare a configuration form type for the new action
----------------------------------------------------

To be able to configure a catalog promotion with your new action you will need a form type for the admin panel.
And with the current implementation, as our action is channel-based, you need to create 2 form types as below:

.. code-block:: yaml

    # config/services.yaml

    App\Form\Type\CatalogPromotionAction\ChannelBasedFixedPriceActionConfigurationType:
        tags:
            - { name: 'sylius.catalog_promotion.action_configuration_type', key: 'fixed_price' }
            - { name: 'form.type' }

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Form\Type\CatalogPromotionAction;

    use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Comp§onent\Validator\Constraints\GreaterThan;
    use Symfony\Component\Validator\Constraints\NotBlank;

    final class FixedPriceActionConfigurationType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('price', MoneyType::class, [
                    'label' => 'Price',
                    'currency' => $options['currency'],
                    'constraints' => [
                        new NotBlank([
                            'groups' => 'sylius',
                            'message' => 'Price needs to be set',
                        ]),
                        new GreaterThan([
                            'value' => 0,
                            'groups' => 'sylius',
                            'message' => 'Price cannot be lower than 0',
                        ]),
                    ],
                ])
            ;
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver
                ->setRequired('currency')
                ->setAllowedTypes('currency', 'string')
            ;
        }

        public function getBlockPrefix(): string
        {
            return 'app_catalog_promotion_action_fixed_price_configuration';
        }
    }

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Form\Type\CatalogPromotionAction;

    use Sylius\Bundle\CoreBundle\Form\Type\ChannelCollectionType;
    use Sylius\Component\Core\Model\ChannelInterface;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    final class ChannelBasedFixedPriceActionConfigurationType extends AbstractType
    {
        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'entry_type' => FixedPriceActionConfigurationType::class,
                'entry_options' => function (ChannelInterface $channel) {
                    return [
                        'label' => $channel->getName(),
                        'currency' => $channel->getBaseCurrency()->getCode(),
                    ];
                },
            ]);
        }

        public function getParent(): string
        {
            return ChannelCollectionType::class;
        }
    }

And define the translation for our new action type:

.. code-block:: yaml

    # translations/messages.en.yaml

    sylius:
        form:
            catalog_promotion:
                action:
                    fixed_price: 'Fixed price'

.. note::
    There is a need to define translation key in the proper format for every catalog promotion action as they are used in form types
    to properly display different actions. The required type is: ``sylius.form.catalog_promotion.action.TYPE`` where ``TYPE`` is the catalog promotion action type.

Prepare an action template for show page of catalog promotion
-------------------------------------------------------------

The last thing is to create a template to display our new action properly. Remember to name it the same as the action type.

.. code-block:: html+twig

    {# templates/bundles/SyliusAdminBundle/CatalogPromotion/Show/Action/fixed_price.html.twig #}

    {% import "@SyliusAdmin/Common/Macro/money.html.twig" as money %}

    <table class="ui very basic celled table">
        <tbody>
        <tr>
            <td class="five wide"><strong class="gray text">Type</strong></td>
            <td>Fixed price</td>
        </tr>
        {% set currencies = sylius_channels_currencies() %}
        {% for channelCode, channelConfiguration in action.configuration %}
            <tr>
                <td class="five wide"><strong class="gray text">{{ channelCode }}</strong></td>
                <td>{{ money.format(channelConfiguration.price, currencies[channelCode]) }}</td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

That's all. You will now be able to choose the new action while creating or editing a catalog promotion.

Learn more
----------

* :doc:`Customization Guide </customization/index>`
* :doc:`Catalog Promotion Concept Book </book/products/catalog_promotions>`
