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

namespace Sylius\Bundle\ApiBundle\Serializer;

use Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/** @experimental */
final class AddressDenormalizer implements ContextAwareDenormalizerInterface
{
    /** @var DenormalizerInterface */
    private $objectNormalizer;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var CustomerProviderInterface */
    private $customerProvider;

    /** @var string */
    private $classType;

    /** @var string */
    private $interfaceType;

    public function __construct(
        DenormalizerInterface $objectNormalizer,
        TokenStorageInterface $tokenStorage,
        CustomerProviderInterface $customerProvider,
        string $classType,
        string $interfaceType
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->customerProvider = $customerProvider;
        $this->objectNormalizer = $objectNormalizer;
        $this->classType = $classType;
        $this->interfaceType = $interfaceType;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        /** @var AddressInterface $address */
        $address = $this->objectNormalizer->denormalize(
            $data,
            $this->classType,
            $format,
            $context
        );

        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            throw new TokenNotFoundException();
        }

        /** @var UserInterface $loggedUser */
        $loggedUser = $token->getUser();

        if ($loggedUser instanceof UserInterface) {
            $customer = $this->customerProvider->provide($loggedUser->getEmail());

            $address->setCustomer($customer);

            $customer->setDefaultAddress($address);
        }

        return $address;
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        return $type === $this->classType;
    }
}
