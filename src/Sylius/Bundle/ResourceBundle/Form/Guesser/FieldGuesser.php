<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\Guesser;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Generates a form class based on a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FieldGuesser
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function guess($resource)
    {
        $resourceClassname = get_class($resource);
        $objectManager = $this->managerRegistry->getManagerForClass($resourceClassname);
        $metadata = $objectManager->getClassMetadata(get_class($resource));

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The default form factory does not support entity classes with multiple primary keys.');
        }

        $fields = array();
        foreach ($this->getFieldsFromMetadata($metadata) as $fieldType => $type) {
            $options = array();

            // TODO : Check if the property exists (must be rewrite)
            if (!method_exists($resource, 'get'.$fieldType)) {
                continue;
            }

            if (in_array($type, array('date', 'datetime'))) {
                $options = array('widget' => 'single_text');
            }

            // Todo : for api only, for the web the model must implement the __toString
            if ('relation' === $type) {
                $options = array('property' => 'id');
            }

            $fields[$fieldType] = $options;
        }

        return $fields;
    }

    /**
     * Returns an array of fields. Fields can be both column fields and
     * association fields.
     *
     * @param ClassMetadataInfo $metadata
     *
     * @return array $fields
     */
    private function getFieldsFromMetadata(ClassMetadataInfo $metadata)
    {
        $fields = (array) $metadata->fieldNames;

        if (!$metadata->isIdentifierNatural()) {
            $fields = array_diff($fields, $metadata->identifier);
        }

        $fieldsMapping = array();

        foreach ($fields as $field) {
            $fieldsMapping[$field] = $metadata->getTypeOfField($field);
        }

        foreach ($metadata->associationMappings as $fieldName => $relation) {
            if ($relation['type'] !== ClassMetadataInfo::ONE_TO_MANY) {
                $fieldsMapping[$fieldName] = 'relation';
            }
        }

        return $fieldsMapping;
    }
}
