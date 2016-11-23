<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Webmozart\Assert\Assert;


/**
 * TODO: spec it!!
 *
 * @author Julien Janvier <j.janvier@lakion.com>
 */
final class ResourcesToIdentifiersTransformer implements DataTransformerInterface
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
     * @param string              $identifier
     */
    public function __construct(RepositoryInterface $repository, $identifier = 'id')
    {
        $this->repository = $repository;
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($resources)
    {
        if (null === $resources) {
            return [];
        }

        Assert::isInstanceOf($resources, Collection::class);
        Assert::allIsInstanceOf($resources, $this->repository->getClassName());

        $identifiers = [];
        foreach ($resources as $resource) {
            $identifiers[] = PropertyAccess::createPropertyAccessor()->getValue($resource, $this->identifier);
        }

        return $identifiers;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($identifiers)
    {
        Assert::nullOrIsArray($identifiers);

        if (empty($value)) {
            return new ArrayCollection();
        }

        Assert::allStringNotEmpty($identifiers);

        $resources = [];
        foreach ($identifiers as $identifier) {
            $resource = $this->repository->findOneBy([$this->identifier => $identifier]);
            if (null === $resource) {
                throw new TransformationFailedException(sprintf(
                    'Object "%s" with identifier "%s"="%s" does not exist.',
                    $this->repository->getClassName(),
                    $this->identifier,
                    $identifiers
                ));
            }

            $resources[] = $resource;
        }

        return $resources;
    }
}
