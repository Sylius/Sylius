# How to integrate a Payment Gateway as a Plugin?

A custom payment gateway is a common need in Sylius, given the wide range of payment providers and regional differences. This guide explains how to set up a new payment gateway that sends payment details to an external API.

## Step 1: Generic Configuration

1. **Create a Sylius Plugin**\
   First, set up a plugin for your custom gateway. Follow the guide to creating a Sylius Plugin.
2. **Create the Gateway Configuration Form**\
   Define a form for your gateway’s configuration settings.

**Form Type**: Create the configuration type in `src/Form/Type/GatewayConfigurationType.php`:

```php
namespace Acme\SyliusExamplePlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class GatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('api_key', TextType::class);
    }
}
```

**Service Configuration**: Add the form type to your service configuration in `config/services.xml` or `config/services.yaml`:

{% tabs %}
{% tab title="XML" %}
```xml
<service id="Acme\SyliusExamplePlugin\Form\Type\GatewayConfigurationType">
    <tag name="sylius.gateway_configuration_type" type="sylius_payment" label="acme_sylius_payment.gateway.name" />
    <tag name="form.type" />
</service>
```
{% endtab %}

{% tab title="YAML" %}
```yaml
Acme\SyliusExamplePlugin\Form\Type\GatewayConfigurationType:
    tags:
      - name: sylius.gateway_configuration_type
        type: sylius_payment
        label: 'acme_sylius_payment.gateway.name'
      - name: form.type
```
{% endtab %}
{% endtabs %}

**Translation**: Add a label for the gateway in `translations/messages.en.yaml`:

```yaml
acme_sylius_payment:
    gateway:
        name: 'My Gateway Name'
```

From now on, your new Payment Gateway should be available in the admin panel (Url: `/admin/payment-methods`).

<figure><img src="../../.gitbook/assets/image (5) (1) (1).png" alt=""><figcaption><p>New payment method available</p></figcaption></figure>

3. **Add Field Templates**\
   Create a template for each field you need for example `templates/admin/payment_method/form/api_key.html.twig`:

```twig
{% set form = hookable_metadata.context.form.gatewayConfig.config.api_key %}


<div class="col-12 col-md-6">
    {{ form_row(form, sylius_test_form_attribute('config-api-key')) }}
</div>
```

4. **Register Field Templates in Twig Hooks**\
   Register the field templates in `config/config.yaml` using Twig hooks:

```yaml
sylius_twig_hooks:
    hooks:
        'sylius_admin.payment_method.create.content.form.sections.gateway_configuration.sylius_payment':
            api_key:
                template: '@AcmeSyliusExamplePlugin/admin/payment_method/api_key.html.twig'
                priority: 0
```

<figure><img src="../../.gitbook/assets/image (9) (1).png" alt=""><figcaption><p>Payment Gateway Config form</p></figcaption></figure>

**NOTE:**

To create a new payment configuration, you need a valid encryption key. If you don’t have one, generate it by running:

```bash
bin/console sylius:payment:generate-key
```

Sylius encrypts configuration data like API keys and payment credentials to protect sensitive information, prevent unauthorized access, and comply with security regulations.

## Step 2: Command Provider & Handler

With Sylius 2.0, a new "Payment Request" system is available. This system allows you to handle payment actions such as capture, status updates, and more through Symfony’s Messenger component. This is especially beneficial for headless implementations.

### **Creating a Gateway Command Provider**

First, set up a command provider to specify which command should be executed for each payment request action.

**Provider Service**: Define the command provider service in `config/services.yaml`:

{% tabs %}
{% tab title="YAML" %}
```yaml
acme.sylius_example.command_provider.sylius_payment:
    class: Sylius\Bundle\PaymentBundle\CommandProvider\ActionsCommandProvider
    arguments:
        - !tagged_locator
            tag: acme.sylius_example.command_provider.sylius_payment
            index_by: 'action'
    tags:
        - name: sylius.payment_request.command_provider
          gateway_factory: 'sylius_payment'
```
{% endtab %}

{% tab title="XML" %}
```xml
<services>
    <service id="acme.sylius_example.command_provider.sylius_payment"
             class="Sylius\Bundle\PaymentBundle\CommandProvider\ActionsCommandProvider">
        <argument type="tagged_locator"
                  tag="acme.sylius_example.command_provider.sylius_payment"
                  index_by="action" />
        <tag name="sylius.payment_request.command_provider"
             gateway-factory="sylius_payment" />
    </service>
</services>
```
{% endtab %}
{% endtabs %}

