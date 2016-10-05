<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Transformer;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ResourceToIdentifierTransformer implements ParameterTransformerInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @param RepositoryInterface $repository
     * @param string $identifier
     */
    public function __construct(RepositoryInterface $repository, $identifier = 'id')
    {
        $this->repository = $repository;
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (!is_object($value)) {
            return null;
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($value, $this->identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        if ('id' === $this->identifier) {
            return $this->repository->find($value);
        }

        return $this->repository->findOneBy([$this->identifier => $value]);
    }
}
