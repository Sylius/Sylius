<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Resource\Repository;

use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use spec\Sylius\Component\Resource\Fixtures\SampleBookResourceInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\Exception\ExistingResourceException;
use Sylius\Component\Resource\Repository\InMemoryRepository;
use Sylius\Component\Resource\Repository\RepositoryInterface;

require_once __DIR__ . '/../Fixtures/SampleBookResourceInterface.php';

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class InMemoryRepositorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(SampleBookResourceInterface::class);
    }

    function it_throws_invalid_argument_exception_when_constructing_with_null()
    {
        $this->beConstructedWith(null);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_unexpected_type_exception_when_constructing_without_resource_interface()
    {
        $this->beConstructedWith(\stdClass::class);

        $this->shouldThrow(UnexpectedTypeException::class)->duringInstantiation();
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(InMemoryRepository::class);
    }

    function it_implements_repository_interface()
    {
        $this->shouldImplement(RepositoryInterface::class);
    }

    function it_throws_invalid_argument_exception_when_adding_wrong_resource_type(ResourceInterface $resource)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('add', [$resource]);
    }

    function it_adds_an_object(SampleBookResourceInterface $monocle)
    {
        $monocle->getId()->willReturn(2);

        $this->add($monocle);
        $this->findOneBy(['id' => 2])->shouldReturn($monocle);
    }

    function it_throws_existing_resource_exception_on_adding_a_resource_which_is_already_in_repository(SampleBookResourceInterface $bike)
    {
        $this->add($bike);
        $this->shouldThrow(ExistingResourceException::class)->during('add', [$bike]);
    }

    function it_removes_a_resource(SampleBookResourceInterface $shirt)
    {
        $shirt->getId()->willReturn(5);

        $this->add($shirt);
        $this->remove($shirt);

        $this->findOneBy(['id' => 5])->shouldReturn(null);
    }

    function it_finds_object_by_id(SampleBookResourceInterface $monocle)
    {
        $monocle->getId()->willReturn(2);

        $this->add($monocle);
        $this->find(2)->shouldReturn($monocle);
    }

    function it_returns_null_if_cannot_find_object_by_id()
    {
        $this->find(2)->shouldReturn(null);
    }

    function it_returns_all_objects_when_finding_by_an_empty_parameter_array(
        SampleBookResourceInterface $book,
        SampleBookResourceInterface $shirt
    ) {
        $book->getId()->willReturn(10);
        $book->getName()->willReturn('Book');

        $shirt->getId()->willReturn(5);
        $shirt->getName()->willReturn('Shirt');

        $this->add($book);
        $this->add($shirt);

        $this->findBy([])->shouldReturn([$book, $shirt]);
    }

    function it_finds_many_objects_by_multiple_criteria_orders_a_limit_and_an_offset(
        SampleBookResourceInterface $firstBook,
        SampleBookResourceInterface $secondBook,
        SampleBookResourceInterface $thirdBook,
        SampleBookResourceInterface $fourthBook,
        SampleBookResourceInterface $wrongIdBook,
        SampleBookResourceInterface $wrongNameBook
    ) {
        $id = 80;
        $name = 'Book';

        $firstBook->getId()->willReturn($id);
        $secondBook->getId()->willReturn($id);
        $thirdBook->getId()->willReturn($id);
        $fourthBook->getId()->willReturn($id);
        $wrongNameBook->getId()->willReturn($id);
        $wrongIdBook->getId()->willReturn(100);

        $firstBook->getName()->willReturn($name);
        $secondBook->getName()->willReturn($name);
        $thirdBook->getName()->willReturn($name);
        $fourthBook->getName()->willReturn($name);
        $wrongIdBook->getName()->willReturn($name);
        $wrongNameBook->getName()->willReturn('Tome');

        $firstBook->getRating()->willReturn(3);
        $secondBook->getRating()->willReturn(2);
        $thirdBook->getRating()->willReturn(2);
        $fourthBook->getRating()->willReturn(4);

        $firstBook->getTitle()->willReturn('World War Z');
        $secondBook->getTitle()->willReturn('World War Z');
        $thirdBook->getTitle()->willReturn('Call of Cthulhu');
        $fourthBook->getTitle()->willReturn('Art of War');

        $this->add($firstBook);
        $this->add($secondBook);
        $this->add($thirdBook);
        $this->add($fourthBook);
        $this->add($wrongIdBook);
        $this->add($wrongNameBook);

        $this->findBy(
            $criteria = [
                'name' => $name,
                'id' => $id,
            ],
            $orderBy = [
                'rating' => RepositoryInterface::ORDER_ASCENDING,
                'title' => RepositoryInterface::ORDER_DESCENDING,
            ],
            $limit = 2,
            $offset = 1
        )->shouldReturn([$secondBook, $thirdBook]);
    }

    function it_throws_invalid_argument_exception_when_finding_one_object_with_empty_parameter_array()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('findOneBy', [[]]);
    }

    function it_finds_one_object_by_parameter(SampleBookResourceInterface $book, SampleBookResourceInterface $shirt)
    {
        $book->getName()->willReturn('Book');
        $shirt->getName()->willReturn('Shirt');

        $this->add($book);
        $this->add($shirt);

        $this->findOneBy(['name' => 'Book'])->shouldReturn($book);
    }

    function it_returns_first_result_while_finding_one_by_parameters(
        SampleBookResourceInterface $book,
        SampleBookResourceInterface $secondBook
    ) {
        $book->getName()->willReturn('Book');
        $secondBook->getName()->willReturn('Book');

        $this->add($book);
        $this->add($secondBook);

        $this->findOneBy(['name' => 'Book'])->shouldReturn($book);
    }

    function it_finds_all_objects_in_memory(SampleBookResourceInterface $book, SampleBookResourceInterface $shirt)
    {
        $this->add($book);
        $this->add($shirt);

        $this->findAll()->shouldReturn([$book, $shirt]);
    }

    function it_return_empty_array_when_memory_is_empty()
    {
        $this->findAll()->shouldReturn([]);
    }

    function it_creates_paginator()
    {
        $this->createPaginator()->shouldHaveType(Pagerfanta::class);
    }

    function it_returns_stated_class_name()
    {
        $this->getClassName()->shouldReturn(SampleBookResourceInterface::class);
    }
}
