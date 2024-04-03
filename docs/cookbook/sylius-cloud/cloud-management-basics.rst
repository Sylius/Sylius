Cloud management basics
=======================

Once your environments are up, you may need some tools to manage them. Sylius Cloud offers a lot of environment commands which may help you in your application maintenance.
Please meet the most commonly used commands of the CLI.

SSH access
----------

On Sylius Cloud, connecting to an SSH session allows you to access your application's environment for administrative tasks, debugging, and troubleshooting.
The recommended method for connecting to SSH on Sylius Cloud is through the CLI, as on the example below:

.. code-block:: bash

    platform ssh -e <ENVIRONMENT_ID>

Alternatively, you can access the SSH URL provided in the Sylius Cloud dashboard or environment information.
This URL typically follows the format ssh.<ENVIRONMENT_ID>.<PROJECT_ID>@ssh.<region>.platform.sh.

To authenticate with SSH on Sylius Cloud, you'll need to use SSH keys.
Ensure that you have SSH keys configured on your local machine and that your public SSH key is added to your Sylius Cloud account.
You can manage SSH keys through the Sylius Cloud Console or CLI.

Database access
---------------

Sylius Cloud CLI provides a command for interacting with the environment database directly from the command line.
You can use commands like platform db:sql to perform database operations on interactive database shell.

If you need to run already defined SQL query on the database, you can run the CLI command as follows:

.. code-block:: bash

    platform db:sql "show tables"

If you with to import an SQL file into the database, you can run the command below:

.. code-block:: bash

    platform sql < my_database_queries_file.sql

Dumping database
----------------
Sylius Cloud CLI provides a command for performing database dumping directly from the command line.
You can use it to create a dump of the specified environment database.

.. code-block:: bash

    platform db:dump

If you need to specify the output filename and/or target directory, you can use the `--file` parameter:

.. code-block:: bash

    platform db:dump --file=MyFileName.sql --directory=/home/MyUserName

You can also specify the tables you want to include or exclude from the export file:

.. code-block:: bash

    platform db:dump --table=table1,table2,table3

The command above will create a database dump containing only the specified tables.

To exclude the tables from the dump file, you can use the `--exclude-table` option:

.. code-block:: bash

    platform db:dump --exclude-table=table1,table2,table3

You also can dump only the schema of your database:

.. code-block:: bash

    platform db:dump --table=table1,table2,table3 --schema-only

Backups
-------

Sylius Cloud provides commands in the CLI for preparing and restoring backups of your environment's database.
To prepare the backup you can use the command:

.. code-block:: bash

    platform backup:create <ENVIRONMENT_ID>

This command creates a backup of the environment's database and stores it securely in Sylius Cloud backup system.
You can optionally specify additional options, such as `--no-wait`, to perform the backup asynchronously without waiting for it to complete.

If you wish to create backup without any downtime, you can use the `--live` command.

.. note::

    Please keep in mind that running live backup may effect risky data inconsistency.

To restore a backup of your environment's database, use the command below:

.. code-block:: bash

    platform backup:restore <ENVIRONMENT_ID> <BACKUP_ID>

This command restores the specified backup of the environment's database to its previous state.
You can obtain the backup ID from the Sylius Cloud dashboard or by listing available backups using the `platform backup:list` command.

Synchronizing environments
--------------------------

Sylius Cloud offers the environment synchronization command. It synchronizes the following components between the source and target environments:

- \- **Code**: Copies the codebase (Git repository) from the source environment to the target environment.
- \- **Configuration**: Applies the configuration settings (defined in the `.platform.app.yaml` file) from the source environment to the target environment.
- \- **Data**: Optionally synchronizes the database and files (if enabled) between the source and target environments.

To synchronize environments please use the command below:

.. code-block:: bash

    platform environment:synchronize <SOURCE_ENVIRONMENT> <TARGET_ENVIRONMENT>

The synchronization command supports several options to customize the synchronization process, including:

 - **\-\-code**: Synchronizes only the codebase between environments.
 - **\-\-config**: Synchronizes only the configuration settings between environments.
 - **\-\-data**: Synchronizes the database and files between environments (if applicable).
 - **\-\-no-wait**: Performs the synchronization asynchronously without waiting for it to complete.

When you run the command without any options, the CLI will ask you whether you want to synchronize code, configuration or data between environments.
