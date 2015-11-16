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

use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ResourceToIdentifierTransformer implements DataTransformerInterface
{
    /**
     * @var ResourceRepositoryInterface
     */
    protected $repository;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @param ResourceRepositoryInterface $repository
     * @param string $identifier
     */
    public function __construct(ResourceRepositoryInterface $repository, $identifier = 'id')
    {
        $this->repository = $repository;
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return '';
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($value, $this->identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        if (null === $resource = $this->repository->findOneBy(array($this->identifier => $value))) {
            throw new TransformationFailedException(sprintf(
                'Object with identifier "%s"="%s" does not exist.',
                $this->identifier,
                $value
            ));
        }

        return $resource;
    }
}
