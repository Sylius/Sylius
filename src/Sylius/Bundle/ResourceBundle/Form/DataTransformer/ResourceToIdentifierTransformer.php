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

namespace Sylius\Bundle\ResourceBundle\Form\DataTransformer;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Webmozart\Assert\Assert;

final class ResourceToIdentifierTransformer implements DataTransformerInterface
{
    /** @var RepositoryInterface */
    private $repository;

    /** @var string */
    private $identifier;

    /**
     * @param string $identifier
     */
    public function __construct(RepositoryInterface $repository, ?string $identifier = null)
    {
        $this->repository = $repository;
        $this->identifier = $identifier ?? 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        Assert::isInstanceOf($value, $this->repository->getClassName());

        return PropertyAccess::createPropertyAccessor()->getValue($value, $this->identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        $resource = $this->repository->findOneBy([$this->identifier => $value]);
        if (null === $resource) {
            throw new TransformationFailedException(sprintf(
                'Object "%s" with identifier "%s"="%s" does not exist.',
                $this->repository->getClassName(),
                $this->identifier,
                $value
            ));
        }

        return $resource;
    }
}
