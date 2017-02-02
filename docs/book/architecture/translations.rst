.. index::
   single: Translations

Translations
============

Sylius uses the approach of personal translations - where each entity is bound with a translation entity, that has it's
own table (instead of keeping all translations in one table for the whole system).
This results in having the ``ProductTranslation`` class and ``sylius_product_translation`` table for the ``Product`` entity.

The logic of handling translations in Sylius is in the **ResourceBundle**

The fields of an entity that are meant to be translatable are saved on the translation entity, only their getters and setters
are also on the original model.

Let's see an example:

Assuming that we would like to have a translatable model of a ``Supplier``, we need a Supplier class and a SupplierTranslation class.

.. code-block:: php

   <?php

   namespace AppBundle\Entity;

   use Sylius\Component\Resource\Model\AbstractTranslation;

   class SupplierTranslation extends AbstractTranslation
   {
       /**
        * @var string
        */
       protected $name;

       /**
        * @return string
        */
       public function getName()
       {
           return $this->name;
       }

       /**
        * @param string $name
        */
       public function setName($name)
       {
           $this->name = $name;
       }
   }

The actual entity has access to its translation by using the ``TranslatableTrait`` which provides the ``getTranslation()`` method.

.. warning::

   Remember that the **Translations collection** of the entity
   (from the TranslatableTrait) has to be initialized in the constructor!

.. code-block:: php

   <?php

   namespace AppBundle\Entity;

   use Sylius\Component\Resource\Model\TranslatableInterface;
   use Sylius\Component\Resource\Model\TranslatableTrait;

   class Supplier implements TranslatableInterface
   {
       use TranslatableTrait {
           __construct as private initializeTranslationsCollection;
       }

       public function __construct()
       {
           $this->initializeTranslationsCollection();
       }

       /**
        * @return string
        */
       public function getName()
       {
           return $this->getTranslation()->getName();
       }

       /**
        * @param string $name
        */
       public function setName($name)
       {
           $this->getTranslation()->setName($name);
       }
   }

Fallback Translations
---------------------

The ``getTranslation()`` method gets a translation for the current locale, while we are in the shop, but we can also manually
impose the locale - ``getTranslation('pl_PL')`` will return a polish translation **if there is a translation in this locale**.

But when the translation for the chosen locale is unavailable, instead the translation for the **fallback locale**
(the one that was either set in ``config.yml`` or using the ``setFallbackLocale()`` method from the TranslatableTrait on the entity) is used.

How to add a new translation programmatically?
----------------------------------------------

You can programmatically add a translation to any of the translatable resources in Sylius.
Let's see how to do it on the example of a ProductTranslation.

.. code-block:: php

   // Find a product to add a translation to it

   /** @var ProductInterface $product */
   $product = $this->container->get('sylius.repository.product')->findOneBy(['code' => 'radiohead-mug-code']);

   // Create a new translation of product, give it a translated name and slug in the chosen locale

   /** @var ProductTranslation $translation */
   $translation = new ProductTranslation();

   $translation->setLocale('pl_PL');
   $translation->setName('Kubek Radiohead');
   $translation->setSlug('kubek-radiohead');

   // Add the translation to your product
   $product->addTranslation($translation);

   // Remember to save the product after adding the translation
   $this->container->get('sylius.manager.product')->flush($product);

Learn more
----------

* :doc:`Resource - translations documentation </components/Resource/translation>`
* :doc:`Locales - concept documentation </book/configuration/locales>`
