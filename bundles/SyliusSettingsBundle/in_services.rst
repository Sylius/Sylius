Using the settings in services
==============================

You can also load and save the settings in any service. Simply use the **SettingsManager** service, available under the ``sylius.settings.manager`` id.

Loading the settings
--------------------

.. code-block:: php

    <?php

    // src/Acme/ShopBundle/Taxation/TaxApplicator.php

    namespace Acme\ShopBundle\Taxation;

    use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;

    class TaxApplicator
    {
        private $settingsManager;

        public function __construct(SettingsManagerInterface $settingsManager)
        {
            $this->settingsManager = $settingsManager;
        }

        public function applyTaxes(Order $order)
        {
            $taxationSettings = $this->settingsManager->loadSettings('taxation');
            $itemsTotal = $order->getItemsTotal();

            $order->setTaxTotal($taxationSettings->get('rate') * $itemsTotal);
        }
    }

Injecting the settings manager is as simple as using any other service.

.. code-block:: xml

    <service id="acme.tax_applicator" class="Acme\ShopBundle\Taxation\TaxApplicator">
        <argument type="service" id="sylius.settings.manager" />
    </service>

Saving the settings
-------------------

.. note::

    To be written.
