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

namespace AppBundle\Service;

use AppBundle\Factory\BookFactory;
use AppBundle\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

class NoInterfaceAutowiredService
{
    /** @var BookFactory */
    public $bookFactory;

    /** @var BookRepository */
    public $bookRepository;

    /** @var EntityManagerInterface */
    public $bookManager;

    public function __construct(
        BookFactory $bookFactory,
        BookRepository $bookRepository,
        EntityManagerInterface $bookManager
    ) {
        $this->bookFactory = $bookFactory;
        $this->bookRepository = $bookRepository;
        $this->bookManager = $bookManager;
    }
}
