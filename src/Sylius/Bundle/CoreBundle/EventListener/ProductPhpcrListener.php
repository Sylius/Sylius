<?php

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;

use PHPCR\NodeInterface;
use PHPCR\Util\NodeHelper;
use PHPCR\Util\UUIDHelper;

use Sylius\Bundle\CoreBundle\Model\Product;
use Sylius\Bundle\ProductBundle\Model\ProductProperty;

class ProductPhpcrListener
{
    /** @var ManagerRegistry  */
    protected $registry;

    /** @var \PHPCR\SessionInterface $phpcrSession */
    protected $phpcrSession;

    /** @var string */
    protected $productPath;

    public function __construct(ManagerRegistry $registry, $productPath)
    {
        $this->registry = $registry;
        $this->productPath = $productPath;
    }

    public function getPhpcrSession()
    {
        if (!$this->phpcrSession) {
            $this->phpcrSession = $this->registry->getConnection();
        }

        return $this->phpcrSession;
    }

    public function preFlush(PreFlushEventArgs $args)
    {
        $phpcrSession = $this->getPhpcrSession();
        if ($phpcrSession->nodeExists($this->productPath)) {
            $node = $phpcrSession->getNode($this->productPath);
        } else {
            $node = NodeHelper::createPath($phpcrSession, $this->productPath);
        }

        /** @var $em EntityManager */
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        $identityMap = $uow->getIdentityMap();
        if (!empty($identityMap['Sylius\Bundle\CoreBundle\Model\Product'])) {
            /** @var $product \Sylius\Bundle\CoreBundle\Model\Product */
            foreach ($identityMap['Sylius\Bundle\CoreBundle\Model\Product'] as $product) {
                $this->movePropertyData($product, $node, $em);
            }
        }

        $insertions = $uow->getScheduledEntityInsertions();
        foreach ($insertions as $entity) {
            if ($entity instanceOf Product) {
                $this->movePropertyData($entity, $node, $em);
            }
        }

        $phpcrSession->save();
    }

    private function movePropertyData(Product $product, NodeInterface $parentNode, EntityManager $em)
    {
        $properties = $product->getProperties();
        if ($properties->isEmpty()) {
            return;
        }

        if (!$product->getUuid()) {
            $product->setUuid(UUIDHelper::generateUUID());
        }

        if ($parentNode->hasNode($product->getUuid())) {
            $productNode = $parentNode->getNode($product->getUuid());
        } else {
            $productNode = $parentNode->addNode($product->getUuid());
            $productNode->addMixin('mix:referenceable');
            $productNode->setProperty('jcr:uuid', $product->getUuid());
        }

        $existingProperties = array();
        /** @var $property \Sylius\Bundle\ProductBundle\Model\ProductPropertyInterface */
        foreach ($properties as $property) {
            $em->detach($property);
            $product->removeProperty($property);
            $productNode->setProperty($property->getName(), $property->getValue());
            $existingProperties[] = $property->getName();
        }

        foreach ($productNode->getProperties() as $property) {
            $name = $property->getName();
            if (!in_array($name, $existingProperties) && 0 !== strpos($name, 'jcr:')) {
                $productNode->setProperty($name, null);
            }
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Product || !$entity->getUuid()) {
            return;
        }

        $phpcrSession = $this->getPhpcrSession();

        /** @var NodeInterface $productNode */
        $productNode = $phpcrSession->getNode($this->productPath.'/'.$entity->getUuid());
        if (!$productNode) {
            return;
        }

        /** @var $em EntityManager */
        $em = $args->getEntityManager();
        $propertyRepository = $em->getRepository('\Sylius\Bundle\ProductBundle\Model\Property');
        foreach ($productNode->getPropertiesValues() as $name => $value) {
            if (0 !== strpos($name, 'jcr:')) {
                $productProperty = new ProductProperty();
                $productProperty->setProperty($propertyRepository->findOneBy(array('name' => $name)));
                $productProperty->setValue($value);
                $entity->addProperty($productProperty);
            }
        }
    }
}