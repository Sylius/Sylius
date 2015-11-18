<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\StringUtil;

class FixtureContext extends DefaultContext
{
    /**
     * @Given /^there are the following ([^"]*):$/
     */
    public function thereAreFollowingResources($resource, TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsResource($resource, $data, false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^there is the following "([^"]*)":$/
     */
    public function thereIsResource($resource, $additionalData, $flush = true)
    {
        if ($additionalData instanceof TableNode) {
            $additionalData = $additionalData->getHash();
        }

        $entity = $this->getRepository($resource)->createNew();

        if (count($additionalData) > 0) {
            $this->setDataToObject($entity, $additionalData);
        }

        return $this->persistAndFlush($entity, $flush);
    }

    /**
     * @Given /^there are no ([^"]*)$/
     */
    public function thereAreNoResources($type)
    {
        $type = str_replace(' ', '_', StringUtil::singularify($type));
        // Hacky hack for multiple singular forms.
        $type = is_array($type) ? $type[1] : $type;
        // Hacky hack again because we do not retrieve the right singular with the previous hack...
        $type = $type == 'addresse' ? 'address' : $type;

        $manager = $this->getEntityManager();

        foreach ($this->getRepository($type)->findAll() as $resource) {
            $manager->remove($resource);
        }

        $manager->flush();
    }

    /**
     * @Given /^([^""]*) with following data should be created:$/
     */
    public function objectWithFollowingDataShouldBeCreated($type, TableNode $table)
    {
        $accessor = new PropertyAccessor();

        $data = $table->getRowsHash();
        $type = str_replace(' ', '_', trim($type));

        $object = $this->waitFor(function () use ($type, $data) {
            try {
                return $this->findOneByName($type, $data['name']);
            } catch (\InvalidArgumentException $exception) {
                return null;
            }
        });

        foreach ($data as $property => $value) {
            $objectValue = $accessor->getValue($object, $property);
            if (is_array($objectValue)) {
                $objectValue = implode(',', $objectValue);
            }

            if ($objectValue !== $value) {
                throw new \Exception(sprintf(
                    '%s object::%s has "%s" value but "%s" expected',
                    $type,
                    $property,
                    $objectValue,
                    is_array($value) ? implode(',', $value) : $value)
                );
            }
        }
    }

    /**
     * @Given /^I have deleted the ([^"]*) "([^""]*)"/
     */
    public function haveDeleted($resource, $name)
    {
        $manager = $this->getEntityManager();
        $manager->remove($this->findOneByName($resource, $name));
        $manager->flush();
    }

    /**
     * @Given I deleted :type with :property :value
     */
    public function iDeletedResource($type, $property, $value)
    {
        $user = $this->findOneBy($type, array($property => $value));
        $entityManager = $this->getEntityManager();
        $entityManager->remove($user);
        $entityManager->flush();
    }

    /**
     * Set data to an object.
     *
     * @param $object
     * @param $data
     */
    protected function setDataToObject($object, array $data)
    {
        foreach ($data as $property => $value) {
            if (1 === preg_match('/date/', strtolower($property))) {
                $value = new \DateTime($value);
            }

            $propertyName = ucfirst($property);
            if (false !== strpos(' ', $property)) {
                $propertyName = '';
                $propertyParts = explode(' ', $property);

                foreach ($propertyParts as $part) {
                    $part = ucfirst($part);
                    $propertyName .= $part;
                }
            }

            $method = 'set'.$propertyName;
            if (method_exists($object, $method)) {
                $object->{'set'.$propertyName}($value);
            }
        }
    }

    /**
     * Persist and flush $entity.
     *
     * @param object $entity
     * @param bool $flush
     *
     * @return mixed
     */
    protected function persistAndFlush($entity, $flush = true)
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $entity;
    }
}
