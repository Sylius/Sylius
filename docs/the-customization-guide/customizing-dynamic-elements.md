# Customizing Dynamic Elements

Sylius 2.1 adopts a modern JavaScript architecture based on **Symfony UX** and **StimulusJS**. This setup enables you to build dynamic, interactive frontend behavior while following clean Symfony conventions.

This guide explains how to customize and register **JavaScript controllers** in your Sylius frontend. It covers automatic controller discovery, manual registration, and JSON-based configuration. You’ll also find real-world examples and tips for debugging common issues.

***

## Prerequisites

* Sylius 2.1 or later
* Webpack Encore is properly configured
* Basic knowledge of StimulusJS

### ⚠️ Compatibility Note

{% hint style="danger" %}
If your application is based on Sylius/Standard prior to the 2.1 release, you must first upgrade your project as outlined [here](https://github.com/Sylius/Sylius-Standard/pull/1126). Without this upgrade, your app will rely on the legacy assets system, and this guide will not apply.
{% endhint %}

***

## Example Use Cases

You might want to customize dynamic elements in cases like:

* Adding quantity increment buttons to the product page using a custom Stimulus controller
* Creating a sticky "Add to Cart" bar that reacts to scroll events
* Updating shipping method options dynamically based on the selected country during checkout
* Enhancing admin panel UX with collapsible panels or sortable table rows

Each of these examples relies on connecting a Stimulus controller to a DOM element, configuring it through `data-*` attributes, and optionally extending or overriding the default Sylius behavior.

***

## 📁 Default Directory Structure

By default, Sylius expects Stimulus controllers to reside in the `assets/controllers/` directory. Each controller should be named following the pattern `*_controller.js`.

```
assets/
├── admin/
│   └── controllers.json
├── shop/
│   └── controllers.json
├── controllers/          ← Auto-discovered controllers
│   └── alert_controller.js
└── controllers.json      ← Shared config imported by Flex
```

***

## Adding a Stimulus Controller Using Automatic Discovery

This is the simplest way to register a new Stimulus controller. If your controller is located in the expected directory and follows the naming conventions, Symfony UX will automatically detect and load it—**no manual configuration needed**.

### 🔧 Steps

#### 1. Create the Stimulus Controller

Place your file under `assets/admin/controllers/` and name it using the `*_controller.js` pattern.

```javascript
// assets/admin/controllers/alert_controller.js

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    alert(this.element.dataset.message || 'Test Alert!');
  }
}
```

This controller will automatically be registered as `alert`.

#### 2. Create a Twig Template

Use the `stimulus_controller()` Twig helper to bind your controller to a DOM element.

<pre class="language-twig"><code class="lang-twig"><strong>{# templates/admin/order/show/alert.html.twig #}
</strong><strong>
</strong><strong>&#x3C;div {{ stimulus_controller('alert') }}>&#x3C;/div>
</strong></code></pre>

Optional: pass `data-*` attributes using `stimulus_controller()` options if needed.

#### 3. Register the Template via a Twig Hook

Hook your controller’s template into the desired part of the admin UI using the Sylius Twig hook system.

<pre class="language-yaml"><code class="lang-yaml"><strong># config/packages/_sylius.yaml
</strong><strong>
</strong><strong>sylius_twig_hooks:
</strong>    hooks:
        'sylius_admin.order.show.content':
            alert:
                template: 'admin/order/show/alert.html.twig'
</code></pre>

This ensures your controller is rendered at the `sylius_admin.order.show.content` hook point, **without overriding core templates**.

#### 4. Rebuild your assets

```bash
yarn build # or yarn watch
```

<figure><img src="../.gitbook/assets/image (37).png" alt=""><figcaption><p><strong>✅ Result:</strong> The alert will be shown every time you enter the order show page!</p></figcaption></figure>

#### Result

* Your controller is discovered automatically.
* It's attached to the DOM using `stimulus_controller()`.
* It’s injected upgrade-safely using a Twig hook.

***

## Disabling or Enabling Existing Stimulus Controllers

In some situations, you may want to **disable a built-in controller** (e.g., to replace it with your own) or **change how it's loaded** (e.g., only when needed). This is possible via the `controllers.json` file using Symfony UX's controller configuration.

### Example: Disable the `taxon-tree` Controller in the Admin Panel

To prevent the built-in `taxon-tree` Stimulus controller from loading:

**📁 File: `assets/admin/controllers.json`**

```json
// assets/admin/controllers.json

{
  "controllers": {
    "@sylius/admin-bundle": {
      "taxon-tree": {
        "enabled": false,
        "fetch": "lazy"
      }
    }
  }
}
```

This disables the controller entirely (`enabled: false`), and even if enabled, it would only load lazily.

#### &#x20;Rebuild your assets after the change

```bash
yarn build # or yarn watch
```

<figure><img src="../.gitbook/assets/image (38).png" alt=""><figcaption><p><strong>✅ Result:</strong> Taxon tree is no longer visible.</p></figcaption></figure>

### 🗂 Common Keys

* `enabled`: `true` or `false`
  * Controls whether the controller is loaded at all.
* `fetch`: `"lazy"` (default) or `"eager"`
  * Defines when the controller is fetched:
    * **lazy**: loaded only when used in the DOM
    * **eager**: loaded immediately on page load

{% hint style="info" %}
#### 💡 Pro Tip

You can override or replace a disabled controller by registering your own under the same name, or by targeting the same HTML with a new controller.
{% endhint %}

***

## 3: Manual Registration

Use manual registration when:

* Your controller is located outside the default `controllers/` directory
* You're integrating a controller from a third-party or custom plugin
* You need more explicit control over how and when the controller is loaded

### Steps

#### 1. Create Your Stimulus Controller

```javascript
// assets/shop/custom/confirm_controller.js

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    console.log('Confirm controller loaded.');
  }

  onClick(event) {
    event.preventDefault();
    console.log('Button clicked!');
  }
}
```

This controller defines a `connect()` lifecycle method and an `onClick()` action for buttons.

#### 2. Register the Controller Manually

```javascript
// assets/shop/bootstrap.js

import ConfirmController from './custom/confirm_controller';

app.register('confirm', ConfirmController);
```

This ensures that Stimulus knows about your `confirm` controller and connects it when used in templates.

#### 3. Create the Twig Template

```twig
{# templates/shop/order/confirmation.html.twig #}

<button
    class="btn btn-primary"
    data-action="click->confirm#onClick"
    {{ stimulus_controller('confirm') }}>
    Confirm
</button>
```

This adds a button that triggers your `onClick` action when clicked.

#### 4. Register the Template via Twig Hooks

Use the Sylius Twig hooks system to inject your custom button into the **Thank You** page (order confirmation).

```yaml
# config/packages/_sylius.yaml

sylius_twig_hooks:
    hooks:
        'sylius_shop.order.thank_you.content.buttons#customer': # for logged in customer
            confirmation:
                template: 'shop/order/confirmation.html.twig'

        'sylius_shop.order.thank_you.content.buttons#guest': # for guest
            confirmation:
                template: 'shop/order/confirmation.html.twig'
```

This hook ensures that your controller-powered button is shown after order placement, regardless of user type.

#### 5. Rebuild your assets

```bash
yarn build # or yarn watch
```

<figure><img src="../.gitbook/assets/image (40).png" alt=""><figcaption><p><strong>✅ Result:</strong> A new button appears that logs to the console every time it is clicked!</p></figcaption></figure>

***

## 🐞 Troubleshooting Tips

* **Incorrect Controller Name?** Verify that the `data-controller` attribute matches the registered controller name.
* **Console Errors?** Use browser developer tools to check if your controller is compiled and loaded correctly.
* **Caching Issues?** Clear both Symfony and browser caches to ensure the latest assets are loaded.

## Learn More

* [Stimulus documentation](https://symfony.com/bundles/StimulusBundle/current/index.html)&#x20;
* [Example Plugin Using New Assets Mechanism](https://github.com/Jibbarth/SyliusCelebratePlugin)

