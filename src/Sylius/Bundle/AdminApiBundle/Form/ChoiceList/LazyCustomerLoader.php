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

namespace Sylius\Bundle\AdminApiBundle\Form\ChoiceList;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

class LazyCustomerLoader implements ChoiceLoaderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * LazyCustomerLoader constructor.
     * @param RepositoryInterface $customerRepository
     */
    public function __construct(RepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceList($value = null)
    {
        return new ArrayChoiceList([], $value);
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        $self = $this;

        $mapEmailToCustomer = function($email) use ($self){
            return $self->customerRepository->findOneBy(['email' => $email]);
        };

        return array_map($mapEmailToCustomer, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        $mapCustomerToEmail = function($customer){
            if($customer == null){
                return null;
            }

            return $customer->getEmail();
        };

        return array_map($mapCustomerToEmail, $choices);
    }
}