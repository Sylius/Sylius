<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Originator;

use Sylius\Component\Core\Model\OriginAwareInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Originator implements OriginatorInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getOrigin(OriginAwareInterface $originAware)
    {
        if (null === $originAware->getOriginId() || null === $originAware->getOriginType()) {
            return null;
        }

        return $this->em
            ->getRepository($originAware->getOriginType())
            ->find($originAware->getOriginId())
        ;
    }

    public function setOrigin(OriginAwareInterface $originAware, $origin)
    {
        if (!is_object($origin)) {
            throw new UnexpectedTypeException($origin, 'object');
        }

        if (!method_exists($origin, 'getId')) {
            throw new \InvalidArgumentException(sprintf(
                'Unable to get origin ID. %s->getId() method does not exist.',
                get_class($origin)
            ));
        }

        $originAware
            ->setOriginId($origin->getId())
            ->setOriginType(get_class($origin))
        ;
    }
}
