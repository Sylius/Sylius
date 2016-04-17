<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\GridBundle\Doctrine\PHPCRODM;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Parameters;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\ODM\PHPCR\DocumentRepository;
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\DataSource;

/**
 * @mixin Driver
 */
class DriverSpec extends ObjectBehavior
{
    function let(DocumentManagerInterface $documentManager)
    {
        $this->beConstructedWith($documentManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\Driver');
    }

    function it_implements_grid_driver()
    {
        $this->shouldImplement(DriverInterface::class);
    }

    function it_throws_exception_if_class_is_undefined(Parameters $parameters)
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getDataSource', [[], $parameters]);
        ;
    }

    function it_creates_data_source_via_doctrine_phpcrodm_query_builder(
        DocumentManagerInterface $documentManager,
        DocumentRepository $documentRepository,
        QueryBuilder $queryBuilder,
        Parameters $parameters
    ) {
        $documentManager->getRepository('App:Book')->willReturn($documentRepository);
        $documentRepository->createQueryBuilder('o')->willReturn($queryBuilder);
        
        $this->getDataSource(['class' => 'App:Book'], $parameters)->shouldHaveType(DataSource::class);
    }
}
