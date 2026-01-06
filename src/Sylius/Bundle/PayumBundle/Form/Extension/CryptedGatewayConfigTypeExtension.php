<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\Form\Extension;

use Payum\Core\Security\CypherInterface;
use Sylius\Bundle\PaymentBundle\Form\Type\GatewayConfigType;
use Sylius\Bundle\PayumBundle\Checker\PayumGatewayConfigEncryptionCheckerInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class CryptedGatewayConfigTypeExtension extends AbstractTypeExtension
{
    public function __construct(
        private readonly PayumGatewayConfigEncryptionCheckerInterface $encryptionChecker,
        private ?CypherInterface $cypher = null,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (null === $this->cypher) {
            return;
        }

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $gatewayConfig = $event->getData();

                if (!$this->encryptionChecker->isPayumEncryptionEnabled($gatewayConfig)) {
                    return;
                }

                $gatewayConfig->decrypt($this->cypher);

                $event->setData($gatewayConfig);
            })
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $gatewayConfig = $event->getData();

                if (!$this->encryptionChecker->isPayumEncryptionEnabled($gatewayConfig)) {
                    return;
                }

                $gatewayConfig->encrypt($this->cypher);

                $event->setData($gatewayConfig);
            })
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [GatewayConfigType::class];
    }
}
