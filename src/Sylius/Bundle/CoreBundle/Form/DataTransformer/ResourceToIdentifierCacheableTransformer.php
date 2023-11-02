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

namespace Sylius\Bundle\CoreBundle\Form\DataTransformer;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Webmozart\Assert\Assert;

final class ResourceToIdentifierCacheableTransformer implements DataTransformerInterface
{
    private RepositoryInterface $repository;

    private string $identifier;

    private static array $cache;

    public function __construct(RepositoryInterface $repository, ?string $identifier = null)
    {
        $this->repository = $repository;
        $this->identifier = $identifier ?? 'id';
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

    /** @param int|string|null $value */
    public function reverseTransform($value): ?ResourceInterface
    {
        if (null === $value) {
            return null;
        }

        if (isset(self::$cache[$value])) {
            return self::$cache[$value];
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

        self::$cache[$value] = $resource;

        return $resource;
    }
}
