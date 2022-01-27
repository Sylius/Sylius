Database indexes
================

Indexing tables allow you to decrease fetching time from the database.

As an example, let's take a look at the customers' list.

The default index page is sorted by registration date, to create a table index all you need to do is modify `App\Entity\Customer\Customer` entity and add the index using annotations.

In this file, add indexes attribute into the table configuration:

.. code-block:: php

    /**
    * @ORM\Table(name="sylius_customer", indexes={@ORM\Index(name="created_at_index", columns={"created_at"})})
    */

Your class should now look like this:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Customer;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\Customer as BaseCustomer;

    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_customer",indexes={@ORM\Index(name="created_at_index", columns={"created_at"})})
     */
    class Customer extends BaseCustomer
    {
    }

.. note::

    You can learn more here about `ORM annotations <https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/annotations-reference.html#annref_haslifecyclecallbacks>`_

This should be considered for the most common sorting in your application.

.. note::

    Using this solution you can increase speed of customer listing by around 10%.
    Indexes should be used when working with huge tables, otherwise it doesnt really affect loading times.
