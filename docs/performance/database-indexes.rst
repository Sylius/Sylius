Database indexes
================

Indexing tables allow you to decrease fetching time from the database.

As an example, let's take a look at the customers' list.

The default index page is sorted by registration date, to create a table index all you need to do is modify `\App\Entity\Customer\Customer` entity and add the index using annotations. This class can be found at `src/Entity/Customer/Customer.php`. Once open, paste the following code:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Customer;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\Customer as BaseCustomer;

    /**
     * @ORM\Entity
     * @ORM\Table(name="customer",indexes={@Index(name="created_at_index", fields={"created_at"})})
     */
    class Customer extends BaseCustomer
    {
    }

.. note::

    You can learn more here about `ORM annotations <https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/annotations-reference.html#annref_haslifecyclecallbacks>`_

This should be considered for the most common sorting in your application.

Using this solution you can increase speed of customer listing by around 10%.
