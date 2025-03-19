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

# Contact

The functionality of contacting the shop support/admin in Sylius is very basic. Each **Channel** of your shop may have a `contactEmail` configured on it. This will be the email address to support.

### Contact form

The contact form can be found on the `/contact` route.

{% hint style="info" %}
When the `contactEmail` is not configured on the channel, the customer will see the following flash message:

\
<img src="../../.gitbook/assets/contact_request_error.png" alt="" data-size="original">
{% endhint %}

The form itself has only two fields `email` (which will be filled automatically for the logged-in users) and `message`.

### ContactEmailManager

The **ContactEmailManager** service is responsible for the sending of a contact request email. It can be found under the `sylius.email_manager.contact` service id.

### ContactController

The controller responsible for the request action handling is the **ContactController**. It has the `sylius.controller.shop.contact` service id.

### Configuration

The routing for contact can be found in the `Sylius/Bundle/ShopBundle/Resources/config/routing/contact.yml` file. By overriding that routing you will be able to customize **redirect url, error flash, success flash, form, and its template**.

You can also change the template of the email that is being sent by simply overriding it in your project in the `templates/bundles/SyliusShopBundle/Email/contactRequest.html.twig` file.

### Learn more

* [Emails - Documentation](e-mails.md)
