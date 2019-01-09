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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ResourceBundle\test\src\AppBundle\Factory\BookFactoryInterface;
use Sylius\Bundle\ResourceBundle\test\src\AppBundle\Repository\BookRepositoryInterface;

final class SecondAutowiredService
{
    /** @var BookFactoryInterface */
    public $bookFactory;

    /** @var BookRepositoryInterface */
    public $bookRepository;

    /** @var EntityManagerInterface */
    public $bookManager;

    public function __construct(
        BookFactoryInterface $bookFactory,
        BookRepositoryInterface $bookRepository,
        EntityManagerInterface $bookManager
    ) {
        $this->bookFactory = $bookFactory;
        $this->bookRepository = $bookRepository;
        $this->bookManager = $bookManager;
    }
}