This setup uses a tagged locator to identify services that are tagged with `acme.sylius_example.command_provider.sylius_payment` and to index them by the `action` tag property. Each tagged service will provide the appropriate command for a specific action.

### **Creating an action Command Provider**

Create the action `CommandProvider` which provides your future `Command`:

{% tabs %}
{% tab title="YAML" %}
```yaml
acme.sylius_example.command_provider.sylius_payment.capture:
    class: Acme\SyliusExamplePlugin\CommandProvider\CapturePaymentRequestCommandProvider
    tags:
        - name: acme.sylius_example.command_provider.sylius_payment
          action: !php/const Sylius\Component\Payment\Model\PaymentRequestInterface::ACTION_CAPTURE
```
{% endtab %}

{% tab title="XML" %}
```xml
<service id="acme.sylius_example.command_provider.sylius_payment.capture"
         class="Acme\SyliusExamplePlugin\CommandProvider\CapturePaymentRequestCommandProvider">
    <tag name="acme.sylius_example.command_provider.sylius_payment"
         action="capture"/>
</service>
```
{% endtab %}
{% endtabs %}

Then create the class related to this service:

```php
<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\CommandProvider;

use Acme\SyliusExamplePlugin\Command\CapturePaymentRequest;
use Sylius\Bundle\PaymentBundle\CommandProvider\PaymentRequestCommandProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final readonly class CapturePaymentRequestCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        return $paymentRequest->getAction() === PaymentRequestInterface::ACTION_CAPTURE;
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        return new CapturePaymentRequest($paymentRequest->getId());
    }
}
```

### **Defining the Capture Command**

Next, create a command to handle the `capture` action. Place this in `src/Command/CapturePaymentRequest.php`:

```php
namespace Acme\SyliusExamplePlugin\Command;

use Sylius\Bundle\PaymentBundle\Command\PaymentRequestHashAwareInterface;
use Sylius\Bundle\PaymentBundle\Command\PaymentRequestHashAwareTrait;

class CapturePaymentRequest implements PaymentRequestHashAwareInterface
{
    use PaymentRequestHashAwareTrait;

    public function __construct(protected ?string $hash) {}
}
```

Here, the `CapturePaymentRequest` class implements the `PaymentRequestHashAwareInterface` using a trait to handle the payment request hash. This command will be dispatched to handle `capture` actions specifically.

### **Creating the Capture Command Handler**

Now, create a command handler that processes the `CapturePaymentRequest`. Place this in `src/CommandHandler/CapturePaymentRequestHandler.php`:

```php
namespace Acme\SyliusExamplePlugin\CommandHandler;

use Acme\SyliusExamplePlugin\Command\CapturePaymentRequest;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProviderInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CapturePaymentRequestHandler
{
    public function __construct(
        private PaymentRequestProviderInterface $paymentRequestProvider,
        private StateMachineInterface $stateMachine,
    ) {}

    public function __invoke(CapturePaymentRequest $capturePaymentRequest): void
    {
        // Retrieve the current PaymentRequest based on the hash provided in the CapturePaymentRequest command
        $paymentRequest = $this->paymentRequestProvider->provide($capturePaymentRequest);

        // Custom capture logic for the payment provider would go here.
        // Example: communicating with the payment gateway API to capture funds.

        // Mark the PaymentRequest as complete|process|fail|cancel.
        $this->stateMachine->apply(
            $paymentRequest,
            PaymentRequestTransitions::GRAPH,
            PaymentRequestTransitions::TRANSITION_COMPLETE
        );
    }
}
```

In this handler, we:

1. Retrieve the current `PaymentRequest` using the `PaymentRequestProviderInterface`.
2. Implement any custom logic to handle the capture action, such as calling an external API.
3. Apply the `complete` transition to the `PaymentRequest` state machine once the action is successfully processed.

### **Important Tips**

* **Defining Other Actions**: Follow similar steps to create commands and handlers for other actions, such as `authorize`, `status`, or `refund`, as required by your payment gateway.
* **Customizing the Payment Flow**: You can also define additional actions beyond the predefined ones (e.g., `capture`, `authorize`). For example, if your provider supports unique actions such as `subscription`, define custom commands and handlers to process those actions.
* **Testing the Setup**: After implementing, you can test the capture flow by simulating the action from Sylius’ admin panel or the shop's front end to ensure the entire flow works seamlessly with your new provider.

This setup allows you to handle payment actions through a clean, event-driven architecture using Symfony’s Messenger component, making it flexible and easy to integrate with various payment gateways in Sylius 2.0.

