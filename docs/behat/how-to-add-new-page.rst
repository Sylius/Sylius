How to add a new page object?
=============================

Sylius uses a solution inspired by ``SensioLabs/PageObjectExtension``, which provides an infrastructure to create
pages that encapsulates all the user interface manipulation in page objects.

To create a new page object it is needed to add a service in Behat container in ``etc/behat/services/pages.xml`` file:

.. code-block:: xml

    <service id="sylius.behat.page.PAGE_NAME" class="%sylius.behat.page.PAGE_NAME.class%" parent="sylius.behat.symfony_page" public="false" />

.. note::

    There are some boilerplates for common pages, which you may use. The available parents are ``sylius.behat.page`` (``Sylius\Behat\Page\Page``)
    and ``sylius.behat.symfony_page`` (``Sylius\Behat\Page\SymfonyPage``). It is not required for a page to extend any class as
    pages are POPOs (Plain Old PHP Objects).

Then you will need to add that service as a regular argument in context service.

The simplest Symfony-based page looks like:

.. code-block:: php

    use Sylius\Behat\Page\SymfonyPage;

    class LoginPage extends SymfonyPage
    {
        public function getRouteName()
        {
            return 'sylius_user_security_login';
        }
    }
