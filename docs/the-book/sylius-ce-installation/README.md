# Sylius CE Installation

{% stepper %}
{% step %}
### Setting up a fresh Sylius project

The recommended way of creating a new Sylius-based project is to use our [Sylius Standard](https://github.com/Sylius/Sylius-Standard) project template.

```bash
composer create-project sylius/sylius-standard AcmeStore
```

This command will create a `AcmeStore` directory with the downloaded latest version of Sylius Standard, and install its dependencies. Before we go further, we have to enter the `AcmeStore` directory.

```bash
cd AcmeStore
```

{% hint style="info" %}
By default, Sylius comes with the latest LTS version of Symfony. If you want to change it, you can do it with the following commands executed from the project's root directory:

{% code lineNumbers="true" %}
```bash
composer config extra.symfony.require "<version>" # e.g. "6.3.*"
composer update
```
{% endcode %}
{% endhint %}
{% endstep %}

{% step %}
### Building front-end

Before we perform an initial installation of Sylius, we need to have our front-end assets built. People often use _Yarn_ over _npm_ due to features like workspaces, offline caching, and faster installation times, which can be particularly useful in large-scale or complex projects. However, npm has improved significantly over time and can also be a great choice for managing your front-end packages.

{% tabs %}
{% tab title="npm" %}
<pre class="language-bash" data-line-numbers><code class="lang-bash"><strong>npm install
</strong>npm run build
</code></pre>
{% endtab %}

{% tab title="yarn" %}
{% code lineNumbers="true" %}
```bash
yarn install
yarn build
```
{% endcode %}
{% endtab %}
{% endtabs %}
{% endstep %}

{% step %}
### Installing Sylius

To make installing Sylius easier, we provide an interactive process for it. But before we start the installer, we need to define the database's credentials. For the local development, the best choice is to use local `.env` files.

To define your database connection string, create the `.env.local` file with the following content:

{% tabs %}
{% tab title="MySQL" %}
{% code title=".env.local" overflow="wrap" %}
```sh
DATABASE_URL=mysql://<username>:<password>@<host>/<your_database_name>_%kernel.environment%?serverVersion=<your_db_version>&charset=utf8
```
{% endcode %}
{% endtab %}

{% tab title="MariaDB" %}
{% code title=".env.local" overflow="wrap" %}
```sh
DATABASE_URL=mysql://<username>:<password>@<host>/<your_database_name>_%kernel.environment%?serverVersion=mariadb-<your_db_version>&charset=utf8
```
{% endcode %}
{% endtab %}

{% tab title="PostgreSQL" %}
{% code title=".env.local" overflow="wrap" %}
```sh
DATABASE_URL=pgsql://<username>:<password>@<host>/<your_database_name>_%kernel.environment%?serverVersion=<your_db_version>&charset=utf8
```
{% endcode %}
{% endtab %}
{% endtabs %}

{% hint style="info" %}
Specific Sylius versions may support various Symfony versions. To make sure the correct Symfony version will be installed (Symfony 7.0, for example), use:

{% code lineNumbers="true" %}
```bash
composer config extra.symfony.require "^7.0"
composer update
```
{% endcode %}

Otherwise, you may face the problem of having Symfony components installed in the wrong version.
{% endhint %}

Once you have the database configured, we can run the interactive installer by running the following command in the project's root directory:

{% code lineNumbers="true" %}
```bash
bin/console sylius:install
```
{% endcode %}

In the first step, Sylius checks whether the system meets the requirements. The second step is dedicated to setting up the database we configured earlier. When the database schema is ready, we can choose whether we want to install the demo data. Next, we go through a basic shop configuration and generate tokens for the Sylius API. At the end, Sylius will automatically install assets from the already installed bundles and plugins.

{% hint style="warning" %}
During the `sylius:install` The command will ask you to provide important information, and its execution ensures that the default **currency** (USD) and the default **locale** (English - US) are set. They can be changed later, respectively, in the “Configuration > Channels” section of the admin and in the `config/services.yaml` file. If you want to change these before running the installation command, set the `locale` and `sylius_installer_currency` parameters in the `config/services.yaml` file. From now on, all the prices will be stored in the database in USD as integers, and all the products will have to be added with a base American English name translation.
{% endhint %}
{% endstep %}

{% step %}
### Configuring Mailer

In order to send emails, you need to configure Mailer Service. Basically, there are multiple ways to do it:

* We are recommending using [Symfony Mailer](https://symfony.com/doc/current/mailer.html), where, out of the box, you can deliver emails by configuring the `MAILER_DSN` variable in your .env file.
* In Symfony Mailer, use the [3rd-party Transports](https://symfony.com/doc/current/mailer.html#using-a-3rd-party-transport)
* (deprecated) Use SwiftMailer with this short configuration:
* **Create an account on a mailing service.**
* **In your** `.env` **file modify/add the** `MAILER_URL` **variable.**

```bash
MAILER_URL=gmail://username:password@local
```

{% hint style="info" %}
Email delivery is disabled for _test_, _dev,_ and _staging_ environments by default. The _prod_ environment has delivery turned on.

You can learn more about configuring mailer service in [How to configure mailer?](../../emails/how-to-configure-mailer.md)
{% endhint %}
{% endstep %}

{% step %}
### Accessing the Shop

We strongly recommend using the Symfony Local Web Server by running the `symfony server:start` command and then accessing `https://127.0.0.1:8000` in your web browser to see the shop.

{% hint style="info" %}
Get to know more about using Symfony Local Web Server in[ the Symfony server documentation](https://symfony.com/doc/current/setup/symfony_server.html). If you are using a built-in server, check [here](https://symfony.com/doc/current/cookbook/web_server/built_in.html).
{% endhint %}

You can log in to the administrator panel located at `/admin` with the credentials you have provided during the installation process.
{% endstep %}

{% step %}
### How to start developing? - Project Structure

After you have successfully gone through the installation process of **Sylius-Standard,** you are probably going to start developing within the framework of Sylius.

In the root directory of your project, you will find these important subdirectories:

* `config/` - Here you will be adding the yaml configuration files, including routing, security, state machine configurations, etc.
* `var/log/` - These are the logs of your application
* `var/cache/` - This is the cache of your project
* `src/` - This is where you will be adding all your custom logic in the `App`
* `public/` - There, you will be placing the assets of your project

{% hint style="info" %}
As it was mentioned before, we are basing on Symfony, that is why we’ve adopted its approach to architecture. Read more [in the Symfony documentation](https://symfony.com/doc/current/quick_tour/the_architecture.html). Read also about the [best practices while structuring your project](https://symfony.com/doc/current/best_practices/creating-the-project.html#structuring-the-application).
{% endhint %}
{% endstep %}

{% step %}
### Running asynchronous tasks

To enable asynchronous tasks (for example, for Catalog Promotions), remember to run the messenger consumer in a separate process, use the command:

```bash
 php bin/console messenger:consume main
```

For production environments, we suggest the use of a more robust solution like Supervisor, which will ensure that the process is still running even if a failure occurs. For more information, please visit the [Symfony documentation](https://symfony.com/doc/current/messenger.html#supervisor-configuration).

You can learn more about Catalog Promotions [here](../products/catalog-promotions.md).
{% endstep %}
{% endstepper %}

## Contributing

If you would like to contribute to Sylius - please go to the [Contribution Guide](../contributing/).
