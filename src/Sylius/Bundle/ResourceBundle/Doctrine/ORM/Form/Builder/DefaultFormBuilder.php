<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine\ORM\Form\Builder;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sylius\Bundle\ResourceBundle\Form\Builder\DefaultFormBuilderInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DefaultFormBuilder implements DefaultFormBuilderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function build(MetadataInterface $metadata, FormBuilderInterface $formBuilder, array $options)
    {
        $classMetadata = $this->entityManager->getClassMetadata($metadata->getClass('model'));

        if (1 < count($classMetadata->identifier)) {
            throw new \RuntimeException('The default form factory does not support entity classes with multiple primary keys.');
        }

        $fields = (array) $classMetadata->fieldNames;

        if (!$classMetadata->isIdentifierNatural()) {
            $fields = array_diff($fields, $classMetadata->identifier);
        }

        foreach ($fields as $fieldName) {
            $options = [];

            if (in_array($fieldName, ['createdAt', 'updatedAt'])) {
                continue;
            }

            if (Type::DATETIME === $classMetadata->getTypeOfField($fieldName)) {
                $options = ['widget' => 'single_text'];
            }

            $formBuilder->add($fieldName, null, $options);
        }

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $associationMapping) {
            if (ClassMetadataInfo::ONE_TO_MANY !== $associationMapping['type']) {
                $formBuilder->add($fieldName, null, ['choice_label' => 'id']);
            }
        }
    }
}
