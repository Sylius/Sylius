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
use Symfony\Component\PropertyAccess\StringUtil;

class BaseContext extends DefaultContext
{
    /**
     * @Given /^there are no ([^"]*)$/
     */
    public function thereAreNoResources($type)
    {
        $type = str_replace(' ', '_', StringUtil::singularify($type));
        $type = is_array($type) ? $type[1] : $type; // Hacky hack for multiple singular forms.
        $type = $type == 'addresse' ? 'address' : $type; // Hacky hack again because we do not retrieve the right singular with the previous hack...

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
        $data = $table->getRowsHash();
        $type = str_replace(' ', '_', trim($type));

        $object = $this->findOneByName($type, $data['name']);
        foreach ($data as $property => $value) {
            $objectValue = $object->{'get'.ucfirst($property)}();
            if (is_array($objectValue)) {
                $objectValue = implode(',', $objectValue);
            }

            if ($objectValue !== $value) {
                throw new \Exception(sprintf('%s object::%s has "%s" value but "%s" expected', $type, $property, $objectValue, is_array($value) ? implode(',', $value) : $value));
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
} 