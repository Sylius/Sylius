<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\DataTransformer;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Webmozart\Assert\Assert;

/**
 * @see \Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer
 * @implements DataTransformerInterface<ResourceInterface, int|string|ResourceInterface>
 */
final readonly class ResourceToIdentifierTransformer implements DataTransformerInterface
{
    /** @phpstan-ignore-next-line */
    public function __construct(
        private RepositoryInterface $repository,
        private string $identifier = 'id',
    ) {
    }

    /**
     * @psalm-suppress MissingParamType
     *
     * @param object|null $value
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        Assert::isInstanceOf($value, $this->repository->getClassName());

        return PropertyAccess::createPropertyAccessor()->getValue($value, $this->identifier);
    }

    /** @param int|string|ResourceInterface|null $value */
    public function reverseTransform($value): ?ResourceInterface
    {
        if (null === $value) {
            return null;
        }
        // Early return in case we're already dealing with a resource
        if ($value instanceof ResourceInterface) {
            return $value;
        }

        /** @var ResourceInterface|null $resource */
        $resource = $this->repository->findOneBy([$this->identifier => $value]);
        if (null === $resource) {
            throw new TransformationFailedException(sprintf(
                'Object "%s" with identifier "%s"="%s" does not exist.',
                $this->repository->getClassName(),
                $this->identifier,
                $value,
            ));
        }

        return $resource;
    }
}
