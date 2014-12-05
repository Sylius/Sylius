.. index::
   single: E-Mails

E-Mails
=======

Sylius is sending various e-mails and this chapter is a reference about all of them. Continue reading to learn what e-mails are sent, when and how to customize the templates.

Customer Welcome E-Mail
-----------------------

Every time new customer registers via registration form or checkout, this e-mail is sent to him.

.. code-block:: text

    SyliusWebBundle:Frontend/Email:customerWelcome.html.twig

Order Confirmation
------------------

This e-mail is sent when order is paid. Template name is:

.. code-block:: text

    SyliusWebBundle:Frontend/Email:orderConfirmation.html.twig

Order Comment
-------------

In the backend, you can comment orders and optionally notify the customer, this template is used:

.. code-block:: text

    SyliusWebBundle:Frontend/Email:orderComment.html.twig

Final Thoughts
--------------

...

Learn more
----------

* ...
