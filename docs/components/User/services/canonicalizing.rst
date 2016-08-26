Canonicalization
================

In order to be able to query or sort by some string, we should normalize it.
The most common use case for that is canonical email or username. We can
then allow for case insensitive users identification by email or username.

Canonicalizer
-------------

User component offers simple canonicalizer which converts given string to lowercase
letters. Example usage:

.. code-block:: php

    // File example: src/script.php
    <?php

    // update this to the path to the "vendor/"
    // directory, relative to this file
    require_once __DIR__.'/../vendor/autoload.php';

    use Sylius\Component\User\Model\User;
    use Sylius\Component\Canonicalizer\Canonicalizer;

    $canonicalizer = new Canonicalizer();

    $user = new User();
    $user->setEmail('MyEmail@eXample.Com');

    $canonicalEmail = $canonicalizer->canonicalize($user->getEmail());
    $user->setEmailCanonical($canonicalEmail);

    $user->getEmail() // returns 'MyEmail@eXample.Com'
    $user->getEmailCanonical() // returns 'myemail@example.com'
