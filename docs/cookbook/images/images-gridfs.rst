How to store images in MongoDB GridFS?
======================================

This guide will show you how to store product images in MongoDB GridFS using the
`DoctrineMongoDBBundle <https://symfony.com/doc/current/bundles/DoctrineMongoDBBundle/index.html>`_.
We're assuming that you already enabled and configured the bundle accordingly.

Prerequisite: data structure
----------------------------

First of all a document class to store an image is required.
To make GridFS files easily reusable, we introduce a mapped superclass which will store the basic file information.

.. code-block:: php

    <?php

    namespace AppBundle\Document;

    use Doctrine\MongoDB\GridFSFile;

    abstract class File
    {
        /**
         * @var string
         */
        protected $id;

        /**
         * @var GridFSFile
         */
        protected $file;

        /**
         * @var int
         */
        protected $length;

        /**
         * @var int
         */
        protected $chunkSize;

        /**
         * @var \DateTime
         */
        protected $uploadDate;

        /**
         * @var string
         */
        protected $md5;

        /**
         * @var string
         */
        protected $filename;

        /**
         * @var string
         */
        protected $contentType;

        /**
         * @return string
         */
        public function getId()
        {
            return (string) $this->id;
        }

        /**
         * The file can either be a string if the document isn't persisted yet, or a GridFSFile
         * if the document has already been persisted.
         *
         * @return GridFSFile|string
         */
        public function getFile()
        {
            return $this->file;
        }

        /**
         * @param string $file
         *
         * @return File
         */
        public function setFile($file)
        {
            $this->file = $file;

            if (!$this->contentType) {
                $this->contentType = mime_content_type($file);
            }

            return $this;
        }

        /**
         * @return int
         */
        public function getLength()
        {
            return (int) $this->length;
        }

        /**
         * @return int
         */
        public function getChunkSize()
        {
            return (int) $this->chunkSize;
        }

        /**
         * @return \DateTime
         */
        public function getUploadDate()
        {
            return $this->uploadDate;
        }

        /**
         * @return string
         */
        public function getMd5()
        {
            return (string) $this->md5;
        }

        /**
         * @return string
         */
        public function getFilename()
        {
            return (string) $this->filename;
        }

        /**
         * @param string $filename
         *
         * @return File
         */
        public function setFilename($filename)
        {
            if ($filename === '') {
                $filename = null;
            }

            $this->filename = $filename;

            return $this;
        }

        /**
         * @return string
         */
        public function getContentType()
        {
            return (string) $this->contentType;
        }

        /**
         * @param string $contentType
         *
         * @return File
         */
        public function setContentType($contentType)
        {
            if ($contentType === '') {
                $contentType = null;
            }

            $this->contentType = $contentType;

            return $this;
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return $this->getFilename();
        }
    }

.. code-block:: xml

    <!-- @AppBundle/Resources/doctrine/model/File.odm.xml -->
    <?xml version="1.0" encoding="UTF-8"?>
    <doctrine-mongo-mapping xmlns="http://doctrine-project.org/schemas/odm/doctrine-mongo-mapping"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                    xsi:schemaLocation="http://doctrine-project.org/schemas/odm/doctrine-mongo-mapping
                    http://doctrine-project.org/schemas/odm/doctrine-mongo-mapping.xsd">

        <mapped-superclass name="AppBundle\Document\File">
            <field fieldName="id" id="true" />
            <field fieldName="file" type="file" />
            <field fieldName="length" type="int" />
            <field fieldName="chunkSize" type="int" />
            <field fieldName="uploadDate" type="date" />
            <field fieldName="md5" type="string" />
            <field fieldName="filename" type="string" index="true" order="asc" />
            <field fieldName="contentType" type="string" />
        </mapped-superclass>
    </doctrine-mongo-mapping>

After creating the base ``File`` class, we're able to create the concrete product image documents for the original and
cached images. They will be stored in the same collection, but can be differentiated by it's ``type`` field
(see ``DiscriminatorField`` and ``DiscriminatorMap``).

.. code-block:: php

    <?php

    namespace AppBundle\Document\Product;

    use AppBundle\Document;

    class Image extends Document\File
    {
    }

