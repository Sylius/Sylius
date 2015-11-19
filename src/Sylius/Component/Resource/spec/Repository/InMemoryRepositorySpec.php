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
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Repository\InMemoryRepository;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class InMemoryRepositorySpec extends ObjectBehavior
{
    function let(RepositableInterface $book, RepositableInterface $shirt)
    {
        $this->beConstructedWith(RepositableInterface::class);

        $book->getId()->willReturn(10);
        $book->getName()->willReturn('Book');
        $book->getRating()->willReturn(5);

        $shirt->getId()->willReturn(5);
        $shirt->getName()->willReturn('Shirt');

        $this->add($book);
        $this->add($shirt);
    }

    function it_throws_invalid_argument_exception_when_constructing_with_null()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', array(null));
    }

    function it_throws_unexpected_type_exception_when_constructing_without_resource_interface(Void $void)
    {
        $this->shouldThrow(UnexpectedTypeException::class)->during('__construct', array($void));
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
        $this->shouldThrow(\InvalidArgumentException::class)->during('add', array($resource));
    }

    function it_throws_invalid_argument_exception_when_adding_object_with_null_id(RepositableInterface $mug)
    {
        $mug->getId()->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('add', array($mug));
    }

    function it_throws_invalid_argument_exception_when_adding_different_objects_with_same_id(
        RepositableInterface $leftShoe,
        RepositableInterface $rightShoe
    ) {
        $leftShoe->getId()->willReturn(1);
        $leftShoe->getName()->willReturn('leftShoe');
        $rightShoe->getId()->willReturn(1);
        $rightShoe->getName()->willReturn('rightShoe');

        $this->add($leftShoe);

        $this->shouldThrow(\InvalidArgumentException::class)->during('add', array($rightShoe));
    }

    function it_adds_an_object(RepositableInterface $monocle)
    {
        $monocle->getId()->willReturn(2);

        $this->add($monocle);
        $this->find(2)->shouldReturn($monocle);
    }

    function it_removes_a_resource(RepositableInterface $shirt)
    {
        $this->remove($shirt);

        $this->find(5)->shouldReturn(null);
    }

    function it_finds_an_object_by_id(RepositableInterface $book)
    {
        $this->find(10)->shouldReturn($book);
    }

    function it_returns_null_when_finding_with_null_parameter()
    {
        $this->find(null)->shouldReturn(null);
    }

    function it_returns_null_when_finding_unavailable_id()
    {
        $this->find(11)->shouldReturn(null);
    }

    function it_finds_many_objects_with_parameter(RepositableInterface $book)
    {
        $book->getName()->shouldBeCalled();

        $this->findBy(array('name' => 'Book'))->shouldReturn(array($book));
    }

    function it_finds_all_objects_when_parameter_is_not_set(RepositableInterface $book, RepositableInterface $shirt)
    {
        $this->findBy()->shouldReturn(array($book, $shirt));
    }

    function it_finds_many_objects_with_parameter_order_limit_offset(
        RepositableInterface $secondBook,
        RepositableInterface $thirdBook,
        RepositableInterface $fourthBook
    ) {
        $secondBook->getId()->willReturn(80);
        $thirdBook->getId()->willReturn(81);
        $fourthBook->getId()->willReturn(82);

        $secondBook->getName()->willReturn('Book');
        $thirdBook->getName()->willReturn('Book');
        $fourthBook->getName()->willReturn('Book');

        $secondBook->getRating()->willReturn(3);
        $thirdBook->getRating()->willReturn(2);
        $fourthBook->getRating()->willReturn(1);

        $this->add($secondBook);
        $this->add($thirdBook);
        $this->add($fourthBook);

        $this->findBy(
            array('name' => 'Book'),
            array('rating' => 'ASC'),
            $limit = 2,
            $offset = 1
        )->shouldReturn(array($thirdBook, $secondBook));
    }

    function it_throws_unexpected_value_exception_on_multiple_results_while_finding_one_object_by_parameter(RepositableInterface $secondBook)
    {
        $secondBook->getId()->willReturn(81);
        $secondBook->getName()->willReturn('Book');

        $this->add($secondBook);

        $this->shouldThrow(\UnexpectedValueException::class)->during('findOneBy', array(array('name' => 'Book')));
    }

    function it_finds_one_object_by_parameter(RepositableInterface $book)
    {
        $this->findOneBy(array('name' => 'Book'))->shouldReturn($book);
    }

    function it_finds_all_objects_in_memory(RepositableInterface $book, RepositableInterface $shirt)
    {
        $this->findAll()->shouldReturn(array($book, $shirt));
    }

    function it_return_empty_array_when_memory_is_empty(RepositableInterface $book, RepositableInterface $shirt)
    {
        $this->remove($book);
        $this->remove($shirt);

        $this->findAll()->shouldReturn(array());
    }

    function it_creates_paginator()
    {
        $this->createPaginator()->shouldHaveType(Pagerfanta::class);
    }

    function it_returns_stated_class_name()
    {
        $this->getClassName()->shouldReturn(RepositableInterface::class);
    }
}

class Void
{
}

interface RepositableInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return int
     */
    public function getRating();
}

class Repositable implements RepositableInterface
{
    /**
     * @var mixed
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $rating;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getRating()
    {
        return $this->rating;
    }
}
