<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AssociationBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use PhpSpec\ObjectBehavior;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class LoadMetadataSubscriberSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'product' => [
                'subject' => 'Some\App\Product\Entity\Product',
                'association' => [
                    'classes' => [
                        'model' => 'Some\App\Product\Entity\EntityAssociation',
                    ],
                ],
                'association_type' => [
                    'classes' => [
                        'model' => 'Some\App\Product\Entity\AssociationType',
                    ]
                ],
            ],
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AssociationBundle\EventListener\LoadMetadataSubscriber');
    }

    function it_is_a_doctrine_event_subscriber()
    {
        $this->shouldImplement(EventSubscriber::class);
    }

    function it_subscribes_a_doctrines_load_class_metadata_event()
    {
        $this->getSubscribedEvents()->shouldReturn(['loadClassMetadata']);
    }

    function it_loads_class_metadata_for_associations(
        LoadClassMetadataEventArgs $eventArguments,
        ClassMetadataInfo $metadata,
        EntityManager $entityManager,
        ClassMetadataFactory $classMetadataFactory,
        ClassMetadataInfo $classMetadataInfo
    ) {
        $eventArguments->getClassMetadata()->willReturn($metadata)->shouldBeCalled();
        $eventArguments->getEntityManager()->willReturn($entityManager)->shouldBeCalled();
        $entityManager->getMetadataFactory()->willReturn($classMetadataFactory)->shouldBeCalled();
        $classMetadataInfo->fieldMappings = [
            'id' => [
                'columnName' => 'id',
            ],
        ];

        $classMetadataFactory->getMetadataFor('Some\App\Product\Entity\Product')->willReturn($classMetadataInfo);
        $classMetadataFactory->getMetadataFor('Some\App\Product\Entity\AssociationType')->willReturn($classMetadataInfo);

        $subjectMapping = [
            'fieldName' => 'owner',
            'targetEntity' => 'Some\App\Product\Entity\Product',
            'inversedBy' => 'associations',
            'joinColumns' => [[
                'name' => 'product_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];
        $associationMapping = [
            'fieldName' => 'associatedObjects',
            'targetEntity' => 'Some\App\Product\Entity\Product',
            'joinTable' => [
                'name' => 'sylius_product_association_product',
                'joinColumns' => [[
                    'name' => 'association_id',
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                    'unique' => false,
                    'onDelete' => 'CASCADE',
                ]],
                'inverseJoinColumns' => [[
                    'name' => 'product_id',
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                    'unique' => false,
                    'onDelete' => 'CASCADE',
                ]],
            ],
        ];
        $associationTypeMapping = [
            'fieldName' => 'type',
            'targetEntity' => 'Some\App\Product\Entity\AssociationType',
            'joinColumns' => [[
                'name' => 'association_type_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];

        $metadata->getName()->willReturn('Some\App\Product\Entity\EntityAssociation');
        $metadata->mapManyToOne($subjectMapping)->shouldBeCalled();
        $metadata->mapManyToMany($associationMapping)->shouldBeCalled();
        $metadata->mapManyToOne($associationTypeMapping)->shouldBeCalled();

        $this->loadClassMetadata($eventArguments);
    }
}
