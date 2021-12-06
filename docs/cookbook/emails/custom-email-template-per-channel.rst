How to customize email templates per channel?
=============================================

.. note::

    This cookbook is suitable for a clean :doc:`sylius-standard installation </book/installation/installation>`.
    For more general tips, while using `SyliusMailerBundle <https://github.com/Sylius/SyliusMailerBundle/blob/master/docs/index.md>`_
    go to `Sending configurable e-mails in Symfony Blogpost <https://sylius.com/blog/sending-configurable-e-mails-in-symfony>`_.

It is a common use-case to customize email templates depending on a channel in which an action was performed.

1. Pick a template to customize

You can find the list of all email templates and their data on :doc:`emails </book/architecture/emails>` documentation page.
Then, override that template following our :doc:`template customization </customization/template>` guide.

In this cookbook, let's assume that we want to customize the order confirmation email located at ``@SyliusShopBundle/Email/orderConfirmation.html.twig``.
This requires creating a new template in ``templates/bundles/SyliusShopBundle/Email/orderConfirmation.html.twig``.

2. Add an ``if`` statement for simple customizations

The simplest customization might be done with an ``if`` statement in the template.

.. code-block:: twig

    <!-- templates/bundles/SyliusShopBundle/Email/orderConfirmation.html.twig -->

    {% block subject %}Topic{% endblock %}

    {% block body %}{% autoescape %}
        {% if channel.code is same as ('TOY_STORE') %}
            Thanks for buying one of our toys!
        {% else %}
            Thanks for buying!
        {% endif %}

        Your order no. {{ order.number }} has been successfully placed.
    {% endautoescape %}{% endblock %}

3. Extract templates for more flexibility (optional)

If you require more flexibility, you can extract templates to standalone files.

.. code-block:: twig

    <!-- templates/bundles/SyliusShopBundle/Email/orderConfirmation.html.twig -->

    {% block subject %}Topic{% endblock %}

    {% block body %}{% autoescape %}
        {% include ['/Email/OrderConfirmation/' ~ sylius.channel.code ~ '.html.twig', '/Email/OrderConfirmation/_default.html.twig'] %}
    {% endautoescape %}{% endblock %}

The code snippet above will first try to load email body template based on channel code and will fall back to default template
if not found.

It is required now to create the default template in ``templates/Email/OrderConfirmation/_default.html.twig`` (this path is
the one defined above).

.. code-block:: twig

    <!-- templates/Email/OrderConfirmation/_default.html.twig -->

    Your order no. {{ order.number }} has been successfully placed.

And also the one specific for ``TOY_STORE`` channel in ``templates/Email/OrderConfirmation/TOY_STORE.html.twig``:

.. code-block:: twig

    <!-- templates/Email/OrderConfirmation/TOY_STORE.html.twig -->

    Thanks for buying one of our toys!

    Your order with number {{ order.number }} is currently being processed.

This way allows to keep independent templates with email contents based on the channel.
