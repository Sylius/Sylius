# Setting up the Sylius Test Application

Once you’ve configured the **Test Application** for your Sylius plugin, you’ll need to prepare the environment to ensure everything works as expected. This includes setting up the database, loading initial data, installing frontend dependencies, and preparing the app to run locally.

Here’s a breakdown of each step and the corresponding commands:

#### 1. **Create the Test Database**

```bash
vendor/bin/console doctrine:database:create
```

This command creates the database for the Test Application using Doctrine. It reads the `DATABASE_URL` from the `.env` file in `tests/TestApplication/`.\
If the database already exists, this command won’t do anything.

#### 2. **Run Migrations to Build the Database Schema**

```bash
vendor/bin/console doctrine:migrations:migrate -n
```

This applies all available Doctrine migrations, including those from Sylius core and your plugin (if any).\
The `-n` flag skips confirmation prompts, which is especially useful in CI environments.

#### 3. **Load Sylius Fixtures**

```bash
vendor/bin/console sylius:fixtures:load -n
```

Fixtures are predefined data (like products, channels, and users) that populate your test database.\
This command loads these sample records, so your test application is ready to use.\
You can add custom fixtures if your plugin needs specific data (such as custom entities or product types).

#### 4. **Install Frontend Dependencies (JavaScript & CSS)**

```bash
(cd vendor/sylius/test-application && yarn install)
```

This installs JavaScript dependencies defined in `package.json` inside the Test Application.\
The command changes into the `vendor/sylius/test-application` directory and runs `yarn install`, which installs the necessary packages into `node_modules`.

If your plugin adds additional JS dependencies, they will be merged automatically.

#### 5. **Build Frontend Assets (Admin & Shop)**

```bash
(cd vendor/sylius/test-application && yarn build)
```

This command builds the frontend assets, including JavaScript and CSS.\
Once the build process completes, the assets will be ready to be installed into the `public/` directory.

#### 6. **Install Assets into the Public Directory**

```bash
vendor/bin/console assets:install
```

This command copies the built frontend assets (JS, CSS) into the `public/` directory of your Test Application.\
Without this step, your app may load without styles or JavaScript features on both the admin and shop sides.

#### 7. **Start the Local Web Server**

```bash
symfony server:start -d
```

This command starts a local development server using the Symfony CLI.\
Your Test Application will be accessible at `http://127.0.0.1:8000` by default.

***

#### What happens after these steps?

Once all of these steps are completed, your plugin will be fully integrated with the Sylius Test Application. At this point, you can:

* Run your automated tests in a clean, stable Sylius environment.
* Open the local app in your browser to test your plugin manually.
* Develop and extend your plugin in a real Sylius setup without needing to maintain a full Sylius app.

This setup ensures that you can develop, test, and confidently integrate your plugin while maintaining compatibility with future Sylius upgrades.

***
