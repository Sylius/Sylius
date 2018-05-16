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

namespace Sylius\Bundle\GridBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Webmozart\Assert\Assert;

final class DateTimeFilterTransformer implements DataTransformerInterface
{
    private static $defaultTime = [
        'from' => ['hour' => '00', 'minute' => '00'],
        'to' => ['hour' => '23', 'minute' => '59'],
    ];

    /** @var string */
    private $type;

    public function __construct(string $type)
    {
        Assert::oneOf($type, array_keys(static::$defaultTime));

        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value): array
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value): array
    {
        if (!$value['date']['year']) {
            return $value;
        }

        $value['time']['hour'] = $value['time']['hour'] === '' ? static::$defaultTime[$this->type]['hour'] : $value['time']['hour'];
        $value['time']['minute'] = $value['time']['minute'] === '' ? static::$defaultTime[$this->type]['minute'] : $value['time']['minute'];

        return $value;
    }
}
