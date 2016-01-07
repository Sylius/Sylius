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
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ResourceAutocompleteToIdentifierTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $select;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->select = $options['select'];
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return [];
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        return array('select' => $accessor->getValue($value, $this->select), 'resource' => $value);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        return $value['resource'];
    }
}
