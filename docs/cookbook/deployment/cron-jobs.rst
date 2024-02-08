How to prepare simple CRON jobs?
================================

What are CRON jobs?
-------------------

This is what we call scheduling repetitive task on the server. In web applications this will be mainly
repetitively running specific commands.

CRON jobs in Sylius
-------------------

Sylius has two vital, predefined commands designed to be run as cron jobs on your server.

* ``sylius:remove-expired-carts`` - to remove carts that have expired after desired time
* ``sylius:cancel-unpaid-orders`` - to cancel orders that are still unpaid after desired time

The recommended configuration

.. code-block:: bash

    0 */6 * * * sh php bin/console sylius:remove-expired-carts
    0 */6 * * * sh php bin/console sylius:cancel-unpaid-orders

How to configure a CRON job ?
-----------------------------

.. tip::

    Learn more here: `Cron and Crontab usage and examples <https://www.pantz.org/software/cron/croninfo.html>`_.
