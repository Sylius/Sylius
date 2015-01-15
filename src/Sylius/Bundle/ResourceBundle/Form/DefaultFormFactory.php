<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Generates a form class based on a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DefaultFormFactory
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function create($resource, EntityManager $entityManager)
    {
        $metadata = $entityManager->getClassMetadata(get_class($resource));

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The default form factory does not support entity classes with multiple primary keys.');
        }

        $builder = $this->formFactory->createNamedBuilder('', 'form', $resource, array('csrf_protection' => false));

        foreach ($this->getFieldsFromMetadata($metadata) as $field => $type) {
            $options = array();

            if (in_array($type, array('date', 'datetime'))) {
                $options = array('widget' => 'single_text');
            }
            if ('relation' === $type) {
                $options = array('property' => 'id');
            }

            $builder->add($field, null, $options);
        }

        return $builder->getForm();
    }

    /**
     * Returns an array of fields. Fields can be both column fields and
     * association fields.
     *
     * @param  ClassMetadataInfo $metadata
     *
     * @return array             $fields
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
