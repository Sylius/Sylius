Theme settings
==============

ThemeBundle has built-in, optional integration with :doc:`SettingsBundle </bundles/SyliusSettingsBundle/index>`.

Installation and configuration
------------------------------

In order to start using settings for your themes, you should :doc:`install SettingsBundle </bundles/SyliusSettingsBundle/installation>` first.

Theme settings schema
---------------------

Theme settings schema should be saved in ``Settings.php`` file in your theme's main directory. The file should
**return** an instance of ``SchemaInterface``. The example settings file looks like:

PHP 5
~~~~~

.. code-block:: php

    <?php // app/themes/AcmeTheme/Settings.php

    use Sylius\Bundle\SettingsBundle\Schema\CallbackSchema;
    use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
    use Symfony\Component\Form\FormBuilderInterface;

    return new CallbackSchema(
        function (SettingsBuilderInterface $settingsBuilder) {
            // define your settings here, e.g.
            $settingsBuilder->setDefault('option', 'foo');
        },
        function (FormBuilderInterface $formBuilder) {
            // define your form type here, e.g.
            $formBuilder->add('option', 'text');
        }
    );

.. note::

    Do not define your custom class in the global namespace, as it may cause conflicts if there are more classes named the same.

PHP 7
~~~~~

.. code-block:: php

    <?php // app/themes/AcmeTheme/Settings.php

    use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
    use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
    use Symfony\Component\Form\FormBuilderInterface;

    return new class implements SchemaInterface {
        public function buildSettings(SettingsBuilderInterface $builder)
        {
            // define your settings here, e.g.
            $settingsBuilder->setDefault('option', 'foo');
        }

        public function buildForm(FormBuilderInterface $builder)
        {
            // define your form type here, e.g.
            $formBuilder->add('option', 'text');
        }
    }

Using theme settings in your templates
--------------------------------------

Configured theme settings can be easily get in theme templates by using ``sylius_theme_settings()`` Twig function,
which works just the same as ``sylius_settings('schema')`` function from vanilla SettingsBundle.

.. code-block:: twig

    {% if sylius_theme_settings().showThisDiv %}
        <div>Hidden div!</div>
    {% endif %}
