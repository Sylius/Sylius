How to add a new context?
=========================

To add a new context to Behat container it is needed to add a service in to one of a following files ``cli.xml``/``domain.xml``/``hook.xml``/``setup.xml``/``transform.xml``/``ui.xml`` in ``src/Sylius/Behat/Resources/config/services/contexts/`` folder:

.. code-block:: xml

    <service id="sylius.behat.context.CONTEXT_CATEGORY.CONTEXT_NAME" class="%sylius.behat.context.CONTEXT_CATEGORY.CONTEXT_NAME.class%">
        <tag name="fob.context_service" />
    </service>

Then you can use it in your suite configuration:

.. code-block:: yaml

    default:
        suites:
            SUITE_NAME:
                contexts_services:
                    - "sylius.behat.context.CONTEXT_CATEGORY.CONTEXT_NAME"

                filters:
                    tags: "@SUITE_TAG"

.. note::

    The context categories are usually one of ``hook``, ``setup``, ``ui`` and ``domain`` and, as you can guess, they are corresponded to files name mentioned above.
