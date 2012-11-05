<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Repository\Doctrine;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\ResourceBundle\Repository\ResourceRepository;

/**
 * Base Doctrine resource manager class.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class DoctrineResourceRepository extends ResourceRepository implements DoctrineResourceRepositoryInterface
{
    protected $objectRepository;

    public function __construct(ObjectRepository $objectRepository, $class)
    {
        $this->objectRepository = $objectRepository;

        parent::__construct($class);
    }

    public function getObjectRepository()
    {
        return $this->objectRepository;
    }
}
