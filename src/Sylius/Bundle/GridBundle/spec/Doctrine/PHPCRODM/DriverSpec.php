<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Doctrine\PHPCRODM;

use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\ODM\PHPCR\DocumentRepository;
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\DataSource;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Parameters;

/**
 * @require Doctrine\ODM\PHPCR\DocumentManagerInterface
 */
final class DriverSpec extends ObjectBehavior
{
    function let(DocumentManagerInterface $documentManager): void
    {
        $this->beConstructedWith($documentManager);
    }

    function it_implements_grid_driver(): void
    {
        $this->shouldImplement(DriverInterface::class);
    }

    function it_throws_exception_if_class_is_undefined(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getDataSource', [[], new Parameters()]);
        ;
    }

    function it_creates_data_source_via_doctrine_phpcrodm_query_builder(
        DocumentManagerInterface $documentManager,
        DocumentRepository $documentRepository,
        QueryBuilder $queryBuilder
    ): void {
        $documentManager->getRepository('App:Book')->willReturn($documentRepository);
        $documentRepository->createQueryBuilder('o')->willReturn($queryBuilder);

        $this->getDataSource(['class' => 'App:Book'], new Parameters())->shouldHaveType(DataSource::class);
    }
}