.. code-block:: xml

    <!-- @AppBundle/Resources/doctrine/model/ProductImage.odm.xml -->
    <?xml version="1.0" encoding="UTF-8"?>
    <doctrine-mongo-mapping xmlns="http://doctrine-project.org/schemas/odm/doctrine-mongo-mapping"
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xsi:schemaLocation="http://doctrine-project.org/schemas/odm/doctrine-mongo-mapping
                        http://doctrine-project.org/schemas/odm/doctrine-mongo-mapping.xsd">

        <document name="AppBundle\Document\Product\Image" collection="product_image" inheritance-type="SINGLE_COLLECTION">
            <discriminator-field name="type" />
            <discriminator-map>
                <discriminator-mapping value="image" class="AppBundle\Document\Product\Image" />
                <discriminator-mapping value="cache" class="AppBundle\Document\Product\Image\Cache" />
            </discriminator-map>
            <default-discriminator-value value="image" />
        </document>
    </doctrine-mongo-mapping>

.. note::

    The image cache file stores its ``filter`` property in an embedded metadata object.

.. code-block:: php

    <?php

    namespace AppBundle\Document\Product\Image;

    use AppBundle\Document;

    final class Cache extends Document\Product\Image
    {
        /**
         * @var Cache\Metadata
         */
        private $metadata;

        /**
         * @param string $file
         * @param string $contentType
         * @param string $filename
         * @param string $filter
         */
        public function __construct($file, $contentType, $filename, $filter)
        {
            $this
                ->setContentType($contentType)
                ->setFile($file)
                ->setFilename($filename)
            ;

            $this->metadata = new Cache\Metadata($filter);
        }

        /**
         * @return Cache\Metadata
         */
        public function getMetadata()
        {
            return $this->metadata;
        }
    }

.. code-block:: xml

    <!-- @AppBundle/Resources/doctrine/model/ProductImageCache.odm.xml -->
    <?xml version="1.0" encoding="UTF-8"?>
    <doctrine-mongo-mapping xmlns="http://doctrine-project.org/schemas/odm/doctrine-mongo-mapping"
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xsi:schemaLocation="http://doctrine-project.org/schemas/odm/doctrine-mongo-mapping
                            http://doctrine-project.org/schemas/odm/doctrine-mongo-mapping.xsd">

        <document name="AppBundle\Document\Product\Image\Cache">
            <embed-one field="metadata" target-document="AppBundle\Document\Product\Image\Cache\Metadata" />
        </document>
    </doctrine-mongo-mapping>

.. code-block:: php

    <?php

    namespace AppBundle\Document\Product\Image\Cache;

    final class Metadata
    {
        /**
         * @var string
         */
        private $filter;

        /**
         * @param string $filter
         */
        public function __construct($filter)
        {
            $this->filter = $filter;
        }

        /**
         * @return string
         */
        public function getFilter()
        {
            return $this->filter;
        }
    }

.. code-block:: xml

    <!-- @AppBundle/Resources/doctrine/model/ProductImageCacheMetadata.odm.xml -->
    <?xml version="1.0" encoding="UTF-8"?>
    <doctrine-mongo-mapping xmlns="http://doctrine-project.org/schemas/odm/doctrine-mongo-mapping"
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xsi:schemaLocation="http://doctrine-project.org/schemas/odm/doctrine-mongo-mapping
                            http://doctrine-project.org/schemas/odm/doctrine-mongo-mapping.xsd">

        <embedded-document name="AppBundle\Document\Product\Image\Cache\Metadata">
            <field fieldName="filter" type="string" index="true" order="asc" />
        </embedded-document>
    </doctrine-mongo-mapping>


How to store images in MongoDB GridFS?
--------------------------------------

In Sylius the `KnpGaufretteBundle <https://github.com/KnpLabs/KnpGaufretteBundle>`_ is used to store images.
In order to store images in MongoDB GridFS, we have to create new GridFS loader services for Gaufrette:

First of all a new service is configured.

.. code-block:: xml

    <!-- @AppBundle/Resources/config/services.xml -->
    <?xml version="1.0" encoding="UTF-8" ?>
    <container xmlns="http://symfony.com/schema/dic/services"
               xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
               xsi:schemaLocation="http://symfony.com/schema/dic/services
                http://symfony.com/schema/dic/services/services-1.0.xsd">

        <services>
            <service id="app.gaufrette_loader.doctrine_grid_fs" class="Doctrine\MongoDB\GridFS" public="false">
                <factory service="doctrine.odm.mongodb.document_manager" method="getDocumentCollection" />
                <argument>AppBundle\Document\Product\Image</argument>
            </service>

            <service id="app.gaufrette_loader.grid_fs" class="MongoGridFS">
                <factory service="app.gaufrette_loader.doctrine_grid_fs" method="getMongoCollection" />
            </service>

            <!-- ... -->
        </services>
    </container>

