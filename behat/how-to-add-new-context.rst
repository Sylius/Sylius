How to add a new context?
=========================

To add a new context it is needed to add an service in Behat container in ``etc/behat/services/contexts.xml`` file:

.. code-block:: xml

    <service id="sylius.behat.context.CONTEXT_CATEGORY.CONTEXT_NAME" class="%sylius.behat.context.CONTEXT_CATEGORY.CONTEXT_NAME.class%" scope="scenario">
        <tag name="sylius.behat.context" />
    </service>

Then you can use it in your suite configuration:

.. code-block:: yaml

    default:
        suites:
            SUITE_NAME:
                contexts_as_services:
                    - "sylius.behat.context.CONTEXT_CATEGORY.CONTEXT_NAME"

                filters:
                    tags: "@SUITE_TAG"

.. note::

    The context categories are usually one of ``hook``, ``setup``, ``ui`` and ``domain``.
