<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Doctrine\ORM\Form\Builder;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\Form\Builder\DefaultFormBuilder;
use Sylius\Bundle\ResourceBundle\Form\Builder\DefaultFormBuilderInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @mixin DefaultFormBuilder
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DefaultFormBuilderSpec extends ObjectBehavior
{
    function let(EntityManagerInterface $entityManager)
    {
        $this->beConstructedWith($entityManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Doctrine\ORM\Form\Builder\DefaultFormBuilder');
    }
    
    function it_is_a_default_form_builder()
    {
        $this->shouldImplement(DefaultFormBuilderInterface::class);
    }

    function it_does_not_support_entities_with_multiple_primary_keys(
        MetadataInterface $metadata,
        FormBuilderInterface $formBuilder,
        EntityManagerInterface $entityManager,
        ClassMetadataInfo $classMetadataInfo
    )
    {
        $metadata->getClass('model')->willReturn('AppBundle\Entity\Book');
        $entityManager->getClassMetadata('AppBundle\Entity\Book')->willReturn($classMetadataInfo);
        $classMetadataInfo->identifier = array('id', 'slug');

        $this
            ->shouldThrow(\RuntimeException::class)
            ->during('build', array($metadata, $formBuilder, array()))
        ;
    }

    function it_excludes_non_natural_identifier_from_the_field_list(
        MetadataInterface $metadata,
        FormBuilderInterface $formBuilder,
        EntityManagerInterface $entityManager,
        ClassMetadataInfo $classMetadataInfo
    )
    {
        $metadata->getClass('model')->willReturn('AppBundle\Entity\Book');
        $entityManager->getClassMetadata('AppBundle\Entity\Book')->willReturn($classMetadataInfo);
        $classMetadataInfo->fieldNames = array('id', 'name', 'description', 'enabled');
        $classMetadataInfo->identifier = array('id');
        $classMetadataInfo->isIdentifierNatural()->willReturn(false);
        $classMetadataInfo->getAssociationMappings()->willReturn(array());

        $classMetadataInfo->getTypeOfField('name')->willReturn(Type::STRING);
        $classMetadataInfo->getTypeOfField('description')->willReturn(Type::TEXT);
        $classMetadataInfo->getTypeOfField('enabled')->willReturn(Type::BOOLEAN);

        $formBuilder->add('id', Argument::cetera())->shouldNotBeCalled();
        $formBuilder->add('name', null, array())->shouldBeCalled();
        $formBuilder->add('description', null, array())->shouldBeCalled();
        $formBuilder->add('enabled', null, array())->shouldBeCalled();

        $this->build($metadata, $formBuilder, array());
    }

    function it_does_not_exclude_natural_identifier_from_the_field_list(
        MetadataInterface $metadata,
        FormBuilderInterface $formBuilder,
        EntityManagerInterface $entityManager,
        ClassMetadataInfo $classMetadataInfo
    )
    {
        $metadata->getClass('model')->willReturn('AppBundle\Entity\Book');
        $entityManager->getClassMetadata('AppBundle\Entity\Book')->willReturn($classMetadataInfo);
        $classMetadataInfo->fieldNames = array('id', 'name', 'description', 'enabled');
        $classMetadataInfo->identifier = array('id');
        $classMetadataInfo->isIdentifierNatural()->willReturn(true);
        $classMetadataInfo->getAssociationMappings()->willReturn(array());

        $classMetadataInfo->getTypeOfField('id')->willReturn(Type::INTEGER);
        $classMetadataInfo->getTypeOfField('name')->willReturn(Type::STRING);
        $classMetadataInfo->getTypeOfField('description')->willReturn(Type::TEXT);
        $classMetadataInfo->getTypeOfField('enabled')->willReturn(Type::BOOLEAN);

        $formBuilder->add('id', null, array())->shouldBeCalled();
        $formBuilder->add('name', null, array())->shouldBeCalled();
        $formBuilder->add('description', null, array())->shouldBeCalled();
        $formBuilder->add('enabled', null, array())->shouldBeCalled();

        $this->build($metadata, $formBuilder, array());
    }

    function it_uses_metadata_to_create_appropriate_fields(
        MetadataInterface $metadata,
        FormBuilderInterface $formBuilder,
        EntityManagerInterface $entityManager,
        ClassMetadataInfo $classMetadataInfo
    )
    {
        $metadata->getClass('model')->willReturn('AppBundle\Entity\Book');
        $entityManager->getClassMetadata('AppBundle\Entity\Book')->willReturn($classMetadataInfo);
        $classMetadataInfo->fieldNames = array('name', 'description', 'enabled');
        $classMetadataInfo->isIdentifierNatural()->willReturn(true);
        $classMetadataInfo->getAssociationMappings()->willReturn(array());

        $classMetadataInfo->getTypeOfField('name')->willReturn(Type::STRING);
        $classMetadataInfo->getTypeOfField('description')->willReturn(Type::TEXT);
        $classMetadataInfo->getTypeOfField('enabled')->willReturn(Type::BOOLEAN);

        $formBuilder->add('name', null, array())->shouldBeCalled();
        $formBuilder->add('description', null, array())->shouldBeCalled();
        $formBuilder->add('enabled', null, array())->shouldBeCalled();

        $this->build($metadata, $formBuilder, array());
    }

    function it_uses_single_text_widget_for_datetime_field(
        MetadataInterface $metadata,
        FormBuilderInterface $formBuilder,
        EntityManagerInterface $entityManager,
        ClassMetadataInfo $classMetadataInfo
    )
    {
        $metadata->getClass('model')->willReturn('AppBundle\Entity\Book');
        $entityManager->getClassMetadata('AppBundle\Entity\Book')->willReturn($classMetadataInfo);
        $classMetadataInfo->fieldNames = array('name', 'description', 'enabled', 'publishedAt');
        $classMetadataInfo->isIdentifierNatural()->willReturn(true);
        $classMetadataInfo->getAssociationMappings()->willReturn(array());

        $classMetadataInfo->getTypeOfField('name')->willReturn(Type::STRING);
        $classMetadataInfo->getTypeOfField('description')->willReturn(Type::TEXT);
        $classMetadataInfo->getTypeOfField('enabled')->willReturn(Type::BOOLEAN);
        $classMetadataInfo->getTypeOfField('publishedAt')->willReturn(Type::DATETIME);

        $formBuilder->add('name', null, array())->shouldBeCalled();
        $formBuilder->add('description', null, array())->shouldBeCalled();
        $formBuilder->add('enabled', null, array())->shouldBeCalled();
        $formBuilder->add('publishedAt', null, array('widget' => 'single_text'))->shouldBeCalled();

        $this->build($metadata, $formBuilder, array());
    }

    function it_also_creates_fields_for_relations_other_than_one_to_many(
        MetadataInterface $metadata,
        FormBuilderInterface $formBuilder,
        EntityManagerInterface $entityManager,
        ClassMetadataInfo $classMetadataInfo
    )
    {
        $metadata->getClass('model')->willReturn('AppBundle\Entity\Book');
        $entityManager->getClassMetadata('AppBundle\Entity\Book')->willReturn($classMetadataInfo);
        $classMetadataInfo->fieldNames = array('name', 'description', 'enabled', 'publishedAt');
        $classMetadataInfo->isIdentifierNatural()->willReturn(true);
        $classMetadataInfo->getAssociationMappings()->willReturn(array(
            'category' => array('type' => ClassMetadataInfo::MANY_TO_ONE),
            'users' => array('type' => ClassMetadataInfo::ONE_TO_MANY),
        ));

        $classMetadataInfo->getTypeOfField('name')->willReturn(Type::STRING);
        $classMetadataInfo->getTypeOfField('description')->willReturn(Type::TEXT);
        $classMetadataInfo->getTypeOfField('enabled')->willReturn(Type::BOOLEAN);
        $classMetadataInfo->getTypeOfField('publishedAt')->willReturn(Type::DATETIME);

        $formBuilder->add('name', null, array())->shouldBeCalled();
        $formBuilder->add('description', null, array())->shouldBeCalled();
        $formBuilder->add('enabled', null, array())->shouldBeCalled();
        $formBuilder->add('publishedAt', null, array('widget' => 'single_text'))->shouldBeCalled();
        $formBuilder->add('category', null, array('property' => 'id'))->shouldBeCalled();
        $formBuilder->add('users', Argument::cetera())->shouldNotBeCalled();

        $this->build($metadata, $formBuilder, array());
    }
}
