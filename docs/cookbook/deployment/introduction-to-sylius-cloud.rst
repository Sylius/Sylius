Introduction to Sylius Cloud by Platform.sh
===========================================

Sylius Cloud by Platform.sh provides an efficient and scalable solution for hosting Sylius. You can deploy and manage your application
with minimal DevOps knowledge. All you need to do is to configure your project dependencies, push the application and maintain it.

What is Sylius Cloud by Platform.sh
-----------------------------------

It is a **solution based on Platform.sh** - a modern PaaS (Platform-as-a-Service) - which gives you the ability to create
and manage any kind of environment, starting from development, through staging, pre-production, up to production environment.
It's based on its own container architecture, which gives you all that is good for performance, security, and scalability at once.

Using Platform.sh as a base of Sylius Cloud adds the possibility of hosting your Sylius platform on one of the following cloud service providers:

* Amazon Web Services (AWS)
* Google Cloud Platform (GCP)
* Microsoft Azure
* OVH

With Sylius Cloud you can choose the service provider, and server characteristics (number of CPUs, memory amount, disk size, etc.)
having clear information about costs. It's a good solution for both small, medium, and huge web stores with big traffic and a lot of orders.

Main Sylius Cloud concepts:

* Git-driven architecture - helps set up new environments by simply pushing a new git branch to the specified remote. It gives you the ability to create new environments fast.
* Infrastructure as a code - all you need to do is write your application and a few YAML files to create your environment. So simple and so powerful.
* Multi-services and multi-apps - it doesn't matter if your application is a monolith or microservice-based architecture. It doesn't matter how many different external services you need (MySQL, PostgreSQL, ElasticSearch, Redis, etc.) - it just works well in all cases.

Sylius Cloud Dictionary
-----------------------

When diving into the Sylius Cloud, it's important to get familiar with some key terms. These terms help you navigate and understand how Sylius Cloud works.

* **Sylius Cloud Console** - a web-based interface of Platform.sh that allows users to manage their projects, environments, applications, and services from a centralized dashboard. It provides a user-friendly and intuitive interface for performing various tasks related to application development, deployment, and management on the Sylius Cloud platform.
* **Sylius Cloud CLI** - the Platform.sh Command Line Interface (CLI) tool that allows users to interact with their Platform.sh-based Sylius Cloud projects, environments, applications, and services from the command line. It provides a set of commands and utilities that streamline common tasks related to application development, deployment, and management on the Sylius Cloud platform.
* **Project** - a container for all your environments, applications, and services. When you create a new project, you're essentially setting up a workspace where you can manage your development, staging, and production environments, along with the associated code repositories and services.
* **Environment** - a self-contained instance of your application that represents a specific stage in the software development lifecycle. Environments are used to separate different stages of development, testing, and production, allowing you to manage your application's codebase, configuration, and services in a controlled and isolated manner.
* **Build process** - the series of steps that are executed to prepare your application for deployment to a specific environment. These steps involve installing dependencies (Composer), warming-up application cache, building NodeJS assets, and performing any other tasks necessary to ensure that your application is ready to run in the target environment.
* **Deployment Process** - the process of replacing the existing application with the already built, updated version. The current application version is hosted until the whole deployment process exits without an error.
* **Relations** - the connections established between different services or components within your project. These connections enable communication and interaction between services, allowing them to work together seamlessly to support your application.

How to deploy Sylius to Sylius Cloud by Platform.sh?
====================================================

.. tip::

    Start with reading `Platform.sh documentation <https://docs.platform.sh/guides/symfony.html>`_.

The process of deploying projects to Sylius Cloud by Platform.sh is based on the guidelines prepared for Symfony projects in general.
In this guide you will find sufficient instructions to have your application up and running.

0. Project configuration
------------------------

In order for your project to be ready for Sylius Cloud, make sure to have it initiated using this command:

.. code-block:: bash

    composer create-project sylius/sylius-standard acme

This way you'll have all the infrastructure configuration files from `Sylius-Standard <https://github.com/Sylius/Sylius-Standard>`_.application

The files you should be interested in are:

* `.platform.app.yaml` - This file is used to define the configuration of the application itself. It specifies how the application should be built, what runtime to use, which commands to run on deployment, etc. It also includes information such as the type of application, language runtime, build and deploy hooks, and more.
* `.env.platform.dist` - The environment file automatically used by the application during the build process.
* `.platform/routes.yaml` - This file defines the routes and how traffic is routed to the application. It specifies the incoming URLs and how they should be directed to the appropriate services or applications. Routes can be configured to handle different paths, domains, or other conditions.
* `.platform/services.yaml` - This file is used to define additional services or dependencies required by the application. It allows defining various types of services such as databases, caches, search engines, etc., along with their configuration. Services defined in this file are automatically provisioned and integrated with the application environment.

1. Prepare a Platform.sh project
--------------------------------

* Create an account on `Platform.sh <https://docs.platform.sh/get-started/introduction.html#an-account>`_ and set up an organisation.

.. hint::

    **Platform.sh** offers a trial month, which you can use for testing your store deployment. If you would be asked to
    provide your credit card data nevertheless, use `this link <https://auth.api.platform.sh/register?trial_type=general>`_
    to create your new project.

    During account creation you'll be asked to provide your phone number during registration which is used for authorization only.

* Create a new project

When you're creating a new project from the Console,
to use the Sylius Cloud potential, please choose the "Create from scratch" option, as selected on the screenshot below:

.. image:: ../../_images/cookbook/sylius-cloud/create-from-scratch.png
    :align: center
    :scale: 50%

|

2. Push the application to the Sylius Cloud
-------------------------------------------

You can now simply push your project to Sylius Cloud by Platform.sh:

.. code-block:: bash

    symfony cloud:push

Having that tested let's dive into details of deployment on Sylius Cloud.
