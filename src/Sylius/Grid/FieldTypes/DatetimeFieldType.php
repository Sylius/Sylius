<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Grid\FieldTypes;

use Sylius\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Grid\Definition\Field;
use Webmozart\Assert\Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DatetimeFieldType implements FieldTypeInterface
{
    /**
     * @var DataExtractorInterface
     */
    private $dataExtractor;

    /**
     * @param DataExtractorInterface $dataExtractor
     */
    public function __construct(DataExtractorInterface $dataExtractor)
    {
        $this->dataExtractor = $dataExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function render(Field $field, $data, array $options)
    {
        $value = $this->dataExtractor->get($field, $data);
        if (null === $value) {
            return null;
        }

        Assert::isInstanceOf($value, \DateTime::class);

        return $value->format($options['format']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'format' => 'Y:m:d H:i:s'
        ]);
        $resolver->setAllowedTypes([
            'format' => 'string'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'datetime';
    }
}
