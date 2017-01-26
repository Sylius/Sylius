.. index::
   single: Translations

Translations
============

Sylius uses the approach of personal translations - where each entity is binded with a translation entity, that has it's
own table (instead of keeping all translations in one table for the whole system).
This results in having the ``ProductTranslation`` class and ``sylius_product_translation`` table for the ``Product`` entity.

The *engine* for handling translations in Sylius is the **ResourceBundle**

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
       public function setTitle($name)
       {
           $this->name = $name;
       }
   }

The actual entity get access to its translation by using the ``TranslatableTrait`` which provides the ``getTranslation()`` method.

.. code-block:: php

<?php

namespace AppBundle\Entity;

use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;

class Supplier implements TranslatableInterface
{
    use TranslatableTrait;

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

The ``getTranslation()`` method get a translation for the current locale, while we are in the shop, but we can also manualy
impose the locale - ``getTranslation('pl_PL')`` will return a polish translation.

Learn more
----------

* :doc:`Resource - translations documentation </components/Resource/translation>`
* :doc:`Locales - concept documentation </book/configuration/locales>`
