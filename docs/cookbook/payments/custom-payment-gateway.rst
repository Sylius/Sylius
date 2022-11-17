How to integrate a Payment Gateway as a Plugin?
===============================================

Among all possible customizations, new gateway provider is one of the most common choices.
Payment processing complexity, regional limits and the amount of potential payment providers makes it hard for Sylius
core to keep up with all possible cases. A custom payment gateway is sometimes the only choice.

In the following example, a new gateway will be configured, which will send payment details to external API.

**1.** Set up a new plugin using the `PluginSkeleton <https://github.com/Sylius/PluginSkeleton>`_.

    .. code-block:: bash

        composer create-project sylius/plugin-skeleton ProjectName

**2.** The first step in the newly created repository would be to create a new Gateway Factory.

    Prepare a gateway factory class in ``src/Payum/SyliusPaymentGatewayFactory.php``:

    .. code-block:: php

        // src/Payum/SyliusPaymentGatewayFactory.php

        <?php

        declare(strict_types=1);

        namespace Acme\SyliusExamplePlugin\Payum;

        use Payum\Core\Bridge\Spl\ArrayObject;
        use Payum\Core\GatewayFactory;

        final class SyliusPaymentGatewayFactory extends GatewayFactory
        {
            protected function populateConfig(ArrayObject $config): void
            {
                $config->defaults([
                    'payum.factory_name' => 'sylius_payment',
                    'payum.factory_title' => 'Sylius Payment',
                ]);
            }
        }

    And at the end of ``src/Resources/config/services.xml`` or ``src/Resources/config/services.yaml`` add such a configuration for your gateway:

    .. code-block:: xml

        <!-- src/Resources/config/services.xml -->

        <service id="app.sylius_payment" class="Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder">
            <argument>Acme\SyliusExamplePlugin\Payum\SyliusPaymentGatewayFactory</argument>
            <tag name="payum.gateway_factory_builder" factory="sylius_payment" />
        </service>

    .. code-block:: yaml

        # src/Resources/config/services.yaml
        
        app.sylius_payment:
            class: Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder
            arguments: [ Acme\SyliusExamplePlugin\Payum\SyliusPaymentGatewayFactory ]
            tags:
              - { name: payum.gateway_factory_builder, factory: sylius_payment }

    
**3.** Next, one should create a configuration form, where authorization
(or some additional information, like sandbox mode) can be specified.

    Create the configuration type in ``src/Form/Type/SyliusGatewayConfigurationType.php``:

    .. code-block:: php

        // src/Form/Type/SyliusGatewayConfigurationType.php

        <?php

        declare(strict_types=1);

        namespace Acme\SyliusExamplePlugin\Form\Type;

        use Symfony\Component\Form\AbstractType;
        use Symfony\Component\Form\Extension\Core\Type\TextType;
        use Symfony\Component\Form\FormBuilderInterface;

        final class SyliusGatewayConfigurationType extends AbstractType
        {
            public function buildForm(FormBuilderInterface $builder, array $options): void
            {
                $builder->add('api_key', TextType::class);
            }
        }

    And add its configuration to `src/Resources/config/services.xml` or ``src/Resources/config/services.yaml``:

    .. code-block:: xml

        <!-- src/Resources/config/services.xml -->

        <service id="Acme\SyliusExamplePlugin\Form\Type\SyliusGatewayConfigurationType">
            <tag name="sylius.gateway_configuration_type" type="sylius_payment" label="Sylius Payment" />
            <tag name="form.type" />
        </service>
    
    .. code-block:: yaml
    
        # src/Resources/config/services.yaml
        
        Acme\SyliusExamplePlugin\Form\Type\SyliusGatewayConfigurationType:
            tags:
              - { name: sylius.gateway_configuration_type, type: sylius_payment, label: 'Sylius Payment' }
              - { name: form.type }

