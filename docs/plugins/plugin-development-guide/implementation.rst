Implementation
--------------

The goal of our plugin is simple - we need to extend ``ProductVariant`` entity and provide new flag, that could be set
on product variant form. Following customizations uses information provided in **Sylius Customization Guide**, especially
from :doc:`customizing models</customization/model>`, :doc:`form</customization/form>` and :doc:`template</customization/template>`
pages.

Model
*****

The only field we need to add is additional ``$availableOnDemand`` boolean. Of course we should start with the unit tests (written with
PHPSpec/PHPUnit/any other unit testing tool):

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace spec\IronMan\SyliusProductOnDemandPlugin\Entity;

    use IronMan\SyliusProductOnDemandPlugin\Entity\ProductVariantInterface;
    use PhpSpec\ObjectBehavior;
    use Sylius\Component\Core\Model\ProductVariant;

    final class ProductVariantSpec extends ObjectBehavior
    {
        function it_is_sylius_product_variant(): void
        {
            $this->shouldHaveType(ProductVariant::class);
        }

        function it_implements_product_variant_interface(): void
        {
            $this->shouldImplement(ProductVariantInterface::class);
        }

        function it_can_be_available_on_demand(): void
        {
            $this->setAvailableOnDemand(true);
            $this->isAvailableOnDemand()->shouldReturn(true);
        }
    }

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace IronMan\SyliusProductOnDemandPlugin\Entity;

    use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;

    class ProductVariant extends BaseProductVariant implements ProductVariantInterface
    {
        /** @var bool */
        private $availableOnDemand;

        public function setAvailableOnDemand(?bool $availableOnDemand): void
        {
            $this->availableOnDemand = $availableOnDemand;
        }

        public function isAvailableOnDemand(): ?bool
        {
            return $this->availableOnDemand;
        }
    }

.. important::

    Of course you need to remember about entity mapping customization as well.

Then our new entity should be configured as sylius product variant resource model:

.. code-block:: yaml

    sylius_product:
        resources:
            product_variant:
                classes:
                    model: IronMan\SyliusProductOnDemandPlugin\Entity\ProductVariant

This configuration should be placed in ``src/Resources/config/config.yml``. It also has to be imported
(``- { resource: "@IronManSyliusProductOnDemandPlugin/Resources/config/config.yml" }``) in ``tests/Application/app/config/config.yml``
to make it work in Behat tests. And at the end importing this file should be one of the steps described in plugin installation.

.. warning::

    Remember that if you modify or add some mapping, you should either provide a migration for the plugin user (that could be
    copied to their migration folder) or mention in installation process requirement of migration generation!

Form
****

To make our new field available in Admin panel, form extension is required:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace IronMan\SyliusProductOnDemandPlugin\Form\Extension;

    use Symfony\Component\Form\AbstractTypeExtension;
    use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
    use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
    use Symfony\Component\Form\FormBuilderInterface;

    final class ProductVariantTypeExtension extends AbstractTypeExtension
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('availableOnDemand', CheckboxType::class, [
                'label' => 'iron_man_sylius_product_on_demand_plugin.ui.available_on_demand',
            ]);
        }

        public function getExtendedType(): string
        {
            return ProductVariantType::class;
        }
    }

Translation keys placed in ``src/Resources/translations/message.{locale}.yml`` will be resolved automatically.

And in your ``app/Resources/config/services.yml``:

.. code-block:: yaml

    services:
        iron_man_sylius_product_on_demand_plugin.form.extension.type.product_variant:
            class: IronMan\SyliusProductOnDemandPlugin\Form\Extension\ProductVariantTypeExtension
            tags:
                - { name: form.type_extension, extended_type: Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType }

Again, you must remember about importing ``app/Resources/config/services.yml`` in ``app/Resources/config/config.yml``.

Template
********

The last step is extending the template of a product variant form. It can be done in three ways:

* by overwriting template
* by using sonata block events
* by writing a theme

For the needs of this tutorial, we would go the first way. What's crucial, we need to determine which template should be overwritten.
Naming for twig files in Sylius, both in **Shop** and **AdminBundle** are pretty clear and straightforward. In this specific case,
the template to override is ``src/Sylius/Bundle/AdminBundle/Resources/views/ProductVariant/Tab/_details.html.twig``. It should be copied
to ``src/Resources/views/SyliusAdminBundle/ProductVariant/Tab/`` directory, and additional field should be placed somewhere in the template.

.. code-block:: twig

    {#...#}

    <div class="ui segment">
        <h4 class="ui dividing header">{{ 'sylius.ui.inventory'|trans }}</h4>
        {{ form_row(form.onHand) }}
        {{ form_row(form.tracked) }}
        {{ form_row(form.version) }}
        {{ form_row(form.availableOnDemand) }}
    </div>

    {#...#}

.. warning::

    Beware! Implementing a new template on the plugin level is **not** everything! You must remember that this template should be
    copied to ``app/Resources/views/SyliusAdminBundle/views/`` directory (with whole catalogs structure, means ``/ProductVariant/Tab``
    in the application that uses your plugin - and therefore it should be mentioned in installation instruction.
    The same thing should be done for your test application (you should have ``tests/Application/views/SyliusAdminBundle/`` catalog
    with this template copied).

    Take a look at :doc:`customizing the templates</customization/template>` section in the documentation,
    for a better understanding of this topic.
