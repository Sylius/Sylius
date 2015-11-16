<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Generates a form class based on a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DefaultResourceFormFactory implements DefaultResourceFormFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param FormFactoryInterface $formFactory
     * @param EntityManager        $entityManager
     */
    public function __construct(FormFactoryInterface $formFactory, EntityManager $entityManager)
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function create(RequestConfiguration $requestConfiguration, ResourceMetadataInterface $resourceMetadata)
    {
        $metadata = $this->entityManager->getClassMetadata($resourceMetadata->getClass('model'));

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The default form factory does not support entity classes with multiple primary keys.');
        }

        $builder = $this->getFormBuilder($requestConfiguration->isHtmlRequest(), $resourceMetadata->getClass('model'));

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

    /**
     * @param bool   $isHtmlRequest
     * @param string $modelClassName
     *
     * @return FormBuilderInterface
     */
    private function getFormBuilder($isHtmlRequest, $modelClassName)
    {
        if ($isHtmlRequest) {
            return $this->formFactory->createBuilder('form', null, array('data_class' => $modelClassName));
        }

        return $this->formFactory->createNamedBuilder('', 'form', null, array('data_class' => $modelClassName, 'csrf_protection' => false));
    }
}
