Customizing Templates
=====================

.. note::

    There are two kinds of templates in Sylius. **Shop** and **Admin** ones, plus you can create your own to satisfy your needs.

Why would you customize a template?
-----------------------------------

The most important case for modifying the existing templates is of course **integrating your own layout of the system**.
Sometimes even if you have decided to stay with the default layout provided by Sylius, you need to **slightly modify it to meet your
business requirements**.
You may just need to **add your logo anywhere**.

Methods of templates customizing
--------------------------------

.. warning::

    There are three ways of customizing templates of Sylius:

    The first one is simple **templates overriding** inside of the ``templates/bundles`` directory of your project. Using
    this method you can completely change the content of templates.

    The second method is **templates customization via events**. You are able to listen on these template events,
    and by that add your own blocks without copying and pasting the whole templates. This feature is really useful
    when :doc:`creating Sylius Plugins </book/plugins/creating-plugin>`.

    The third method is **using Sylius themes**. Creating a Sylius theme requires a few more steps than basic template overriding,
    but allows you to have a different design on multiple channels of the same Sylius instance. :doc:`Learn more about themes here </book/themes/themes>`.

.. tip::

    You can browse the full implementation of these examples on `this GitHub Pull Request.
    <https://github.com/Sylius/Customizations/pull/16>`_


How to customize templates by overriding?
-----------------------------------------

.. note::

    How do you know which template you should be overriding?
    Go to the page that you are going to modify, at the bottom in the Symfony toolbar click on the route,
    which will redirect you to the profiler. In the Request Attributes section
    under ``_sylius [ template => ...]`` you can check the path to the current template.


* **Shop** templates: customizing Login Page template:

The default login template is: ``@SyliusShopBundle/login.html.twig``.
In order to override it you need to create your own: ``templates/bundles/SyliusShopBundle/login.html.twig``.

Copy the contents of the original template to make your work easier. And then modify it to your needs.

