How to add Google Analytics script to shop?
===========================================

All shops tend to observe traffic on their websites, the most popular tool for that is Google Analytics.
In Sylius we have two ways to enable it:

*If you have the Sylius layout overridden completely then:*

* paste the script directly into :ref:`head section of the layout <google_analytics_to_head_section>`

or *if you are just customizing the Sylius layout, and you will be updating to future versions then:*

* add the script :ref:`via Sonata events <google_analytics_via_sonata_events>`

.. _google_analytics_to_head_section:

Adding Google Analytics by pasting the script directly into the layout template.
--------------------------------------------------------------------------------

If you want to add Google Analytics by this way, you need to override the ``layout.html.twig`` in ``templates/bundles/SyliusShopBundle/layout.html.twig``.

.. code-block:: twig

    {# templates/bundles/SyliusShopBundle/layout.html.twig #}

    {# rest of layout.html.twig code #}

    {% block metatags %}
    {% endblock %}

    {% block google_script %}
        <!-- Google Analytics -->
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-XXXXX-Y', 'auto');
            ga('send', 'pageview');
        </script>
        <!-- End Google Analytics -->
    {% endblock %}

    {% block stylesheets %}
    {% endblock %}

    {# rest of layout.html.twig code #}

.. _google_analytics_via_sonata_events:

Adding Google Analytics script with Sonata events.
--------------------------------------------------

If you want to add Google Analytics by sonata event you need to add a new file, create the file ``googleScript.html.twig`` in ``/templates/layout.html.twig``.

.. code-block:: twig

    {# templates/googleScript.html.twig#}

    <!-- Google Analytics -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-XXXXX-Y', 'auto');
        ga('send', 'pageview');
    </script>
    <!-- End Google Analytics -->

Now, we need to configure a new service.

.. code-block:: yaml

    # config/packages/_sylius.yaml
    app.block_event_listener.layout.after_stylesheets:
        class: Sylius\Bundle\UiBundle\Block\BlockEventListener
        arguments:
            - 'googleScript.html.twig'
        tags:
            - { name: kernel.event_listener, event: sonata.block.event.sylius.shop.layout.stylesheets, method: onBlockEvent }

Learn more
----------

* `Google Analytics Documentation <https://developers.google.com/analytics/devguides/collection/analyticsjs>`_
