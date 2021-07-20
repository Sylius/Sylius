Authorization
=============

In the new API all admin routes are protected by JWT authentication. If you would like to test these endpoints
`in our Swagger UI docs <http://master.demo.sylius.com/api/v2/docs>`_, you need to retrieve a JWT token first.
You could do that by using an endpoint with default credentials for API administrators:

.. image:: ../../_images/sylius_api/api_platform_authentication_endpoint.png
    :align: center
    :scale: 50%

|

In the response, you will get a token that has to be passed in each request header. In the Swagger UI, you can set
the authentication token for each request.

.. image:: ../../_images/sylius_api/api_platform_authentication_response.png
    :align: center
    :scale: 50%

|

Notice the **Authorize** button and unlocked padlock near the available URLs:

.. image:: ../../_images/sylius_api/api_platform_not_authorized.png
    :align: center
    :scale: 50%

|

Click the **Authorize** button and put the authentication token (remember about the ``Bearer`` prefix):

.. image:: ../../_images/sylius_api/api_platform_authorization.png
    :align: center
    :scale: 50%

|

After clicking **Authorize**, you should see locked padlock near URLs and the proper header should be added to each API call:

.. image:: ../../_images/sylius_api/api_platform_authorized.png
    :align: center
    :scale: 50%
