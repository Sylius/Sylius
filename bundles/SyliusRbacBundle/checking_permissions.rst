Checking Permissions
====================

Service ``sylius.authorization_checker`` is responsible for granting access.

In Controller
-------------

.. code-block:: php

    namespace App\Bundle\AppBundle\Controller;

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

    class YourController extends Controller
    {
        public function securedAction(Request $request)
        {
            if (!$this->get('sylius.authorization_checker')->isGranted('your.permission.code')) {
               throw new AccessDeniedHttpException();
            }
        }
    }

In Templates
------------

.. code-block:: twig

    {% if sylius_is_granted('your.permission.code') %}
        {{ product.price }}
    {% endif %}

