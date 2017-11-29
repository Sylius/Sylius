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

namespace Sylius\Bundle\CoreBundle\Fixture\OptionsResolver;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Webmozart\Assert\Assert;

/**
 * Using the hacky hack to distinct between option which wasn't set
 * and option which was set to empty.
 *
 * Usage:
 *
 *   $optionsResolver
 *     ->setDefault('option', LazyOption::randomOne($repository))
 *     ->setNormalizer('option', LazyOption::findOneBy($repository, 'code'))
 *   ;
 *
 *   Returns:
 *     - null if user explicitly set it (['option' => null])
 *     - random one if user skipped that option ([])
 *     - specific one if user defined that option (['option' => 'CODE'])
 */
final class LazyOption
{
    public static function randomOne(RepositoryInterface $repository): \Closure
    {
        return function (Options $options) use ($repository) {
            $objects = $repository->findAll();

            if ($objects instanceof Collection) {
                $objects = $objects->toArray();
            }

            Assert::notEmpty($objects);

            return $objects[array_rand($objects)];
        };
    }

    public static function randomOneOrNull(RepositoryInterface $repository, int $chanceOfRandomOne): \Closure
    {
        return function (Options $options) use ($repository, $chanceOfRandomOne) {
            if (random_int(1, 100) > $chanceOfRandomOne) {
                return null;
            }

            $objects = $repository->findAll();

            if ($objects instanceof Collection) {
                $objects = $objects->toArray();
            }

            return 0 === count($objects) ? null : $objects[array_rand($objects)];
        };
    }

    public static function randomOnes(RepositoryInterface $repository, int $amount): \Closure
    {
        return function (Options $options) use ($repository, $amount) {
            $objects = $repository->findAll();

            if ($objects instanceof Collection) {
                $objects = $objects->toArray();
            }

            $selectedObjects = [];
            for (; $amount > 0 && count($objects) > 0; --$amount) {
                $randomKey = array_rand($objects);

                $selectedObjects[] = $objects[$randomKey];

                unset($objects[$randomKey]);
            }

            return $selectedObjects;
        };
    }

    public static function all(RepositoryInterface $repository): \Closure
    {
        return function (Options $options) use ($repository) {
            return $repository->findAll();
        };
    }

    public static function findBy(RepositoryInterface $repository, string $field): \Closure
    {
        return function (Options $options, $previousValues) use ($repository, $field) {
            if (null === $previousValues || [] === $previousValues) {
                return $previousValues;
            }

            Assert::isArray($previousValues);

            $resources = [];
            foreach ($previousValues as $previousValue) {
                if (is_object($previousValue)) {
                    $resources[] = $previousValue;
                } else {
                    $resources[] = $repository->findOneBy([$field => $previousValue]);
                }
            }

            return $resources;
        };
    }

    public static function findOneBy(RepositoryInterface $repository, string $field): \Closure
    {
        return function (Options $options, $previousValue) use ($repository, $field) {
            if (null === $previousValue || [] === $previousValue) {
                return $previousValue;
            }

            if (is_object($previousValue)) {
                return $previousValue;
            }

            return $repository->findOneBy([$field => $previousValue]);
        };
    }
}
