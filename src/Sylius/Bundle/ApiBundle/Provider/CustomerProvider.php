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

namespace Sylius\Bundle\ApiBundle\Provider;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;

final class CustomerProvider implements CustomerProviderInterface
{
    /** @var CanonicalizerInterface */
    private $canonicalizer;

    /** @var FactoryInterface */
    private $customerFactory;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    public function __construct(
        CanonicalizerInterface $canonicalizer,
        FactoryInterface $customerFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->canonicalizer = $canonicalizer;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
    }

    public function provide(string $email): CustomerInterface
    {
        $emailCanonical = $this->canonicalizer->canonicalize($email);

        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['emailCanonical' => $emailCanonical]);

        if ($customer === null) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($email);
        }

        return $customer;
    }
}
