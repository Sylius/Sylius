How to encrypt gateway config stored in the database?
=====================================================

**1.** Generate your Defuse Secret Key by executing the following script:

.. code-block:: php

    <?php

    use Defuse\Crypto\Key;

    require_once 'vendor/autoload.php';

    var_dump(Key::createNewRandomKey()->saveToAsciiSafeString());

**2.** Store your generated key in a parameter in ``app/config/parameters.yml``.

.. code-block:: yaml

    # app/config/parameters.yml

    parameters:
        # ...
        defuse_secret: "YOUR_GENERATED_KEY"

**3.** Add the following code to the application configuration in the ``app/config/config.yml``.

.. code-block:: yaml

    # app/config/config.yml

    payum:
        dynamic_gateways:
            encryption:
                defuse_secret_key: "%defuse_secret%"

**4.** Existing gateway configs will be automatically encrypted when updated. New gateway configs will be encrypted by default.
