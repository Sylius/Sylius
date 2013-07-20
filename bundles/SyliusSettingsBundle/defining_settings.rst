Creating your settings schema
=============================

You have to create a new class implementing **SchemaInterface**, which will represent the structure of your configuration.
For purpose of this tutorial, let's define the page metadata settings.

.. code-block:: php

    <?php

    // src/Acme/ShopBundle/Settings/MetaSettingsSchema.php

    namespace Acme\ShopBundle\Settings;

    use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
    use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
    use Symfony\Component\Form\FormBuilderInterface;

    class MetaSettingsSchema implements SchemaInterface
    {
        public function buildSettings(SettingsBuilderInterface $builder)
        {
            $builder
                ->setDefaults(array(
                    'title'            => 'Sylius - Modern ecommerce for Symfony2',
                    'meta_keywords'    => 'symfony, sylius, ecommerce, webshop, shopping cart',
                    'meta_description' => 'Sylius is modern ecommerce solution for PHP. Based on the Symfony2 framework.',
                ))
                ->setAllowedTypes(array(
                    'title'            => array('string'),
                    'meta_keywords'    => array('string'),
                    'meta_description' => array('string'),
                ))
            ;
        }

        public function buildForm(FormBuilderInterface $builder)
        {
            $builder
                ->add('title')
                ->add('meta_keywords')
                ->add('meta_description', 'textarea')
            ;
        }
    }

.. note::

    **SettingsBuilderInterface** is extended version of Symfony's OptionsResolver component.

As you can see there are two methods in our schema and both are very simple. First one, the ``->buildSettings()``
defines default values and allowed data types. Second, ``->buildForm()`` creates the form to be used in the web interface to update the settings.

Now, lets register our **MetaSettingsSchema** service. Remember that we are tagging it as `sylius.settings_schema`:

.. code-block:: xml

    <service id="acme.settings_schema.meta" class="Acme\ShopBundle\Settings\MetaSettingsSchema">
        <tag name="sylius.settings_schema" namespace="meta" />
    </service>

Your new settings schema is available for use.
