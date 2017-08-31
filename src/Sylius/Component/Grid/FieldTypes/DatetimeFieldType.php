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

namespace Sylius\Component\Grid\FieldTypes;

use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class DatetimeFieldType implements FieldTypeInterface
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
     *
     * @throws \InvalidArgumentException
     */
    public function render(Field $field, $data, array $options)
    {
        $value = $this->dataExtractor->get($field, $data);
        if (null === $value) {
            return null;
        }

        Assert::isInstanceOf($value, \DateTimeInterface::class);

        return $value->format($options['format']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('format', 'Y:m:d H:i:s');
        $resolver->setAllowedTypes('format', 'string');
    }
}