Now we can override the Gaufrette configuration in ``app/config/config.yml`` to use the newly created
loader service ``app.gaufrette_loader.grid_fs``.

.. code-block:: yaml

    knp_gaufrette:
        adapters:
            sylius_image:
                gridfs:
                    mongogridfs_id: app.gaufrette_loader.grid_fs

Once this configuration is changed, newly uploaded images are already stored in MongoDB GridFS.

How to load images from MongoDB GridFS?
---------------------------------------

Loading images from MongoDB GridFS is a bit more complicated and requires some custom classes.

First of all we have to create a new ``data_loader`` for the ``LiipImagineBundle``.

.. code-block:: php

    <?php

    namespace AppBundle\Imagine\Binary\Loader;

    use Doctrine\ODM\MongoDB\DocumentManager;
    use Liip\ImagineBundle\Binary\Loader\LoaderInterface ;
    use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;

    final class GridFSLoader implements LoaderInterface
    {
        /**
         * @var DocumentManager
         */
        protected $dm;

        /**
         * @var string
         */
        protected $class;

        /**
         * @param DocumentManager $dm
         * @param string $class
         */
        public function __construct(DocumentManager $dm, string $class)
        {
            $this->dm = $dm;
            $this->class = $class;
        }

        /**
         * {@inheritdoc}
         */
        public function find($filename)
        {
            $image = $this->dm
                ->getRepository($this->class)
                ->findOneBy(['filename' => $filename]);

            if (!$image) {
                throw new NotLoadableException(sprintf('Source image was not found with filename "%s"', $filename));
            }

            return $image->getFile()->getBytes();
        }
    }

Now we can create the service definition for the data loader:

.. code-block:: xml

    <!-- @AppBundle/Resources/config/services.xml -->
    <?xml version="1.0" encoding="UTF-8" ?>
    <container xmlns="http://symfony.com/schema/dic/services"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://symfony.com/schema/dic/services
            http://symfony.com/schema/dic/services/services-1.0.xsd">

        <services>
            <service id="app.imagine_loader.grid_fs" class="AppBundle\Imagine\Binary\Loader\GridFSLoader">
                <argument type="service" id="doctrine.odm.mongodb.document_manager" />
                <argument>AppBundle\Document\Product\Image</argument>
                <tag name="liip_imagine.binary.loader" loader="app.imagine_loader.grid_fs" />
            </service>

            <!-- ... -->
        </services>
    </container>

The ``LiipImagineBundle`` still doesn't know that we're storing our images in GridFS, which is why we have to create a
custom resolver class that can find an image for a given filename and store new cached filter types of an image.

.. note::

    The route ``product_cache_image`` is defined via an annotation on the ``ImagineController::imageAction`` class method below this example.

.. code-block:: php

    <?php

    namespace AppBundle\Imagine\Cache\Resolver;

    use AppBundle\Document\Product\Image\Cache;
    use Doctrine\ODM\MongoDB\DocumentManager;
    use Doctrine\ODM\MongoDB\DocumentRepository;
    use Liip\ImagineBundle\Binary\BinaryInterface;
    use Liip\ImagineBundle\Imagine\Cache\Resolver\ResolverInterface;
    use Symfony\Component\Routing\RouterInterface;

    final class GridFSResolver implements ResolverInterface
    {
        /**
         * @var DocumentManager
         */
        private $documentManager;

        /**
         * @var string
         */
        private $class;

        /**
         * @var RouterInterface
         */
        private $router;

        /**
         * @param DocumentManager $documentManager
         * @param string $class
         * @param RouterInterface $router
         */
        public function __construct(DocumentManager $documentManager, string $class, RouterInterface $router)
        {
            $this->documentManager = $documentManager;
            $this->class = $class;
            $this->router = $router;
        }

        /**
         * {@inheritdoc}
         */
        public function isStored($path, $filter)
        {
            return $this->findCacheFile($path, $filter) !== null;
        }

        /**
         * {@inheritdoc}
         */
        public function resolve($path, $filter)
        {
            $cache = $this->findCacheFile($path, $filter);

            return $this->router->generate('product_cache_image', ['id' => $cache->getId()], RouterInterface::ABSOLUTE_URL);
        }

        /**
         * {@inheritdoc}
         *
         * @throws GridFSException
         */
        public function store(BinaryInterface $binary, $path, $filter)
        {
            $file = tempnam(sys_get_temp_dir(), 'GridFSResolver');

            if (file_put_contents($file, $binary->getContent()) === false) {
                // We're using a custom exception to make it explicit catchable
                throw new GridFSException("Could not write cache file '{$file}' to disk");
            }

            try {
                $cache = new Cache($file, $binary->getMimeType(), $path, $filter);

                $this->documentManager->persist($cache);
                $this->documentManager->flush();
            } finally {
                @unlink($file);
            }
        }

        /**
         * {@inheritdoc}
         */
        public function remove(array $paths, array $filters)
        {
            if (empty($paths) && empty($filters)) {
                return;
            }

            $queryBuilder = $this->getRepository()->createQueryBuilder();

            $queryBuilder
                ->remove()
                ->multiple()
                ->field('metadata.filter')
                ->in($filters)
            ;

            if (!empty($paths)) {
                $queryBuilder
                    ->field('filename')
                    ->in($paths)
                ;
            }

            $queryBuilder->getQuery()->execute();
        }

        /**
         * @param string $path
         * @param string $filter
         *
         * @return Cache|null
         */
        private function findCacheFile($path, string $filter)
        {
            return $this->getRepository()->findOneBy(['filename' => $path, 'metadata.filter' => $filter]);
        }

        /**
         * @return DocumentRepository
         */
        private function getRepository()
        {
            return $this->documentManager->getRepository($this->class);
        }
    }

