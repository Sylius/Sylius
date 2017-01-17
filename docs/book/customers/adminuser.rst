.. index::
   single: AdminUser

AdminUser
=========

The **AdminUser** entity extends the **User** entity. It is created to enable administrator accounts that have access to the administration panel.

How to create an AdminUser programmatically?
--------------------------------------------

The **AdminUser** is created just like every other entity, it has its own factory. By default it will have an administration **role** assigned.

.. code-block:: php

   /** @var AdminUserInterface $admin */
   $admin = $this->container->get('sylius.factory.admin_user')->createNew();

   $admin->setEmail('administrator@test.com');
   $admin->setPlainPassword('pswd');

   $this->container->get('sylius.repository.admin_user')->add($admin);

Administration Security
-----------------------

In **Sylius** by default you have got the administration panel routes (``/admin/*``) secured by a firewall - its configuration
can be found in the `security.yml <https://github.com/Sylius/Sylius/blob/master/app/config/security.yml>`_ file.

Only the logged in **AdminUsers** are eligible to enter these routes.

Learn more
----------

* :doc:`Customer & ShopUser - Documentation </book/customers/customer_and_shopuser>`
