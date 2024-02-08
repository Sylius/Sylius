How to add Facebook login?
==========================

For integrating social login functionalities Sylius uses the `HWIOAuthBundle <https://github.com/hwi/HWIOAuthBundle/blob/master/Resources/doc/index.md>`_.
Here you will find the tutorial for integrating Facebook login into Sylius:

Set up the HWIOAuthBundle
-------------------------

* Add HWIOAuthBundle to your project:

.. code-block:: bash

    composer require hwi/oauth-bundle php-http/httplug-bundle

`php-http/httplug-bundle` is optional, require this dependency if you don't want to provide your own services.
For more information, please visit `Setting up HWIOAuthBundle <https://github.com/hwi/HWIOAuthBundle/blob/master/Resources/doc/1-setting_up_the_bundle.md#a-add-hwioauthbundle-to-your-project>`_.

* Enable the bundle:

.. code-block:: php

    // config/bundles.php

    return [
        // ...
        Http\HttplugBundle\HttplugBundle::class => ['all' => true], // If you require the php-http/httplug-bundle package.
        HWI\Bundle\OAuthBundle\HWIOAuthBundle::class => ['all' => true],
    ];

* Import the routing:

.. code-block:: yaml

    # config/routes.yaml
    hwi_oauth_redirect:
        resource: "@HWIOAuthBundle/Resources/config/routing/redirect.xml"
        prefix: /connect

    hwi_oauth_connect:
        resource: "@HWIOAuthBundle/Resources/config/routing/connect.xml"
        prefix: /connect

    hwi_oauth_login:
        resource: "@HWIOAuthBundle/Resources/config/routing/login.xml"
        prefix: /login

    facebook:
        path: /login/check-facebook

Configure the connection to Facebook
------------------------------------

.. note::

    To properly connect to Facebook you will need a `Facebook developer account <https://developers.facebook.com>`_.
    Having an account create a new `app for your website <https://developers.facebook.com/quickstarts/?platform=web>`_.
    In your app dashboard you will have the ``client_id`` (App ID) and the ``client_secret`` (App Secret),
    which are needed for the configuration.

.. code-block:: yaml

    # config/packages/hwi_oauth.yaml
    hwi_oauth:
        firewall_names: [shop]
        resource_owners:
            facebook:
                type: facebook
                client_id: <client_id>
                client_secret: <client_secret>
                scope: "email"

Sylius uses email as the username, that's why we choose emails as ``scope`` for this connection.

.. tip::

    If you cannot connect to your localhost with the Facebook app, configure its settings in such a way:

    * **App Domain**: ``localhost``
    * Click ``+Add Platform`` and choose "Website" type.
    * Provide the **Site URL** of the platform - your local server on which you run Sylius: ``https://localhost:8000``

    Alternatively, you could temporarily expose your localhost to be publicly accessible, using a tool like `ngrok <https://ngrok.com/>`_.
    Facebook app configuration would be similar to:

    * **App Domain**: ``abcde12345.ngrok.io``
    * **Site URL** ``https://abcde12345.ngrok.io``

Configure the security layer
----------------------------

As Sylius already has a service that implements the **OAuthAwareUserProviderInterface** - ``sylius.oauth.user_provider`` - we can only
configure the oauth firewall.
Under the ``security: firewalls: shop:`` keys in the ``security.yaml`` configure like below:

.. code-block:: yaml

    # config/packages/security.yaml
    security:
        firewalls:
            shop:
                oauth:
                    resource_owners:
                        facebook: "/login/check-facebook"
                    login_path: sylius_shop_login
                    use_forward: false
                    failure_path: sylius_shop_login

                    oauth_user_provider:
                        service: sylius.oauth.user_provider
                anonymous: true

Add facebook login button
-------------------------

You can for instance override the login template (``SyliusShopBundle/Resources/views/login.html.twig``) in the ``templates/SyliusShopBundle/login.html.twig``
and add these lines to be able to login via Facebook.

.. code-block:: twig

    <a href="{{ path('hwi_oauth_service_redirect', {'service': 'facebook' }) }}">
        <span>Login with Facebook</span>
    </a>

**Done!**

Learn more
----------

* `HWIOAuthBundle documentation <https://github.com/hwi/HWIOAuthBundle/blob/master/Resources/doc/index.md>`_
