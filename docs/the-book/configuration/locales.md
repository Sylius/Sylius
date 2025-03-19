---
layout:
  title:
    visible: true
  description:
    visible: false
  tableOfContents:
    visible: true
  outline:
    visible: true
  pagination:
    visible: true
---

# Locales

{% hint style="info" %}
To add a new locale to your store you have to assign it to a channel.
{% endhint %}

To support multiple languages we are using **Locales** in **Sylius**. Locales are language codes standardized by ISO 15897.

{% hint style="info" %}
In the dev environment, you can easily check the locale you are currently using in the Symfony debug toolbar.
{% endhint %}

### Base Locale

During the installation, you provided a default base locale. This is the language in which everything in your system will be saved in the database - all the product names, texts on the website, e-mails, etc.

### Locale Context

To manage the currently used language, we use the **LocaleContext**. You can always access it with the ID `sylius.context.locale` in the container.

<pre class="language-php"><code class="lang-php"><strong>public function fooAction()
</strong>{
    $locale = $this->get('sylius.context.locale')->getLocaleCode();
}
</code></pre>

The locale context can be injected into any of your services and give you access to the currently used locale.

### Available Locales Provider

The Locale Provider service (`sylius.locale_provider`) is responsible for returning all languages available for the current user. By default, returns all configured locales. You can easily modify this logic by overriding this service.

```php
public function fooAction()
{
    $locales = $this->get('sylius.locale_provider')->getAvailableLocalesCodes();

    foreach ($locales as $locale) {
        echo $locale;
    }
}
```

To get all languages configured in the store, regardless of your availability logic, use the locales repository:

```php
$locales = $this->get('sylius.repository.locale')->findAll();
```
