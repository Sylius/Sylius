How to encrypt gateway config stored in the database?
=====================================================

**1.** Add defuse/php-encryption to your project
.. code-block:: 

    composer require defuse/php-encryption

**2.** Generate your Defuse Secret Key by executing the following script:

.. code-block:: php

    <?php

    use Defuse\Crypto\Key;

    require_once 'vendor/autoload.php';

    var_dump(Key::createNewRandomKey()->saveToAsciiSafeString());

**3.** Store your generated key in a parameter in ``app/config/parameters.yml``.

.. code-block:: yaml

    # app/config/parameters.yml

    parameters:
        # ...
        defuse_secret: "YOUR_GENERATED_KEY"

**4.** Add the following code to the application configuration in the ``app/config/config.yml``.

.. code-block:: yaml

    # app/config/config.yml

    payum:
        dynamic_gateways:
            encryption:
                defuse_secret_key: "%defuse_secret%"

**5.** Existing gateway configs will be automatically encrypted when updated. New gateway configs will be encrypted by default.
