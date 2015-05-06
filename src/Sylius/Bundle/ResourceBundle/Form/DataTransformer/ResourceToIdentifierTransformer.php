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

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Resource to identifier transformer.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceToIdentifierTransformer implements DataTransformerInterface
{
    /**
     * Repository.
     *
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Identifier.
     *
     * @var string
     */
    protected $identifier;

    /**
     * Constructor.
     *
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
    public function transform($value)
    {
        if (!$value) {
            return null;
        }

        if (!array_key_exists('identifier', $value)) {
           throw new \InvalidArgumentException('Missing identifier.');
        }

        $identifier = $value['identifier'];

        if (null === $resource = $this->repository->findOneBy(array($this->identifier => $identifier))) {
            throw new TransformationFailedException(sprintf(
                'Resource "%s" with identifier "%s"="%s" does not exist.',
                $this->repository->getClassName(),
                $this->identifier,
                $identifier
            ));
        }

        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return array();
        }

        $class = $this->repository->getClassName();

        if (!$value instanceof $class) {
            throw new UnexpectedTypeException($value, $class);
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        return array(
            'identifier' => $accessor->getValue($value, $this->identifier),
            'input'      => 'Test'
        );
    }
}
