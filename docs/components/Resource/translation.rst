Translations
============

.. _component_resource_translations_abstract-translation:

Implementing AbstractTranslation
--------------------------------

First let's create a class which will keep our translatable properties:

.. code-block:: php

   <?php

   namespace Example\Model;

   use Sylius\Component\Resource\Model\AbstractTranslation;

   class BookTranslation extends AbstractTranslation
   {
       /**
        * @var string
        */
       private $title;

       /**
        * @return string
        */
       public function getTitle()
       {
           return $this->title;
       }

       /**
        * @param string $title
        */
       public function setTitle($title)
       {
           $this->title = $title;
       }
   }

.. _component_resource_translations_translatable-trait:

Using TranslatableTrait
-----------------------

Now the following class will be actually capable of translating the **title**:

.. code-block:: php

   <?php

   namespace Example\Model;

   use Sylius\Component\Resource\Model\TranslatableInterface;
   use Sylius\Component\Resource\Model\TranslatableTrait;

   class Book implements TranslatableInterface
   {
       use TranslatableTrait;

       /**
        * @return string
        */
       public function getTitle()
       {
           return $this->getTranslation()->getTitle();
       }

       /**
        * @param string $title
        */
       public function setTitle($title)
       {
           $this->getTranslation()->setTitle($title);
       }
   }

.. note::
   As you could notice, inside both methods we use the ``getTranslation`` method.
   More specified explanation on what it does is described further on.

.. _component_resource_translations_usage:

Using Translations
------------------

Once we have both classes implemented we can start translating.
So first we need to create a few instances of our translation class:

.. code-block:: php

   <?php

   use Example\Model\Book;
   use Example\Model\BookTranslation;

   $englishBook = new BookTranslation();
   $englishBook->setLocale('en');
   $englishBook->setTitle("Harry Potter and the Philosopher's Stone");
   // now we have a title set for the english locale

   $spanishBook = new BookTranslation();
   $spanishBook->setLocale('es');
   $spanishBook->setTitle('Harry Potter y la Piedra Filosofal');
   // spanish

   $germanBook = new BookTranslation();
   $germanBook->setLocale('de');
   $germanBook->setTitle('Harry Potter und der Stein der Weisen');
   // and german

When we already have our translations, we can work with the **Book**:

.. code-block:: php

   <?php

   $harryPotter = new Book();

   $harryPotter->addTranslation($englishBook);
   $harryPotter->addTranslation($spanishBook);
   $harryPotter->addTranslation($germanBook);

   $harryPotter->setFallbackLocale('en'); // the locale which translation should be used by default

   $harryPotter->setCurrentLocale('es'); // the locale which translation we want to get

   $harryPotter->getTitle(); // returns 'Harry Potter y la Piedra Filosofal'

   $harryPotter->setCurrentLocale('ru');

   $harryPotter->getTitle(); // now returns "Harry Potter and the Philosopher's Stone"
                             // as the translation for chosen locale is unavailable,
                             // instead the translation for fallback locale is used

You can always use the ``getTranslation`` method by itself, but the same principal is in play:

.. code-block:: php

   <?php

   $harryPotter->getTranslation('de');  // returns $germanBook
   // but
   $harryPotter->getTranslation();
   // and
   $harryPotter->getTranslation('hi');
   // both return $englishBook

.. caution::
   The ``getTranslation`` method throws `\\RuntimeException`_ in two cases:

   * No locale has been specified in the parameter and the current locale is undefined
   * No fallback locale has been set

.. _\\RuntimeException: https://secure.php.net/manual/pl/class.runtimeexception.php

.. _component_resource_provider_locale-provider:

LocaleProvider
--------------

This service provides you with an easy way of managing locales.
The first parameter set in it's constructor is the current locale and the second, fallback.

In this example let's use the provider with our `Book`
class which uses the :ref:`component_resource_translations_translatable-trait`:

.. code-block:: php

   <?php

   use Example\Model\Book;
   use Sylius\Component\Resource\Provider\LocaleProvider;

   $provider = new LocaleProvider('de', 'en');

   $book = new Book();

   $book->setCurrentLocale($provider->getCurrentLocale());
   $book->setFallbackLocale($provider->getFallbackLocale());

   $book->getCurrentLocale(); // returns 'de'
   $book->getFallbackLocale(); // returns 'en'

... and with an :ref:`component_resource_translations_abstract-translation`
class such as the exemplary `BookTranslation` it goes:

.. code-block:: php

   <?php

   use Example\Model\BookTranslation;
   use Sylius\Component\Resource\Provider\LocaleProvider;

   $provider = new LocaleProvider('de', 'en');

   $bookTranslation = new BookTranslation();

   $bookTranslation->setLocale($provider->getCurrentLocale());

   $translation->getLocale(); // returns 'de'

.. note::
   This service implements the :ref:`component_resource_provider_locale-provider-interface`.
