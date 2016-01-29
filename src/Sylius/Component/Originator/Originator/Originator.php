<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Originator\Originator;

use Doctrine\Common\Persistence\ManagerRegistry;
use Sylius\Component\Originator\Model\OriginAwareInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Originator implements OriginatorInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $manager;

    /**
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * @var string
     */
    protected $identifier;

    public function __construct(ManagerRegistry $manager, $identifier = 'id')
    {
        $this->manager = $manager;
        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrigin(OriginAwareInterface $originAware)
    {
        if (null === $originAware->getOriginId() || null === $originAware->getOriginType()) {
            return null;
        }

        return $this->manager
            ->getRepository($originAware->getOriginType())
            ->findOneBy([
                $this->identifier => $originAware->getOriginId(),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setOrigin(OriginAwareInterface $originAware, $origin)
    {
        if (!is_object($origin)) {
            throw new UnexpectedTypeException($origin, 'object');
        }

        if (null === $id = $this->accessor->getValue($origin, $this->identifier)) {
            throw new \InvalidArgumentException(sprintf('Origin %s is not set.', $this->identifier));
        }

        $originAware->setOriginId($id);
        $originAware->setOriginType(get_class($origin));
    }
}
