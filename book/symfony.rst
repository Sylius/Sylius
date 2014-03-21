.. index::
   single: Symfony

Symfony
=======

Sylius is built on top of `Symfony2 <http://symfony.com>`_, a modern framework
for PHP. If you are familiar with this tool, you can safely skip this quick
introduction. You should feel like home when working with Sylius.

HTTP Fundamentals
-----------------

HTTP (Hypertext Transfer Protocol) is a text language that allows
two machines to communicate with each other.

Symfony2 is built from the ground-up around this protocol.

Each time any client wants to access Sylius store page, it all starts with a *request*.
This text message, created by the browser, is sent to a server, which should 
return a proper response.

In HTTP, message can look something like this:

.. code-block:: text

    GET /products HTTP/1.1
    Host: sylius.org
    Accept: text/html
    User-Agent: Mozilla/5.0 (Macintosh)

The URI (e.g. ``/``, ``/products/nike-t-shirt``) is the unique location
that identifies the resource the client wants. The HTTP tells the app
what client wants to *do* with the resource. 

The HTTP methods are the *verbs* of the request and define the few common 
ways that you can act upon the resource:

+----------+---------------------------------------+
| *GET*    | Retrieve the resource from the server |
+----------+---------------------------------------+
| *POST*   | Create a resource on the server       |
+----------+---------------------------------------+
| *PUT*    | Update the resource on the server     |
+----------+---------------------------------------+
| *DELETE* | Delete the resource from the server   |
+----------+---------------------------------------+

An example HTTP request to delete a specific product might look like:

.. code-block:: text

    DELETE /products/15 HTTP/1.1

Based on the request, the server will return a response.

.. code-block:: text

    HTTP/1.1 200 OK
    Date: Sat, 03 Apr 2013 22:25:05 GMT
    Server: lighttpd/1.4.19
    Content-Type: text/html

    <html>
        <!-- Interesting content! -->
    </html>

Controllers
-----------

A controller is a PHP function you create that takes information from the
HTTP request, then constructs and returns an HTTP response. 
The response could be an HTML page representing a product, an XML document with all 
informations about particular order, a serialized JSON product representation, an image, a redirect, a 404 error...

The following controller would render a page that simply prints ``Hello world!``::

    use Symfony\Component\HttpFoundation\Response;

    public function helloAction()
    {
        return new Response('Hello world!');
    }

Every page you see in Sylius store is a result of controller action.
When you view a product page, list all products or display login form, it's always a result
of this simple schema, where *controller creates a Response based on current Request*.

Routing
-------

.. note::

    To be written.

Twig - Templating engine
------------------------

.. note::

    To be written.

Final Thoughts
--------------

Symfony2 is very flexible web application framework. To work with Sylius you need to know 
only the fundamentals, but we encourage you to learn more about this awesome tool. 
It will give you better understanding of Sylius internals, and allow you to 
create really advanced e-commerce projects on top of that knowledge.

Learn more from the Symfony documentation
----------------------------

* `Symfony2 and HTTP Fundamentals <http://symfony.com/doc/current/book/http_fundamentals.html>`_
* `Controller <http://symfony.com/doc/current/book/controller.html>`_
* `Routing <http://symfony.com/doc/current/book/routing.html>`_
* `Templating and Twig <http://symfony.com/doc/current/book/templating.html>`_
* `Databases and Doctrine <http://symfony.com/doc/current/book/doctrine.html>`_
