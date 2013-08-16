SyliusBloggerBundle
===================

Highly flexible blogging system for Symfony2 applications.

It does not matter if you want to setup your personal log or just add blog/news feature to existing Symfony2 application.

Installation
------------

Downloading the bundle
~~~~~~~~~~~~~~~~~~~~~~

Recommended way to install **Sylius** bundles is using `Composer <http://getcomposer.org>`_.

Add the following lines in your `composer.json` file. ::

    "require": {
        "sylius/blogger-bundle": "dev-master"
    }

Run the update to download the package and update autoloader.

.. code-block:: bash

    $ php composer.phar update

Autoloader configuration
~~~~~~~~~~~~~~~~~~~~~~~~

If you're not using the autoloader provided by **Composer**, you need to register proper path and namespace inside your autoload.

Installing dependencies
~~~~~~~~~~~~~~~~~~~~~~~

This bundle uses **Pagerfanta library** and **PagerfantaBundle**, you just need to enable them in kernel.

The installation guide can be found `here <https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle>`_.

Adding bundle to kernel
~~~~~~~~~~~~~~~~~~~~~~~

Finally, enable the blogger bundle in the kernel.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            new Sylius\Bundle\BloggerBundle\SyliusBloggerBundle(),
        );
    }

Importing routing configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Add this in your routing configuration, `config.yml` by default.
You can customize the prefixes to match your needs.

.. code-block:: yaml

    sylius_blogger_post:
        resource: @SyliusBloggerBundle/Resources/config/routing/frontend/post.yml
        prefix: /blog

    sylius_blogger_backend_post:
        resource: @SyliusBloggerBundle/Resources/config/routing/backend/post.yml
        prefix: /administration/blog/posts

Or in XML format.

.. code-block:: xml

    <import resource="@SyliusBloggerBundle/Resources/config/routing/frontend/post.yml" prefix="/blog" />
    <import resource="@SyliusBloggerBundle/Resources/config/routing/backend/post.yml" prefix="/administration/blog/posts" />

Creating your post class
~~~~~~~~~~~~~~~~~~~~~~~~

Next step requires creating your own **Post** class.
If you're using the parent bundle model, you should obviously put in under this bundle namespace, but the place does not really matter for **SyliusBloggerBundle**.

.. code-block:: php

    <?php

    namespace Acme\Bundle\BloggerBundle\Entity\Post;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Bundle\BloggerBundle\Entity\Post as BasePost;

    class Post extends BasePost
    {
    }

Now you have to map this class in doctrine, but only required mapping is for **ID**, everything else is inherited from base class.

Container configuration
-----------------------

We need to let know Symfony2 and **SyliusBloggerBundle** about all this classes and configure the only supported driver at the moment.
Inside your container configuration, `config.yml` by default, add this.

.. code-block:: yaml

    sylius_blogger:
        driver: doctrine/orm
        engine: twig
        classes:
            model:
                post: Acme\Bundle\BloggerBundle\Entity\Post

Updating database schema
~~~~~~~~~~~~~~~~~~~~~~~~

The last thing you need to do is updating the database schema.

For "**doctrine/orm**" driver run the following command.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

Templates
~~~~~~~~~

.. note::

    Currently bundle does not include default templates.

Template names match the controller and action names, so template for creating post will be... ::

    SyliusBloggerBundle:Backend/Post:create.html.twig

You can override it like any other template inside Symfony2.

Usage guide
-----------

This part covers some common usecases and customizations.

Clean integration
~~~~~~~~~~~~~~~~~

There are several ways to integrate this bundle with your project. You can create your bundle inside application that will extend **SyliusBloggerBundle**.
This approach has some benefits but not everybody prefer creating many bundles.

If you would like to add parent bundle to organize the code better, or this is "your way" of doing things, remember to set the proper parent in it.

.. code-block:: php

    public function getParent()
    {
        return 'SyliusBloggerBundle';
    }

This will allow you easily override the templates and organize things.

Customizing the form
~~~~~~~~~~~~~~~~~~~~

Perhaps you want to modify or extend the post form? This is really easy and configurable.
Overriding the form can be achieved in two ways, if you only need to add or remove some fields, without adding any dependencies to form type, simply create your own `PostType` class that will extend the **Sylius** one.

.. code-block:: php

    <?php

    namespace Acme\Bundle\BloggerBundle\Form\Type;

    use Sylius\Bundle\BloggerBundle\Form\Type\PostType as BasePostType;
    use Symfony\Component\Form\FormBuilder;

    class PostType extends BasePostType
    {
        public function buildForm(FormBuilder $builder, array $options) 
        {
            parent::buildForm($builder, $options);

            $builder
                ->remove('author')
                ->add('enableRating', 'checkbox', array(
                    'required' => false
                ))
            ;
        }
    }

Then you can set this class to be used when creating and updating the post.

.. code-block:: yaml

    sylius_blogger:
        classes:
            form:
                type:
                    post: Acme\Bundle\BloggerBundle\Form\Type\PostType

Now your custom form will be used.

If you need to include some custom services in the form type, you should create your service and tag it as form type using this name... ::

    sylius_blogger_post

This will put your form type as a service. If you're using this method, please remember to pass the FQCN of your post model to the parent constructor or `data_class` form option.

Configuration reference
-----------------------

Full configuration reference with default values.

.. code-block:: yaml

    sylius_blogger:
        driver: doctrine/orm
        engine: twig
        classes:
            model:
                post: ~
            controller:
                frontend:
                    post: Sylius\Bundle\BloggerBundle\Controller\Frontend\PostController
                backend:
                    post: Sylius\Bundle\BloggerBundle\Controller\Backend\PostController
            form:
                type:
                    post: Application\Sylius\BloggerBundle\Form\Type\PostType
            manipulator:
                post: Sylius\Bundle\BloggerBundle\Manipulator\PostManipulator

Testing and continuous integration
----------------------------------

.. image:: https://api.travis-ci.org/Sylius/SyliusBloggerBundle.png

This bundle uses `travis-ci.org <http://travis-ci.org/Sylius/SyliusBloggerBundle>`_ for CI.

Before running tests, load the dependencies using `Composer <http://getcomposer.org>`_.

    .. code-block:: bash

        $ wget http://getcomposer.org/composer.phar
        $ php composer.phar install

Now you can test by simply using this command.

    .. code-block:: bash

        $ phpunit

Working examples
----------------

If you want to see this and other bundles in action, try out the `Sylius sandbox application <http://github.com/Sylius/Sylius-Sandbox>`_.

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/SyliusBloggerBundle/issues>`_.
If you have found bug, please create an issue.
