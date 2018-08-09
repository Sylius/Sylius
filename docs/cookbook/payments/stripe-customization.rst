How to customize Stripe Credit Card payment?
============================================



Prerequisite: install Stripe
--------------

First of all, Stripe needs to be installed, configured and tested, using this :  :doc:`documentation </cookbook/payments/stripe>`

Override Payum templates
---------------------

.. note::

    By doing this you'll be able to use a custom layout and to pass extra parameters to stripe like the user email.

.. code-block:: yaml

    # app/config/config.yml
    twig:
        paths:
            "%kernel.root_dir%/../app/Resources/PayumCore/views": PayumCore
            "%kernel.root_dir%/../app/Resources/PayumStripe/views": PayumStripe

Having this configuration defined, you can now create your customuzations in your `app/Ressource/` directory, as usual.

For example, you could define this layout:

.. code-block:: twig

    # app/Resources/PayumCore/views/layout.html.twig
    {% extends '@SyliusShop/Checkout/layout.html.twig' %}

    {% block stylesheets %}
        {% block payum_stylesheets "" %}

        {{ parent() }}
    {% endblock %}

    {% block content %}
        <div class="ui padded segment">

            <div class="ui header">
                Please enter your payment details in order to finalize your order.
                <div class="ui sub header">
                    {{ model.description }} {{ model.currency ?? '' }}
                </div>
            </div>
            <div class="ui hidden divider"></div>
            <div id="payum_body-outer">
                {% block payum_body "" %}
            </div>
        </div>
        <div class="ui hidden divider" ></div>
    {% endblock %}

    {% block footer %}<!-- footer emptied -->{% endblock %}

    {% block javascripts %}
        {{ parent() }}

        {% block payum_vendor_javascripts "" %}
        {% block payum_javascripts "" %}
    {% endblock %}


For example, you could also pass extra parameters to Stripe:
.. code-block:: yaml

    # app/Resources/PayumStripe/views/Action/obtain_checkout_token.html.twig
    {% extends layout ?: "@PayumCore/layout.html.twig" %}

    {% block payum_body %}
        {{ parent() }}

        <form action="{{ actionUrl|default('') }}" method="POST">
            <script
                    {# see https://stripe.com/docs/checkout#integration-simple #}
                    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                    data-key="{{ publishable_key }}"
                    data-image="{{ asset('images/payment/iTop-Hub-logo-on-stripe-checkout.png') }}"
                    data-name="{{ model.name|default("iTop Hub") }}"
                    data-description="{{ model.description|default("") }}"
                    data-amount="{{ model.amount }}"
                    data-currency="{{ model.currency|default("EUR") }}"
                    data-email="{{ app.user.email }}"
                    data-locale="auto"
                    data-allow-remember-me="false"
            >
            </script>
        </form>
    {% endblock payum_body %}

    {% block payum_javascripts %}
        {{ parent() }}

        <script type="text/javascript">
            $(function() {$('form .stripe-button-el').click();});
        </script>
    {% endblock payum_javascripts %}

Add metadata to Stripe
---------------------------------------------------------------------------

The metadata have to be given later to stripe, so you can't simply use the template overriding, instead, you have to define a Payum Extension that will hanbdle this for you at the right time:

First you have to create a Service

.. code-block:: php

    <?php
    /* src/AppBundle/Payment/StripeAddMetadataOnCaptureExtensions.php */

    namespace AppBundle\Payment;

    use AppBundle\Entity\Customer;
    use AppBundle\Entity\Order;
    use AppBundle\Entity\OrderItem;
    use Payum\Core\Extension\Context;
    use Payum\Core\Extension\ExtensionInterface;
    use Payum\Core\Request\Capture;

    use Sylius\Bundle\PayumBundle\Request\GetStatus;
    use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;

    /**
     *
     * This class handles the addition of metadata to stripe
     *
     * It is wired using Payum's built in extension system.
     * It is wired to stripe_checkout only using a factory filter on the service tag.
     * It is additionally filtered to the Capture request using a code check.
     *
     * @author Bruno DA SILVA (https://github.com/bruno-ds)
     */
    class StripeAddMetadataOnCaptureExtensions implements ExtensionInterface
    {
        /**
         * @var Context $context
         */
        public function onPreExecute(Context $context)
        {
            if (! $this->supports($context)) {
                return;
            }

            return;
        }

        /**
         * @var Context $context
         */
        public function onExecute(Context $context)
        {
            if (! $this->supports($context)) {
                return;
            }

            /** @var $request Capture */
            $request = $context->getRequest();

            /** @var SyliusPaymentInterface $payment */
            $payment = $request->getModel();

            /** @var Order $order */
            $order = $payment->getOrder();

            /** @var Customer $customer */
            $customer = $order->getCustomer();

            $context->getGateway()->execute($status = new GetStatus($payment));
            if (! $status->isNew()) {
                return;
            }

            /** @var array $paymentDetails */
            $paymentDetails = $payment->getDetails();

            if (!isset($paymentDetails['metadata'])) {
                $paymentDetails['metadata'] = [];
            }

            $paymentDetails['metadata']['order_id'] = $order->getId();
            $paymentDetails['metadata']['order_number'] = $order->getNumber();
            $paymentDetails['metadata']['sylius_customer_id'] = $customer->getId();

            $item_list = [];
            /** @var OrderItem $item */
            foreach ($order->getItems() as $item) {
                $item_list[] = $item->getVariantName();
            }
            $paymentDetails['metadata']['item_list'] = implode(', ', $item_list);

            $payment->setDetails($paymentDetails);
        }

        /**
         * @var Context $context
         */
        public function onPostExecute(Context $context)
        {
            if (! $this->supports($context)) {
                return;
            }

            return;
        }

        public function supports($context)
        {
            /** @var $request Capture */
            $request = $context->getRequest();

            return
                $request instanceof Capture &&
                $request->getModel() instanceof SyliusPaymentInterface
                ;
        }
    }


Then let's register this service using the Payum's tag `payum.extension`

.. code-block:: yaml

    # app/config/config.yml

    app.payment.payum.stripe.on_capture.add_metadata:
        class: AppBundle\Payment\StripeAddMetadataOnCaptureExtensions
        tags:
            - { name: payum.extension, factory: stripe_checkout, prepend: true}


From now on, your service will be acalled each time a Stripe's Catpure is performed and it will add metadata to the payment, they will be visible on stripe's side.

**Done!**


Learn more
----------

* :doc:`Payments concept documentation </book/orders/payments>`
* `Payum - Project Documentation <https://github.com/Payum/Payum/blob/master/src/Payum/Core/Resources/docs/index.md>`_
* `Payum - Extension Principles <https://github.com/Payum/Payum/blob/master/docs/the-architecture.md#extensions>`
* `Payum - Extension Service Tag Configuration <https://github.com/Payum/PayumBundle/blob/master/Resources/doc/container_tags.md#extension-tag>`
* `Stripe metadata explanation <https://stripe.com/blog/adding-context-with-metadata?locale=fr>`
* `Stripe metadata Documentation <https://stripe.com/docs/api#metadata>`
