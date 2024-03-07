Sylius Plus
===========

In summary, Sylius Plus is a premium offering that provides enterprise eCommerce businesses with access to advanced features, functionalities,
and support beyond what is available in the open-source version of Sylius. Its proprietary nature and license requirement make it accessible only to users
with a valid license, ensuring that Sylius Plus delivers value and differentiation to its subscribers in the competitive eCommerce landscape.

How to install Sylius Plus modules?
-----------------------------------

Sylius Plus modules are the Sylius plugins (so the Symfony bundles), which need to be defined in your composer.json file as described below:

.. code-block:: json
    {
        "require": {
            "sylius/customer-service-plugin": "^0.2.0",
            "sylius/loyalty-plugin": "^0.2.0",
            "sylius/multi-source-inventory-plugin": "^0.2.0",
            "sylius/multi-store-plugin": "^0.2.0",
            "sylius/paypal-plugin": "^1.5",
            "sylius/plus-rbac-plugin": "^0.2.0",
            "sylius/refund-plugin": "^1.4",
            "sylius/return-plugin": "^0.2.0",
        },
        "repositories": {
            "private-packagist": {
                "type": "composer",
                "url": "https://sylius.repo.packagist.com/<YOUR-CLIENT-NAME>/"
            }
        }
    }

Since all Sylius Plus modules are private packages, you need to configure the private packagist repository. You'll receive the repository URL from our sales.

Enabling Sylius Plus token on Platform.sh
-----------------------------------------

Along with the repository URL you'll receive an authentication token, which is needed to be configured in your Platform.sh environment. It can't be hardcoded into `composer.json` file
as it is a very sensitive information. The best way is to configure it by creating an environment variable, which is automatically read by composer:

.. code-block:: bash
    platform variable:create --level project --name env:COMPOSER_AUTH \
        --json true --visible-runtime false --sensitive true --visible-build true \
        --value '{"http-basic": {"repo.packagist.com": {"username": "token", "password": "<YOUR_TOKEN>"}}'

After putting the environment variable you should be able to install Sylius Plus modules into your application.
