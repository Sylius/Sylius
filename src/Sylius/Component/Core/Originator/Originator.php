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
use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Originator implements OriginatorInterface
{
    protected $em;
    protected $accessor;
    protected $identifier;

    public function __construct(EntityManagerInterface $em, $identifier = 'id')
    {
        $this->em = $em;
        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->identifier = $identifier;
    }

    public function getOrigin(OriginAwareInterface $originAware)
    {
        if (null === $originAware->getOriginId() || null === $originAware->getOriginType()) {
            return null;
        }

        return $this->em
            ->getRepository($originAware->getOriginType())
            ->findOneBy(array(
                $this->identifier => $originAware->getOriginId()
            ))
        ;
    }

    public function setOrigin(OriginAwareInterface $originAware, $origin)
    {
        if (!is_object($origin)) {
            throw new UnexpectedTypeException($origin, 'object');
        }

        if (null === $id = $this->accessor->getValue($origin, $this->identifier)) {
            throw new \InvalidArgumentException(sprintf(
                'Origin %s is not set.',
                $this->identifier
            ));
        }

        $originAware
            ->setOriginId($id)
            ->setOriginType(get_class($origin))
        ;
    }
}
