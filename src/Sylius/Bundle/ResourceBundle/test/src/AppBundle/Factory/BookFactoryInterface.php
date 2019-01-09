<?php

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\test\src\AppBundle\Factory;

use AppBundle\Entity\Book;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Factory\TranslatableFactoryInterface;

interface BookFactoryInterface extends TranslatableFactoryInterface
{
}
