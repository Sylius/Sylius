How to store images on AWS-S3 automatically?
============================================


Instructions:
-------------

First you need to ensure that the official ``AWS-S3 SDK`` for PHP is installed:

.. code-block:: bash

    composer require aws/aws-sdk-php league/flysystem-aws-s3-v3


1. Configure Flysystem AWS S3 Adapter
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Place this file under ``config/packages/flysystem.yaml``:

.. code-block:: yaml

    # config/packages/flysystem.yaml
    flysystem:
        storages:
            sylius.storage:
                adapter: 'aws'
                options:
                    client: Aws\S3\S3Client
                    bucket: "%aws.s3.bucket%"
                    prefix: "media/image"
                    streamReads: true

For more details see:
 * https://flysystem.thephpleague.com/docs/adapter/aws-s3-v3/
 * https://github.com/thephpleague/flysystem-bundle/blob/3.x/docs/2-cloud-storage-providers.md#aws-sdk-s3

2. Configure Liip-Imagine:
^^^^^^^^^^^^^^^^^^^^^^^^^^

Add this file under ``config/packages/liip_imagine.yaml`` in order to make Liip-Imagine aware of AWS S3 storage:

.. code-block:: yaml

    # config/packages/liip_imagine.yaml
    liip_imagine:
        loaders:
            aws_s3:
                flysystem:
                    filesystem_service: sylius.storage
        resolvers:
            aws_s3:
                aws_s3:
                    client_config:
                        credentials:
                            key:    "%aws.s3.key%"
                            secret: "%aws.s3.secret%"
                        region: "%aws.s3.region%"
                        version: "%aws.s3.version%"
                    bucket: "%aws.s3.bucket%"
                    get_options:
                        Scheme: https
                    put_options:
                        CacheControl: "max-age=86400"
                    cache_prefix: media/cache
        data_loader: aws_s3
        cache: aws_s3

3. Define S3-related parameters and configure final services:
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In ``config/services.yaml``:

.. code-block:: yaml

    # config/services.yaml
    parameters:
        aws.s3.key: "%env(AWS_S3_KEY)%"
        aws.s3.secret: "%env(AWS_S3_SECRET)%"
        aws.s3.bucket: "%env(AWS_S3_BUCKET)%"
        aws.s3.region: "%env(AWS_S3_REGION)%"
        aws.s3.version: "%env(AWS_S3_VERSION)%"

    services:
        # composer require aws/aws-sdk-php
        Aws\S3\S3Client:
            factory: [Aws\S3\S3Client, 'factory']
            arguments:
                -
                    version: "%aws.s3.version%"
                    region: "%aws.s3.region%"
                    credentials:
                        key: "%aws.s3.key%"
                        secret: "%aws.s3.secret%"