.. code-block:: php

    <?php

    namespace AppBundle\Imagine\Cache\Resolver;

    class GridFSException extends \RuntimeException
    {
    }

Create the service definition for the resolver:

.. code-block:: xml

    <!-- @AppBundle/Resources/config/services.xml -->
    <?xml version="1.0" encoding="UTF-8" ?>
    <container xmlns="http://symfony.com/schema/dic/services"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://symfony.com/schema/dic/services
            http://symfony.com/schema/dic/services/services-1.0.xsd">

        <services>
            <service id="app.imagine_resolver.grid_fs" class="AppBundle\Imagine\Cache\Resolver\GridFSResolver">
                <argument type="service" id="doctrine.odm.mongodb.document_manage" />
                <argument>AppBundle\Document\Product\Image\Cache</argument>
                <argument type="service" id="router" />
                <tag name="liip_imagine.cache.resolver" resolver="app.imagine_resolver.grid_fs" />
            </service>

            <!-- ... -->
        </services>
    </container>

Last but not least we have to override the ``liip_imagine`` configuration in the ``app/config/config.yml`` file
to use the new data loader and resolver.

.. code-block:: yaml

    liip_imagine:
        data_loader: app.imagine_loader.grid_fs
        cache: app.imagine_resolver.grid_fs

Now we're going to add a new controller action which can resolve a cached product image and it's route.

.. note::

    This implementation uses the `Symfony Doctrine param converter <https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html#doctrine-converter>`_.

.. code-block:: xml

    <!-- app/config/routing.xml -->
    <?xml version="1.0" encoding="UTF-8" ?>
    <routes xmlns="http://symfony.com/schema/routing"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://symfony.com/schema/routing
            http://symfony.com/schema/routing/routing-1.0.xsd">

        <route id="product_cache_image" path="/product/media/cache/{id}" />

        <!-- ... -->
    </routes>

.. code-block:: php

    <?php

    namespace AppBundle\Controller;

    use AppBundle\Document\Product\Image\Cache;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\ResponseHeaderBag;

    class ImagineController extends Controller
    {
        /**
         * @param Cache $cache
         * @param Request $request
         *
         * @return Response
         */
        public function imageAction(Cache $cache, Request $request)
        {
            $response = new Response();
            $response->setEtag($cache->getMd5());

            if ($response->isNotModified($request)) {
                return $response;
            }

            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $cache->getId());
            $response->headers->set('Content-Disposition', $disposition);
            $response->headers->set('Content-Type', $cache->getContentType());

            $response->setContent($cache->getFile()->getBytes());

            return $response;
        }
    }

Learn more
----------

* `The MongoDB GridFS documentation <https://docs.mongodb.com/manual/core/gridfs/>`_
* `The Doctrine MongoDB ODM documentation <http://docs.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/>`_
* `The DoctrineMongoDBBundle documentation <https://symfony.com/doc/current/bundles/DoctrineMongoDBBundle/index.html>`_