**4.** To introduce support for new configuration fields, we need to create a value object which will be passed to action,
so we can use an API Key provided in form.

    Create a new ValueObject in ``src/Payum/SyliusApi.php``:

    .. code-block:: php

        // src/Payum/SyliusApi.php

        <?php

        declare(strict_types=1);

        namespace Acme\SyliusExamplePlugin\Payum;

        final class SyliusApi
        {
            /** @var string */
            private $apiKey;

            public function __construct(string $apiKey)
            {
                $this->apiKey = $apiKey;
            }

            public function getApiKey(): string
            {
                return $this->apiKey;
            }
        }

    In ``src/Payum/SyliusPaymentGatewayFactory.php`` we need to add support for newly created ``SyliusApi`` VO by adding
    ``$config['payum.api'] = function (ArrayObject $config) { return new SyliusApi($config['api_key']); };`` at the end of
    ``populateConfig`` method. Adjusted ``SyliusPaymentGatewayFactory`` class should look like this:

    .. code-block:: php

        // src/Payum/SyliusPaymentGatewayFactory.php

        <?php

        declare(strict_types=1);

        namespace Acme\SyliusExamplePlugin\Payum;

        use Payum\Core\Bridge\Spl\ArrayObject;
        use Payum\Core\GatewayFactory;

        final class SyliusPaymentGatewayFactory extends GatewayFactory
        {
            protected function populateConfig(ArrayObject $config): void
            {
                $config->defaults([
                    'payum.factory_name' => 'sylius_payment',
                    'payum.factory_title' => 'Sylius Payment',
                ]);

                $config['payum.api'] = function (ArrayObject $config) {
                    return new SyliusApi($config['api_key']);
                };
            }
        }

    From now on, your new Payment Gateway should be available in the admin panel.

    .. image:: ../../_images/cookbook/custom-payment-gateway/new_gateway_configuration_type.png

**5.** Configure new payment method in the admin panel

    .. image:: ../../_images/cookbook/custom-payment-gateway/new_payment_method.png

**6.** Configure required actions

    We will create two actions: CaptureAction and StatusAction. The first one will be responsible for sending data to
    an external system:

     * payment amount
     * currency
     * API key configured in the previously created form

    while the second one will translate HTTP codes of the Response to a proper state of payment.

**6.1.** Create ``StatusAction`` and add it to the ``SyliusPaymentGatewayFactory``

    In a gateway factory class in ``src/Payum/SyliusPaymentGatewayFactory.php`` we need to add
    ``'payum.action.status' => new StatusAction(),`` to config defaults. Adjusted ``SyliusPaymentGatewayFactory`` class
    should look like this:

    .. code-block:: php

        // src/Payum/SyliusPaymentGatewayFactory.php

        <?php

        declare(strict_types=1);

        namespace Acme\SyliusExamplePlugin\Payum;

        use Acme\SyliusExamplePlugin\Payum\Action\StatusAction;
        use Payum\Core\Bridge\Spl\ArrayObject;
        use Payum\Core\GatewayFactory;

        final class SyliusPaymentGatewayFactory extends GatewayFactory
        {
            protected function populateConfig(ArrayObject $config): void
            {
                $config->defaults([
                    'payum.factory_name' => 'sylius_payment',
                    'payum.factory_title' => 'Sylius Payment',
                    'payum.action.status' => new StatusAction(),
                ]);

                $config['payum.api'] = function (ArrayObject $config) {
                    return new SyliusApi($config['api_key']);
                };
            }
        }

    Now we need to create a ``StatusAction`` in ``src/Payum/Action/StatusAction.php``:

    .. code-block:: php

        // src/Payum/Action/StatusAction.php

        <?php

        declare(strict_types=1);

        namespace Acme\SyliusExamplePlugin\Payum\Action;

        use Payum\Core\Action\ActionInterface;
        use Payum\Core\Exception\RequestNotSupportedException;
        use Payum\Core\Request\GetStatusInterface;
        use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;

        final class StatusAction implements ActionInterface
        {
            public function execute($request): void
            {
                RequestNotSupportedException::assertSupports($this, $request);

                /** @var SyliusPaymentInterface $payment */
                $payment = $request->getFirstModel();

                $details = $payment->getDetails();

                if (200 === $details['status']) {
                    $request->markCaptured();

                    return;
                }

                if (400 === $details['status']) {
                    $request->markFailed();

                    return;
                }
            }

            public function supports($request): bool
            {
                return
                    $request instanceof GetStatusInterface &&
                    $request->getFirstModel() instanceof SyliusPaymentInterface
                ;
            }
        }

    ``StatusAction`` will update the state of payment based on details provided by ``CaptureAction``.
    Based on the value of the status code of the HTTP request, the payment status will be adjusted as follows:

     * HTTP 400 (Bad request) - payment has failed
     * HTTP 200 (OK) - payment succeeded

