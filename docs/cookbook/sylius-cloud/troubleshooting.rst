Troubleshooting
===============

Every tool sometimes crashes or has some common issues. In this section we'll help you in solving common problems you can meet using Sylius Cloud.

Connection timeout
------------------

We hope you won't, but sometimes using the CLI you can see the error message:

.. code-block:: text

    cURL error 28: Operation timed out after 30000 milliseconds with 0 bytes received

This message may confuse a lot of people. But in short words it means the environment you're currently on (in context of CLI), has been removed.
It might be removed by the CLI or i.e. the Console.

Best would be to run the `platform project:list` command and then switch to a different project:

.. code-block:: bash

    platform get <PROJECT_ID>

If the commands above also finish with timeout, please use Console to obtain any other project ID.
Then please locate the `.platform/local/project.yaml` file and paste the new project ID into the `id:` key.
