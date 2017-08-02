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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class StringFieldType implements FieldTypeInterface
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

        return is_string($value) ? htmlspecialchars($value) : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