**6.2.** Create a service for handling the CaptureAction

    .. warning::

        An external request interceptor was used for training purposes. Please,
        visit `Beeceptor <https://beeceptor.com/>`_. and supply  ``sylius-payment`` as an endpoint name. If the service
        is not working, you can use `Post Test Server V2 <https://ptsv2.com/>`_. as well, but remember about adjusting
        the ``https://sylius-payment.free.beeceptor.com`` path.

    This time we will start with creating a ``CaptureAction`` in ``src/Payum/Action/CaptureAction.php``:

    .. code-block:: php

        // src/Payum/Action/CaptureAction.php

        <?php

        declare(strict_types=1);

        namespace Acme\SyliusExamplePlugin\Payum\Action;

        use Acme\SyliusExamplePlugin\Payum\SyliusApi;
        use GuzzleHttp\Client;
        use GuzzleHttp\Exception\RequestException;
        use Payum\Core\Action\ActionInterface;
        use Payum\Core\ApiAwareInterface;
        use Payum\Core\Exception\RequestNotSupportedException;
        use Payum\Core\Exception\UnsupportedApiException;
        use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
        use Payum\Core\Request\Capture;

        final class CaptureAction implements ActionInterface, ApiAwareInterface
        {
            /** @var Client */
            private $client;
            /** @var SyliusApi */
            private $api;

            public function __construct(Client $client)
            {
                $this->client = $client;
            }

            public function execute($request): void
            {
                RequestNotSupportedException::assertSupports($this, $request);

                /** @var SyliusPaymentInterface $payment */
                $payment = $request->getModel();

                try {
                    $response = $this->client->request('POST', 'https://sylius-payment.free.beeceptor.com', [
                        'body' => json_encode([
                            'price' => $payment->getAmount(),
                            'currency' => $payment->getCurrencyCode(),
                            'api_key' => $this->api->getApiKey(),
                        ]),
                    ]);
                } catch (RequestException $exception) {
                    $response = $exception->getResponse();
                } finally {
                    $payment->setDetails(['status' => $response->getStatusCode()]);
                }
            }

            public function supports($request): bool
            {
                return
                    $request instanceof Capture &&
                    $request->getModel() instanceof SyliusPaymentInterface
                ;
            }

            public function setApi($api): void
            {
                if (!$api instanceof SyliusApi) {
                    throw new UnsupportedApiException('Not supported. Expected an instance of ' . SyliusApi::class);
                }

                $this->api = $api;
            }
        }

    And at the end of ``src/Resources/config/services.xml`` or `src/Resources/config/services.yaml`` add such a configuration for your capture action:

    .. code-block:: xml

        <!-- src/Resources/config/services.xml -->

        <service id="Acme\SyliusExamplePlugin\Payum\Action\CaptureAction" public=true>
            <argument type="service" id="sylius.http_client" />
            <tag name="payum.action" factory="sylius_payment" alias="payum.action.capture" />
        </service>
    
    .. code-block:: yaml
    
        # src/Resources/config/services.yaml
        
        Acme\SyliusExamplePlugin\Payum\Action\CaptureAction:
            public: true
            arguments:
                - '@sylius.http_client'
            tags:
                - { name: payum.action, factory: sylius_payment, alias: payum.action.capture }

    Your shop is ready to handle the first checkout with your newly created gateway!

    .. tip::

        On both previously mentioned interceptors, you may configure a status code of the response.
        Check the behavior of Sylius for 400 status code (HTTP Bad Request) as well!

Learn more
----------

* :doc:`Order payments documentation </book/orders/payments>`
* `Payum documentation <https://github.com/Payum/Payum/blob/master/docs/index.md>`_
* `Mollie payment integration <https://github.com/BitBagCommerce/SyliusMolliePlugin/>`_