## Step 3: Handling Payment via the UI and API

Sylius "Payment Request" system is designed to work statelessly, making it compatible with both API and UI interactions. For UI scenarios where you need to display specific pages, perform redirects, or present a form, you’ll need to create a custom HTTP response provider. Here’s how to configure your payment handling.

### Create an Actions HTTP Response Provider

The Actions HTTP Response Provider is a service very similar to the Actions Command Provider we created earlier. Its goal is to route an action to the right HTTP Response Provider which we will create in the next chapter.

{% tabs %}
{% tab title="YAML" %}
```yaml
acme.sylius_example.provider.order_pay.http_response.sylius_payment:
  class: Sylius\Bundle\PaymentBundle\Provider\ActionsHttpResponseProvider
  arguments:
      - !tagged_locator
          tag: acme.sylius_example.provider.http_response.sylius_payment
          index_by: action
  tags:
      - name: sylius.payment_request.provider.http_response
        gateway_factory: 'sylius_payment'
```
{% endtab %}

{% tab title="XML" %}
```xml
<services>
  <service id="acme.sylius_example.provider.order_pay.http_response.sylius_payment"
           class="Sylius\Bundle\PaymentBundle\Provider\ActionsHttpResponseProvider">
    <argument type="tagged_locator"
              tag="acme.sylius_example.provider.http_response.sylius_payment"
              index-by="action"/>
    <tag name="sylius.payment_request.provider.http_response"
         gateway-factory="sylius_payment"/>
  </service>
</services>
```
{% endtab %}
{% endtabs %}

### Create an HTTP Response Provider

Define a custom HTTP response provider to manage UI behavior for actions, like `capture`. This provider will allow you to control the response for different payment states (e.g., redirecting to a payment portal or displaying a confirmation page).

**Provider Class: `src/OrderPay/Provider/CaptureHttpResponseProvider.php`**

```php
<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\OrderPay\Provider;

use Sylius\Bundle\PaymentBundle\Provider\HttpResponseProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class CaptureHttpResponseProvider implements HttpResponseProviderInterface
{
    public function __construct(
        private readonly Environment $twig,
    ) {}

    public function supports(RequestConfiguration $requestConfiguration, PaymentRequestInterface $paymentRequest): bool
    {
        return $paymentRequest->getAction() === PaymentRequestInterface::ACTION_CAPTURE;
    }

    public function getResponse(RequestConfiguration $requestConfiguration, PaymentRequestInterface $paymentRequest): Response
    {
        $data = $paymentRequest->getResponseData();

        // Example: Redirect to an external portal
        return new RedirectResponse($data['portal_redirect_url']);

        // Example: Display a Twig template
        return new Response(
            $this->twig->render(
                '@AcmeSyliusExamplePlugin/order_pay/capture.html.twig',
                $data
            )
        );
    }
}
```

### Register the Response Provider

To activate your response provider, add it to your service configuration.

**Service Registration: `config/services.yaml`**

{% tabs %}
{% tab title="YAML" %}
```yaml
acme.sylius_example.provider.order_pay.http_response.sylius_payment.capture:
    class: Acme\SyliusExamplePlugin\OrderPay\Provider\CaptureHttpResponseProvider
    tags:
        - name: acme.sylius_example.provider.http_response.sylius_payment
          action: !php/const Sylius\Component\Payment\Model\PaymentRequestInterface::ACTION_CAPTURE
```
{% endtab %}

{% tab title="XML" %}
```xml
<service id="acme.sylius_example.provider.order_pay.http_response.sylius_payment.capture"
         class="Acme\SyliusExamplePlugin\OrderPay\Provider\CaptureHttpResponseProvider">
    <tag name="acme.sylius_example.provider.http_response.sylius_payment"
         action="capture" />
</service>
```
{% endtab %}
{% endtabs %}

### Handling Other Payment Actions

For additional actions like `status`, `refund`, or `cancel`, repeat the command and handler creation process. This modularity allows you to tailor each payment action (e.g., `StatusPaymentRequest`, `RefundPaymentRequest`) according to your business requirements.

### Using Payment Request for UI

The Payment Request system is inherently stateless, making it ideal for headless or standard setups. For UI interactions, like redirecting after payment, you can use the response providers configured above to customize the "after pay" route or provide specific UI feedback based on the payment status.

***

By following this setup, you’ll have a fully functional "Payment Gateway" in Sylius, equipped to manage both API and UI-based payment flows, tailored to your specific business logic.
