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

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Object to identifier transformer.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ObjectToIdentifierTransformer implements ParameterTransformerInterface
{
    /**
     * Object repository.
     *
     * @var ObjectRepository
     */
    private $objectRepository;

    /**
     * Object identifier name.
     *
     * @var string
     */
    private $identifier;

    /**
     * Constructor.
     *
     * @param ObjectRepository $objectRepository
     * @param string           $identifier
     */
    public function __construct(ObjectRepository $objectRepository, $identifier = 'id')
    {
        $this->objectRepository = $objectRepository;
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

        return $this->objectRepository->findOneBy(array($this->identifier => $value));
    }
}
