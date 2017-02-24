.. index::
   single: Authorization

Authorization
=============

This part of documentation is about authorization to Sylius platform through API. In order to check this configuration,
please set up your local copy of Sylius platform and change *sylius.dev*  to your address.

OAuth2
------

Sylius has the OAuth2 authorization configured. The authorization process is a standard procedure. Authorize as admin and enjoy the API!

.. note::

    User has to have the ROLE_API_ACCESS role in order to access ``/api`` resources

Create OAuth client
^^^^^^^^^^^^^^^^^^^

Use Sylius command:

.. code-block:: bash

    php bin/console sylius:oauth-server:create-client \
        --grant-type="password" \
        --grant-type="refresh_token" \
        --grant-type="token"

You will receive client public id and client secret

Exemplary Result
''''''''''''''''

.. code-block:: bash

    A new client with public id 3e2iqilq2ygwk0ccgogkcwco8oosckkkk4gkoc0k4s8s044wss, secret 44ectenmudus8g88w4wkws84044ckw0k4w4kg0sokoss84oko8 has been added

.. tip::

    If you use Guzzle check out `OAuth2 plugin <https://github.com/Sainsburys/guzzle-oauth2-plugin>`_ and use Password Credentials.

Obtain access token
^^^^^^^^^^^^^^^^^^^

Send the request with the following parameters:

Definition
''''''''''

.. code-block:: text

    GET /api/oauth/v2/token

+---------------+----------------+--------------------------------------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                                                      |
+===============+================+==================================================================================================+
| client_id     | query          | Client public id generated in the previous step                                                  |
+---------------+----------------+--------------------------------------------------------------------------------------------------+
| client_secret | query          | Client secret generated in the previous step                                                     |
+---------------+----------------+--------------------------------------------------------------------------------------------------+
| grant_type    | query          | We will use 'password' to authorize as user. Other available options are token and refresh-token |
+---------------+----------------+--------------------------------------------------------------------------------------------------+
| username      | query          | User name                                                                                        |
+---------------+----------------+--------------------------------------------------------------------------------------------------+
| password      | query          | User password                                                                                    |
+---------------+----------------+--------------------------------------------------------------------------------------------------+

.. note::

    This action can be done by POST method as well.

Example
'''''''

.. code-block:: bash

    curl http://sylius.dev/api/oauth/v2/token \
        -d "client_id"=demo_client \
        -d "client_secret"=secret_demo_client \
        -d "grant_type"=password \
        -d "username"=api@example.com \
        -d "password"=sylius-api

.. tip::

    In a developer environment there is a default API user and client data. To use this credentials you have to load data fixtures.
    Otherwise you have to use your user data and replace client id and client secret with data generated in a previous step.

Exemplary Response
''''''''''''''''''

.. code-block:: json

    {
        "access_token": "NzFiYTM4ZTEwMjcwZTcyZWIzZTA0NmY3NjE3MTIyMjM1Y2NlMmNlNWEyMTAzY2UzYmY0YWIxYmUzNTkyMDcyNQ",
        "expires_in": 3600,
        "token_type": "bearer",
        "scope": null,
        "refresh_token": "MDk2ZmIwODBkYmE3YjNjZWQ4ZTk2NTk2N2JmNjkyZDQ4NzA3YzhiZDQzMjJjODI5MmQ4ZmYxZjlkZmU1ZDNkMQ"
    }

Request for a resource
^^^^^^^^^^^^^^^^^^^^^^

Put access token in the request header:

.. code-block:: text

    Authorization: Bearer NzFiYTM4ZTEwMjcwZTcyZWIzZTA0NmY3NjE3MTIyMjM1Y2NlMmNlNWEyMTAzY2UzYmY0YWIxYmUzNTkyMDcyNQ

You can now access any resource you want under /api prefix.

Example
'''''''

.. code-block:: bash

    curl http://sylius.dev/api/v1/users/
        -H "Authorization: Bearer NzFiYTM4ZTEwMjcwZTcyZWIzZTA0NmY3NjE3MTIyMjM1Y2NlMmNlNWEyMTAzY2UzYmY0YWIxYmUzNTkyMDcyNQ"

.. note::

    You have to refresh your token after it expires.

Refresh Token
^^^^^^^^^^^^^

Send request with the following parameters

Definition
''''''''''

.. code-block:: text

    GET /api/oauth/v2/token

+---------------+----------------+---------------------------------------------------+
| Parameter     | Parameter type |  Description                                      |
+===============+================+===================================================+
| client_id     | query          |  Public client id                                 |
+---------------+----------------+---------------------------------------------------+
| client_secret | query          |  Client secret                                    |
+---------------+----------------+---------------------------------------------------+
| grant_type    | query          |  We will use 'refresh_token' to authorize as user |
+---------------+----------------+---------------------------------------------------+
| refresh_token | query          |  Refresh token generated during authorization     |
+---------------+----------------+---------------------------------------------------+

Example
'''''''

.. code-block:: bash

    curl http://sylius.dev/api/oauth/v2/token \
        -d "client_id"=demo_client \
        -d "client_secret"=secret_demo_client \
        -d "grant_type"=refresh_token \
        -d "refresh_token"=MDk2ZmIwODBkYmE3YjNjZWQ4ZTk2NTk2N2JmNjkyZDQ4NzA3YzhiZDQzMjJjODI5MmQ4ZmYxZjlkZmU1ZDNkMQ

Exemplary Response
''''''''''''''''''

You can now use new token to send requests

.. code-block:: json

    {
        "access_token": "MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng",
        "expires_in": 3600,
        "token_type": "bearer",
        "scope": null,
        "refresh_token": "MWI4NzVkNThjZDc2Y2M1N2JiNzBmOTQ0MDFmY2U0YzVjYzllMDE1OTU5OWFiMzJiZTY5NGU4NzYyODU1N2ZjYQ"
    }

Default values in dev environment
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In a developer environment there are default client id, client secret and default access token provided to allow you to test our API just out-of-the-box.
In order to access them, please use the following values:

+---------------+--------------------+
| Parameter     | Value              |
+===============+====================+
| client_id     | demo_client        |
+---------------+--------------------+
| client_secret | secret_demo_client |
+---------------+--------------------+
| grant_type    | password           |
+---------------+--------------------+
| access_token  | SampleToken        |
+---------------+--------------------+

These values will be used later on to make it easier for you to check, how our API works.
