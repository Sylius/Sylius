The Report
============

Retrieving reports
-------------------

Retrieving a report from the database should always happen via repository, which always implements ``Sylius\Bundle\ResourceBundle\Model\RepositoryInterface``.
If you are using Doctrine, you're already familiar with this concept, as it extends the native Doctrine ``ObjectRepository`` interface.

Your report repository is always accessible via the ``sylius.repository.report`` service.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.report');
    }

Retrieving reports is simple as calling proper methods on the repository.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.report');

        $report = $repository->find(4); // Get report with id 4, returns null if not found.
        $report = $repository->findOneBy(array('name' => 'My Super Report')); // Get one report by defined criteria.

        $reports = $repository->findAll(); // Load all the reports!
        $reports = $repository->findBy(array('renderer' => 'table')); // Find reports matching some custom criteria.
    }

Creating new report object
---------------------------

To create new report instance, you can simply call ``createNew()`` method on the repository.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.report');
        $report = $repository->createNew();
    }

.. note::

    Creating a report via this factory method makes the code more testable, and allows you to change the report class easily.

Saving & removing report
-------------------------

To save or remove a report, you can use any ``ObjectManager`` which manages Report. You can always access it via alias ``sylius.manager.report``.
But it's also perfectly fine if you use ``doctrine.orm.entity_manager`` or other appropriate manager service.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.report');
        $manager = $this->container->get('sylius.manager.report'); // Alias for appropriate doctrine manager service.

        $report = $repository->createNew();

        $report
            ->setName('Foo')
            ->setDescription('Nice report')
        ;

        $manager->persist($report);
        $manager->flush(); // Save changes in database.
    }

To remove a report, you also use the manager.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.report');
        $manager = $this->container->get('sylius.manager.report');

        $report = $repository->find(1);

        $manager->remove($report);
        $manager->flush(); // Save changes in database.
    }
