---
layout:
  title:
    visible: true
  description:
    visible: false
  tableOfContents:
    visible: true
  outline:
    visible: true
  pagination:
    visible: true
---

# Installing Plugins

Sylius is highly flexible and can easily be customized to fit your business needs. However, you don’t always have to build custom solutions from scratch! Sylius supports the creation of **plugins**, which are the best way to extend its functionality and share your custom features with the community.

You can take advantage of plugins developed by the **Sylius Core Team** or the **Sylius Community**. While the official Sylius website lists approved plugins, many more are available within the wider Sylius ecosystem.

***

#### Example: Installing SyliusCmsPlugin

To showcase how easy and powerful Sylius plugins can be, let’s install the popular **SyliusCmsPlugin** developed by BitBag. This plugin adds CMS features to your Sylius store.

**Installation Steps**

The installation process follows the typical steps used for most Sylius plugins:

1.  **Install the Plugin Using Composer**

    Run the following command to add the plugin to your project:

    ```bash
    composer require bitbag/cms-plugin
    ```
2.  **Configure the Plugin**

    After installation, you'll need to configure the plugin. This usually involves importing the routing for the plugin in your `config/routes.yaml` file.
3.  **Update the Database**

    Run database migrations to apply any necessary changes:

    ```bash
    php bin/console doctrine:migrations:migrate
    ```
4.  **Additional Steps**

    Some plugins may require additional steps. For example, the **SyliusCmsPlugin** requires the installation of CKEditor. Follow the plugin’s documentation for these steps.

***

#### Using the Plugin

Once installed and configured, you can immediately start using the plugin’s features in your shop:

<figure><img src="../.gitbook/assets/plugin-installed.png" alt="" width="249"><figcaption></figcaption></figure>

***

#### Why Use Plugins?

Plugins are one of the fastest and easiest ways to customize your Sylius store. Before creating a custom solution, always check the existing plugins available in the Sylius ecosystem—you might find that the functionality you need has already been developed!

By using plugins, you avoid reinventing the wheel and speed up the development process, allowing you to focus on other critical aspects of your shop.

***

#### Learn more

* [Plugins Development Guide](https://old-docs.sylius.com/en/1.13/book/plugins/guide/index.html)
* :sparkles: [Sylius Store: The Official Plugins List](https://store.sylius.com/)
