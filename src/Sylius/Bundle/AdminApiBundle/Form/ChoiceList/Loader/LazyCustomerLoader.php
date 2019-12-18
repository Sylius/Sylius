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

namespace Sylius\Bundle\AdminApiBundle\Form\ChoiceList\Loader;

use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

final class LazyCustomerLoader implements ChoiceLoaderInterface
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function loadChoiceList($value = null): ChoiceListInterface
    {
        return new ArrayChoiceList([], $value);
    }

    public function loadChoicesForValues(array $values, $value = null): array
    {
        return $this->customerRepository->findBy(['email' => $values]);
    }

    public function loadValuesForChoices(array $choices, $value = null): array
    {
        /** Intentionally left blank, as in the only usage of this loader is in the context of api, where we don't need to load choices */
        return [];
    }
}
