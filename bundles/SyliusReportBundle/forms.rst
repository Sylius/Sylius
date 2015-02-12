Forms
=====

The bundle ships with a useful form types for report model, but also for default renderers and data fetchers.

Report form
------------

The report form type is named ``sylius_report`` and you can create it whenever you need, using the form factory.

.. code-block:: php

    <?php

    // src/Acme/DemoBundle/Controller/DemoController.php

    namespace Acme\DemoBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;

    class DemoController extends Controller
    {
        public function fooAction(Request $request)
        {
            $form = $this->get('form.factory')->create('sylius_report');
        }
    }

The default report form consists of following fields.

+-----------------+----------+
| Field           | Type     |
+=================+==========+
| name            | text     |
+-----------------+----------+
| code            | text     |
+-----------------+----------+
| description     | textarea |
+-----------------+----------+
| renderer        | choice   |
+-----------------+----------+
| dataFetcher     | choice   |
+-----------------+----------+

You can render each of these using the usual Symfony way ``{{ form_row(form.description) }}``.

**SyliusReportBundle provides some default data fetchers and renderers. Each of them have custom configuration and adds diffrent part to report form.**

Data fetchers
##############

Basic data fetcher extend same time provider. This implementation results in same configuration fields for three different data fetchers.

.. caution::

    Default data fetchers are part of SyliusCoreBundle, cause they are strictly connected with Sylius database.

Already available data fetchers:
    * User registrations
    * Sales total
    * Numbers of orders

+---------------------------+-------------+
| Field                     | Type        |
+===========================+=============+
| Start date                | datetime    |
+---------------------------+-------------+
| End date                  | datetime    |
+---------------------------+-------------+
| Time period               | choice      |
+---------------------------+-------------+
| Print empty records?      | checkbox    |
+---------------------------+-------------+

Already available time periods:
    * Daily
    * Monthly
    * Yearly

.. note::

   "Print empty records?" is inconspicuous, but really important part of data fetcher form - it can make your report beautiful and clear, or ruin your day with tones of unusable data. Be aware of it! 


User registrations
""""""""""""""""""""
Provides statistic about user registration in time period

Sales total
""""""""""""""""""""
Provides statistic about total completed sales over time

Number of orders
""""""""""""""""""""
Provides statistic about number of completed orders over time

Renderers
############


Table Renderer
""""""""""""""""

+-----------------+----------+
| Field           | Type     |
+=================+==========+
| template        | choice   |
+-----------------+----------+

Already available templates:
    * Default - one simple table

Chart Renderer
""""""""""""""""

+-----------------+----------+
| Field           | Type     |
+=================+==========+
| type            | choice   |
+-----------------+----------+
| template        | choice   |
+-----------------+----------+

Already available types:
    * Bar chart
    * Line chart
    * Radar chart
    * Polar chart
    * Pie chart
    * Doughnut chart

.. note::

    All chart are rendered at html5 canvas element, with some defaults style and colors, via Chart.js plugin

Already available templates:
    * Default - one, full-width chart