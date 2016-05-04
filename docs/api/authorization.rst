.. index::
   single: Authorization

Authorization
=============

This part of documentation is about authorization to Sylius platform through API. In order to check this configuration, please set up your local copy of Sylius platform and change *sylius.dev*  to your address.


OAuth2
------
Sylius has configured OAuth2 authorization. The authorization process is standard procedure. Authorize as admin and enjoy the API!

.. note::

    User has to have ROLE_API role in order to access /api resources

Create OAuth client
~~~~~~~~~~~~~~~~~~~

Use sylius command:

.. code-block:: bash

    php app/console sylius:oauth-server:create-client
        --grant-type="password"
        --grant-type="refresh_token"
        --grant-type="token"

You will receive client public id and client secret

Example Result
..............

.. code-block:: bash

    A new client with public id 3e2iqilq2ygwk0ccgogkcwco8oosckkkk4gkoc0k4s8s044wss, secret 44ectenmudus8g88w4wkws84044ckw0k4w4kg0sokoss84oko8 has been added

.. tip::

    If you use Guzzle check out `OAuth2 plugin`__ and use Password Credentials.

__ https://github.com/commerceguys/guzzle-oauth2-plugin

Obtain access token
~~~~~~~~~~~~~~~~~~~

Send the request with the following parameters:

Definition
..........

.. code-block:: text

    GET /oauth/v2/token

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
.......

.. code-block:: bash

    curl http://sylius.dev/oauth/v2/token
        -d "client_id"=demo_client
        -d "client_secret"=secret_demo_client
        -d "grant_type"=password
        -d "username"=api@example.com
        -d "password"=api

.. tip::

    In a developer environment there is a default API user and client data. To use this credentials you have to load `API`__ data fixtures.
    Otherwise you have to use your user data and replace client id and client secret with data generated in a previous step.

__ https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/FixturesBundle/DataFixtures/ORM/LoadApiData.php

Example Response
................

.. code-block:: json

    {
        "access_token": "NzFiYTM4ZTEwMjcwZTcyZWIzZTA0NmY3NjE3MTIyMjM1Y2NlMmNlNWEyMTAzY2UzYmY0YWIxYmUzNTkyMDcyNQ",
        "expires_in": 3600,
        "token_type": "bearer",
        "scope": null,
        "refresh_token": "MDk2ZmIwODBkYmE3YjNjZWQ4ZTk2NTk2N2JmNjkyZDQ4NzA3YzhiZDQzMjJjODI5MmQ4ZmYxZjlkZmU1ZDNkMQ"
    }

Request for resource
~~~~~~~~~~~~~~~~~~~~

Put access token in the request header:

.. code-block:: text

    Authorization: Bearer NzFiYTM4ZTEwMjcwZTcyZWIzZTA0NmY3NjE3MTIyMjM1Y2NlMmNlNWEyMTAzY2UzYmY0YWIxYmUzNTkyMDcyNQ

You can now access any resource you want under /api prefix.

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/users/
        -H "Authorization: Bearer NzFiYTM4ZTEwMjcwZTcyZWIzZTA0NmY3NjE3MTIyMjM1Y2NlMmNlNWEyMTAzY2UzYmY0YWIxYmUzNTkyMDcyNQ"

.. note::

    You have to refresh your token after it expires.

Refresh Token
~~~~~~~~~~~~~~~~~~~

Send request with the following parameters

Definition
..........

.. code-block:: text

    GET /oauth/v2/token

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
.......

.. code-block:: bash

    curl http://sylius.dev/oauth/v2/token
        -d "client_id"=demo_client
        -d "client_secret"=secret_demo_client
        -d "grant_type"=refresh_token
        -d "refresh_token"=MDk2ZmIwODBkYmE3YjNjZWQ4ZTk2NTk2N2JmNjkyZDQ4NzA3YzhiZDQzMjJjODI5MmQ4ZmYxZjlkZmU1ZDNkMQ

Example Response
................

You can now use new token to send requests

.. code-block:: json

    {
        "access_token": "MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng",
        "expires_in": 3600,
        "token_type": "bearer",
        "scope": null,
        "refresh_token": "MWI4NzVkNThjZDc2Y2M1N2JiNzBmOTQ0MDFmY2U0YzVjYzllMDE1OTU5OWFiMzJiZTY5NGU4NzYyODU1N2ZjYQ"
    }
