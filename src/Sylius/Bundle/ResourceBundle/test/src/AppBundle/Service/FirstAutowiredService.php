<?php

declare(strict_types=1);

namespace AppBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class FirstAutowiredService
{
    /** @var FactoryInterface */
    public $bookFactory;

    /** @var RepositoryInterface */
    public $bookRepository;

    /** @var ObjectManager */
    public $bookManager;

    public function __construct(
        FactoryInterface $bookFactory,
        RepositoryInterface $bookRepository,
        ObjectManager $bookManager
    ) {
        $this->bookFactory = $bookFactory;
        $this->bookRepository = $bookRepository;
        $this->bookManager = $bookManager;
    }
}
