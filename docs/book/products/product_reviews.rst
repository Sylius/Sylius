.. index::
    single: Product Reviews

Product Reviews
===============

Product Reviews are a marketing tool that let your customers to give opinions about the products they buy in your shop.
They have a ``rating`` and ``comment``.

Rating
------

The rating of a product review is required and must be between 1 and 5.

Product review state machine
----------------------------

When you look inside the ``CoreBundle/Resources/config/app/state_machine/sylius_review.yml`` you will find out that a Review can have
3 different states:

* ``new``,
* ``accepted``,
* ``rejected``

There are only two possible transitions: ``accept`` (from ``new`` to ``accepted``) and ``reject`` (from ``new`` to ``rejected``).

.. image:: ../../_images/sylius_product_review.png
    :align: center
    :scale: 70%

When a review is accepted **the average rating of a product is updated**.

How is the average rating calculated?
-------------------------------------

The average rating is updated by
the `AverageRatingUpdater <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/ReviewBundle/Updater/AverageRatingUpdater.php>`_ service.

It has inside the `AverageRatingCalculator <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Review/Calculator/AverageRatingCalculator.php>`_,
and uses it inside the ``updateFromReview`` method.

How to add a ProductReview programmatically?
--------------------------------------------

Create a new review using a factory:

.. code-block:: php

    /** @var ReviewInterface $review */
    $review = $this->container->get('sylius.factory.product_review')->createNew();

Fill the content of your review.

.. code-block:: php

    $review->setTitle('My Review');
    $review->setRating(5);
    $review->setComment('This product is really great');

Then get a customer from the repository, which you would like to make an author of this review.

.. code-block:: php

    $customer = $this->container->get('sylius.repository.customer')->findOneBy(['email' => 'john.doe@test.com']);

    $review->setAuthor($customer);

Remember to set the object that is the subject of your review and then add the review to the repository.

.. code-block:: php

    $review->setReviewSubject($product);

    $this->container->get('sylius.repository.product_review')->add($review);

Learn more
----------

* :doc:`Product - Bundle Documentation </bundles/SyliusProductBundle/index>`
* :doc:`Product - Component Documentation </components/Product/index>`
