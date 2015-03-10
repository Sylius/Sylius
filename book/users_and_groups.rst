.. index::
   single: Users and Groups

Users and Groups
================

Retrieving users and groups from the database should always happen via repository, which implements ``Sylius\Bundle\ResourceBundle\Model\RepositoryInterface``.
If you are using Doctrine, you're already familiar with this concept, as it extends the native Doctrine ``ObjectRepository`` interface.

Your user repository is always accessible via ``sylius.repository.user`` service.
Of course, ``sylius.repository.group`` is also available for use. Sometimes you will not need it because you can obtain a group directly from the User.

User Management
---------------

Creating users is quite straight forward. You simply call the ``createNew()`` method on the repository. Next you are able to set all the information.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.user');
        $user = $repository->createNew();
        $user->setEmail('john.doe@example.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');

        $manager = $this->container->get('sylius.manager.user');
        $manager->persist($user);
        $manager->flush(); // Save changes in database.
    }

Setting billing and shipping addresses is easy. First you need an Address object. This is created by calling the ``createNew()`` method on the repository.

Afterwards you can set all the data and assign the Address object to the user by using either ``setShippingAddress()`` or ``setBillingAddress()``.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $userRepository = $this->container->get('sylius.repository.user');
        $user = $userRepository->find(1);

        $addressRepository = $this->container->get('sylius.repository.address');
        $address = $addressRepository->createNew();

        $address->setFirstName('John');
        $address->setLastName('Doe');
        $address->setCity('New York');
        $address->setPostcode(2000);
        $address->setStreet('Times Sq. 137');

        $user->setBillingAddress($address);

        $manager = $this->container->get('sylius.manager.user');
        $manager->persist($user);
        $manager->flush(); // Save changes in database.
    }



Groups
------

To create a new group instance, you can simply call the ``createNew()`` method on the repository.

Do not forget setting a name for the group, it is a required field as it is not nullable. The name for the group must also be unique.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.group');
        $group = $repository->createNew();
        $group->setName('Premium customers');
    }

You can now start adding users to your newly made group.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $groupRepository = $this->container->get('sylius.repository.group');
        $group = $groupRepository->findOneBy(array('name' => 'Premium customers');

        $userRepository = $this->container->get('sylius.repository.user');
        $user = $userRepository->find(1);
        $user->addGroup($group);

        $manager = $this->container->get('sylius.manager.group');
        $manager->persist($user);
        $manager->flush(); // Save changes in database.
    }

Final Thoughts
--------------

...

Learn more
----------

* ...
