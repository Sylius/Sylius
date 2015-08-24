<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\ImportExport\Reader\ORM;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\CoreBundle\ImportExport\Processor\UserProcessorInterface;
use Sylius\Component\ImportExport\Model\JobInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserReaderSpec extends ObjectBehavior
{
    function let(
        UserProcessorInterface $userProcessor,
        EntityRepository $userRepository
    )
    {
        $this->beConstructedWith($userProcessor, $userRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\ImportExport\Reader\ORM\UserReader');
    }

    function it_implements_import_export_reader_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Reader\ReaderInterface');
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn('user_orm');
    }

    function it_has_result_code()
    {
        $this->getResultCode()->shouldReturn(0);
    }

    function it_reads_data(
        $userRepository,
        $userProcessor,
        AbstractQuery $query,
        \DateTime $dateTime,
        LoggerInterface $logger,
        QueryBuilder $queryBuilder
    )
    {
        $expectedResponse = array(
            array(
                'date' => '2012-07-08 11:14:15',
                'field' => 'value',
            )
        );

        $rawRead = array(
            array(
                'date' => $dateTime,
                'field' => 'value',
            ),
        );
        $userRepository->createQueryBuilder('user')->willReturn($queryBuilder)->shouldBeCalled();

        $queryBuilder->addSelect('customer')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->leftJoin('user.customer', 'customer')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setFirstResult(0)->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setMaxResults(2)->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getResult(2)->shouldBeCalled()->willReturn(
            array(
                array(
                    'date' => $dateTime,
                    'field' => 'value',
                ),
            )
        );

        $userProcessor->convert($rawRead, 'Y-m-d H:i:s')->shouldBeCalled()->willReturn($expectedResponse);

        $configuration = array(
            'batch_size'  => '2',
            'class'       => 'Sylius\Component\Core\Model\User',
            'date_format' => 'Y-m-d H:i:s',
        );

        $this->read($configuration, $logger)->shouldReturn($expectedResponse);
    }

    function it_returns_null_if_no_data_was_read(
        $userRepository,
        AbstractQuery $query,
        LoggerInterface $logger,
        QueryBuilder $queryBuilder
    )
    {
        $userRepository->createQueryBuilder('user')->willReturn($queryBuilder)->shouldBeCalled();

        $queryBuilder->addSelect('customer')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->leftJoin('user.customer', 'customer')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setFirstResult(0)->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setMaxResults(2)->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getResult(2)->shouldBeCalled()->willReturn(array());

        $configuration = array(
            'batch_size' => '2',
            'date_format' => 'Y-m-d H:i:s',
        );

        $this->read($configuration, $logger)->shouldReturn(null);
    }

    function it_finalize_job(JobInterface $job)
    {
        $job->addMetadata(array('result_code' => 0));
        $this->finalize($job);
    }
}