How to add Facebook login?
==========================

For integrating social login functionalities Sylius uses the `HWIOAuthBundle <https://github.com/hwi/HWIOAuthBundle/blob/master/Resources/doc/index.md>`_.
Here you will find the tutorial for integrating Facebook login into Sylius:

Set up the HWIOAuthBundle
-------------------------

* Add HWIOAuthBundle to your project:

.. code-block:: bash

    $ composer require hwi/oauth-bundle

* Enable the bundle in the ``AppKernel.php``:

.. code-block:: php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
        );
    }

* Import the routing:

.. code-block:: yaml

    # app/config/routing.yml
    hwi_oauth_redirect:
        resource: "@HWIOAuthBundle/Resources/config/routing/redirect.xml"
        prefix:   /connect

    hwi_oauth_connect:
        resource: "@HWIOAuthBundle/Resources/config/routing/connect.xml"
        prefix:   /connect

    hwi_oauth_login:
        resource: "@HWIOAuthBundle/Resources/config/routing/login.xml"
        prefix:   /login

    facebook:
        path: "/login/check-facebook"

Configure the connection to Facebook
------------------------------------

.. note::

    To properly connect to Facebook you will need a `Facebook developer account <http://developers.facebook.com>`_.
    Having an account create a new `app for your website <https://developers.facebook.com/quickstarts/?platform=web>`_.
    In your app dashboard you will have the ``client_id`` (App ID) and the ``client_secret`` (App Secret),
    which are needed for the configuration.

.. code-block:: yaml

    # app/config/config.yml
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
    * Provide the **Site URL** of the platform - your local server on which you run Sylius: ``http://localhost:8000``

Configure the security layer
----------------------------

As Sylius already has a service that implements the **OAuthAwareUserProviderInterface** - ``sylius.oauth.user_provider`` - we can only
configure the oauth firewall.
Under the ``security: firewalls: shop:`` keys in the ``security.yml`` configure like below:

.. code-block:: yaml

    # app/config/security.yml
    security:
        firewalls:
            shop:
                oauth:
                    resource_owners:
                        facebook: "/login/check-facebook"
                    login_path: /login
                    use_forward: false
                    failure_path: /login

                    oauth_user_provider:
                        service: sylius.oauth.user_provider
                anonymous: true

Add facebook login button
-------------------------

You can for instance override the login template (``SyliusShopBundle/Resources/views/login.html.twig``) in the ``app/Resources/SyliusShopBundle/views/login.html.twig``
and add these lines to be able to login via Facebook.

.. code-block:: twig

    <a href="{{ path('hwi_oauth_service_redirect', {'service': 'facebook' }) }}">
        <span>Login with Facebook</span>
    </a>

**Done!**

Learn more
----------

* `HWIOAuthBundle documentation <https://github.com/hwi/HWIOAuthBundle/blob/master/Resources/doc/index.md>`_