.. code-block:: twig

    {% extends '@SyliusShop/layout.html.twig' %}

    {% import '@SyliusUi/Macro/messages.html.twig' as messages %}

    {% block content %}
    <div class="ui column stackable center page grid">
        {% if last_error %}
            {{ messages.error(last_error.messageKey|trans(last_error.messageData, 'security')) }}
        {% endif %}

        {# You can add a headline for instance to see if you are changing things in the correct place. #}
        <h1>
            This Is My Headline
        </h1>

        <div class="five wide column"></div>
        <form class="ui six wide column form segment" action="{{ path('sylius_shop_login_check') }}" method="post" novalidate>
            <div class="one field">
                {{ form_row(form._username, {'value': last_username|default('')}) }}
            </div>
            <div class="one field">
                {{ form_row(form._password) }}
            </div>
            <div class="one field">
                <button type="submit" class="ui fluid large primary submit button">{{ 'sylius.ui.login_button'|trans }}</button>
            </div>
        </form>
    </div>
    {% endblock %}

Done! If you do not see any changes on the ``/shop/login`` url, clear your cache:

.. code-block:: bash

    php bin/console cache:clear

* **Admin** templates: Customization of the Country form view.

The default template for the Country form is: ``SyliusAdminBundle:Country:_form.html.twig``.
In order to override it you need to create your own: ``templates/bundles/SyliusAdminBundle/Country/_form.html.twig``.

Copy the contents of the original template to make your work easier. And then modify it to your needs.

.. code-block:: twig

    <div class="ui segment">
        {{ form_errors(form) }}
        {{ form_row(form.code) }}
        {{ form_row(form.enabled) }}
    </div>
    <div class="ui segment">

        {# You can add a headline for instance to see if you are changing things in the correct place. #}
        <h1>My Custom Headline</h1>

        <h4 class="ui dividing header">{{ 'sylius.ui.provinces'|trans }}</h4>
        {{ form_row(form.provinces, {'label': false}) }}
    </div>

Done! If you do not see any changes on the ``/admin/countries/new`` url, clear your cache:

.. code-block:: bash

    php bin/console cache:clear

How to customize templates via events?
--------------------------------------

Sylius uses its own event mechanism called Sylius Template Events which implementation is based purely on Twig.
This (compared to the legacy way of using SonataBlockBundle) leads to:

* better performance - as it is no longer based on EventListeners
* less boilerplate code - no need to register more Listeners
* easier variable pass - now you just need to add it to configuration file
* extended configuration - now you can change if block is enabled, change its template, or even priority

.. note::

    If you want to read more about the Sylius Template Events from developers/architectural perspective
    check the [Github Issue](https://github.com/Sylius/Sylius/issues/10997) referring this feature.

We will now guide you through a simple way of customizing your template with Sylius Template Events.

How to locate template events?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The events naming convention uses the routing to the place where we are adding it, but instead of ``_`` we are using ``.``,
followed by a slot name (like ``sylius_admin_customer_show`` route results in the ``sylius.admin.customer.show.slot_name`` events).
The slot name describes where exactly in the template's structure should the event occur, it will be ``before`` or ``after`` certain elements.

Although when the resource name is not just one word (like ``product_variant``) then the underscore stays in the event prefix string.
Then ``sylius_admin_product_variant_create`` route will have the ``sylius.admin.product_variant.create.slot_name`` events.

Let's see how the event is rendered in a default Sylius Admin template. This is the rendering of the event that occurs
on the create action of Resources, at the bottom of the page (after the content of the create form):

.. code-block:: twig

    {# First we are setting the event_prefix based on route as it was mentioned before #}
    {% set event_prefix = metadata.applicationName ~ '.admin.' ~ metadata.name ~ '.create' %}

    {# And then the slot name is appended to the event_prefix #}
    {{ sylius_template_event([event_prefix, 'sylius.admin.create'], _context) }}

.. note::

    Besides the events that are named based on routing, Sylius also has some other general events: those that will appear
    on every Sylius admin or shop. Examples: ``sylius.shop.layout.slot_name`` or ``sylius.admin.layout.slot_name``.
    They are rendered in the ``layout.html.twig`` views for both Admin and Shop.

.. tip::

    In order to find events in Sylius templates you can simply search for the ``sylius_template_event`` phrase in your
    project's directory.

How to locate rendered template event?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

With DevTools in your browser
"""""""""""""""""""""""""""""

If you want to search easier for the event name you want to modify, the Sylius Template Events can be easily
found in your browser with the debug tools it provides.
Just use the ``explore`` (in Chrome browser) or its equivalent in other browsers to check the HTML code of your webpage.
Here you will be able to see commented blocks where the name of the template as well as the event name will be shown:

.. image:: /_images/sylius_event_debug.png

In the example above we were looking for the HTML responsible for rendering of the Sylius Logo. Mentioned markup is surrounded
by statements of where the event, as well as block, started.
What is more, we can see which twig template is responsible for rendering this block and what the priority of this rendering is.

.. image:: /_images/sylius_logo_locate.png

This will have all the necessary information that you need for further customization.

With Symfony Profiler
"""""""""""""""""""""

The ``Template events`` section in Symfony Profiler gives you the list of events used to render the page with their blocks.
Besides all information about blocks mentioned in the above section, you will see one more especially beneficial when it
comes to optimization which is ``Duration``.

.. image:: /_images/sylius_template_events_metrics.png

How to use template events for customizations?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

When you have found an event in the place where you want to add some content, here's what you have to do.

Let's assume that you would like to add some content after the header in the Sylius shop views.
You will need to look at the ``SyliusShopBundle/Resources/views/layout.html.twig`` template,
which is the basic layout of Sylius shop, and then in it find the appropriate event.

For the space below the header it will be ``sylius.shop.layout.after_header``.

* Create a Twig template file that will contain what you want to add.

.. code-block:: twig

    {# templates/block.html.twig #}

    <h1> Test Block Title </h1>

* And configure Sylius UI to display it for the chosen event:

.. code-block:: yaml

    # config/packages/sylius_ui.yaml

    sylius_ui:
        events:
            sylius.shop.layout.after_header:
                blocks:
                    my_block_name: 'block.html.twig'

That's it. Your new block should appear in the view.

.. tip::

    Learn more about adding custom JS & CSS in the cookbook :doc:`here </book/frontend/managing-assets>`.

Passing variables to the template events
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

By default, Sylius Template Events provide all variables from the template. If you want to pass some additional
variables, you can do it with the ``context`` key in the configuration. Let's greet our customers at the top of
the homepage:

.. code-block:: twig

    {# templates/greeting.html.twig #}

    <h2>{{ message }}</h2>

.. code-block:: yaml

    # config/packages/sylius_ui.yaml

    sylius_ui:
        events:
            sylius.shop.homepage:
                blocks:
                    greeting:
                        template: 'greeting.html.twig'
                        priority: 70
                        context:
                            message: 'Hello!'

However, this simple way of passing variables may not be sufficient when you want to pass some complex data that comes
as a result of application logic. Perhaps you would like to greet customers with their names. In such cases, you need to
define your own ``Context Provider``.

Context Providers
"""""""""""""""""

Context Providers are responsible for providing context to the template events. The default one is the
``DefaultContextProvider`` which provides all variables from the template and from the context in the block's
configuration. You can have multiple Context Providers and they will provide their context to the template events with
the given priority with the ``sylius.ui.template_event.context_provider`` tag.

Let's do something fancier than just greeting customers with a name. Say happy birthday to the customer! To do so,
create a ``GreetingContextProvider`` that will provide the ``message`` variable from the example above but this time
depending on the customer's birthday:

    .. code-block:: php

        <?php

        declare(strict_types=1);

        namespace App\ContextProvider;

        use Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface;
        use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
        use Sylius\Component\Customer\Context\CustomerContextInterface;

        final class GreetingContextProvider implements ContextProviderInterface
        {
            public function __construct(private CustomerContextInterface $customerContext)
            {
            }

            public function provide(array $templateContext, TemplateBlock $templateBlock): array
            {
                $customer = $this->customerContext->getCustomer();

                if (null === $customer) {
                    return $templateContext;
                }

                $customerName = $customer->getFirstName() ?? $customer->getFullName();

                if (
                    null === $customer->getBirthday() ||
                    $customer->getBirthday()->format("m-d") !== (new \DateTime())->format("m-d")
                ) {
                    $templateContext['message'] = sprintf('Hello %s!', $customerName);
                } else {
                    $templateContext['message'] = sprintf('Happy Birthday %s!', $customerName);
                }

                return $templateContext;
            }

            public function supports(TemplateBlock $templateBlock): bool
            {
                return 'sylius.shop.homepage' === $templateBlock->getEventName()
                    && 'greeting' === $templateBlock->getName();
            }
        }

Register the new Context Provider as a service in the ``config/services.yaml``:

    .. code-block:: yaml

        services:
            # ...

            App\ContextProvider\GreetingContextProvider:
                arguments:
                    - '@sylius.context.customer'
                tags:
                    - { name: sylius.ui.template_event.context_provider }

Now if the customer's birthday is today, they will be greeted with a happy birthday message.

.. image:: /_images/sylius_template_events_greeting.png

What more can I do with the Sylius Template Events?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

You might think that this is the only way of customisation with the events, but you can also do more.

1. Disabling blocks:
    You can now disable some blocks that do not fit your usage, just put in config:

    .. code-block:: yaml

        sylius_ui:
            events:
                sylius.shop.layout.event_with_ugly_block:
                    blocks:
                        the_block_i_dont_like:
                            enabled: false

2. Change the priority of blocks:
    In order to override the templates from vendor, or maybe you are developing plugin you can change the priority of a block:

    .. code-block:: yaml

        sylius_ui:
            events:
                sylius.shop.layout.vendor_block:
                    blocks:
                        my_important_block:
                            priority: 1

3. Access variables:
    You can access variables by using the function:

    .. code-block:: html

        {{ dump() }}

    You can also access the resources and entities (in the correct views) variables:

    .. code-block:: html

        # for example in products show view
        {{ dump(product) }}

    Or you can pass any variable from the template to the block and access it with function:

    .. code-block:: html

        # Parent html
        ...
            {{ sylius_template_event('sylius.shop.product.show', {'customVariable': variable}) }}
        ...

    .. code-block:: html

        # Template html
        ...
            {{ dump(customVariable) }}
        ...

4. Override block templates:
    You can override the existing blocks by changing the config:

    .. code-block:: yaml

        # config.yaml
        sylius_ui:
            events:
                sylius.shop.layout.header.grid:
                    blocks:
                        logo: 'logo.html.twig'

    And adding your own template into `templates/logo.html.twig` folder.

    .. note::

        Check out the full example of overriding the template in :doc:`Shop Customizations </getting-started-with-sylius/shop-customizations>`

How to use themes for customizations?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

You can refer to the theme documentation available here:
- :doc:`Themes (The book) </book/themes/themes>`
- `SyliusThemeBundle (Bundle documentation) <https://github.com/Sylius/SyliusThemeBundle/blob/master/docs/index.md>`_

Global Twig variables
---------------------

Each of the Twig templates in Sylius is provided with the ``sylius`` variable,
that comes from the `ShopperContext <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Core/Context/ShopperContext.php>`_.

The **ShopperContext** is composed of ``ChannelContext``, ``CurrencyContext``, ``LocaleContext`` and ``CustomerContext``.
Therefore it has access to the current channel, currency, locale and customer.

The variables available in Twig are:

+---------------------+----------------------------+
| Twig variable       | ShopperContext method name |
+=====================+============================+
| sylius.channel      | getChannel()               |
+---------------------+----------------------------+
| sylius.currencyCode | getCurrencyCode()          |
+---------------------+----------------------------+
| sylius.localeCode   | getLocaleCode()            |
+---------------------+----------------------------+
| sylius.customer     | getCustomer()              |
+---------------------+----------------------------+

How to use these Twig variables?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

You can check for example what is the current channel by dumping the ``sylius.channel`` variable.

.. code-block:: twig

    {{ dump(sylius.channel) }}

That's it, this will dump the content of the current Channel object.

.. include:: /customization/plugins.rst
