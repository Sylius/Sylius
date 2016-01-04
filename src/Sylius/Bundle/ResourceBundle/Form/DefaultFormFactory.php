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

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo as ORMClassMetadataInfo;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo as ODMClassMetadataInfo;
use Symfony\Component\Form\FormFactoryInterface;

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

    public function create($resource, ObjectManager $entityManager)
    {
        $metadata = $entityManager->getClassMetadata(get_class($resource));

        if (count($metadata->getIdentifier()) > 1) {
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
     * @param ClassMetadata $metadata
     *
     * @return array $fields
     */
    private function getFieldsFromMetadata(ClassMetadata $metadata)
    {
        $fields = (array)$metadata->getFieldNames();

        if (!$this->isIdentifierNatural($metadata)) {
            $fields = array_diff($fields, $metadata->getIdentifier());
        }

        $fieldsMapping = array();

        foreach ($fields as $field) {
            $fieldsMapping[$field] = $metadata->getTypeOfField($field);
        }

        foreach ($metadata->associationMappings as $fieldName => $relation) {
            if ($relation['type'] !== ORMClassMetadataInfo::ONE_TO_MANY) {
                $fieldsMapping[$fieldName] = 'relation';
            }
        }

        return $fieldsMapping;
    }

    /**
     * It is a WORKAROUND because Doctrine ClassMetadata has different implementation
     * for ORM and ODM.
     *
     * @param ClassMetadata $metadata
     * @return bool
     */
    private function isIdentifierNatural(ClassMetadata $metadata)
    {
        if ($metadata instanceof ORMClassMetadataInfo) {
            return $metadata->isIdentifierNatural();
        }
        if ($metadata instanceof ODMClassMetadataInfo) {
            return $metadata->generatorType === ODMClassMetadataInfo::GENERATOR_TYPE_NONE;
        }
        return true;
    }
}
