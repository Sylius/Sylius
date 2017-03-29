<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\Form\Extension;

use Payum\Core\Security\CryptedInterface;
use Payum\Core\Security\CypherInterface;
use Sylius\Bundle\PayumBundle\Form\Type\GatewayConfigType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CryptedGatewayConfigTypeExtension extends AbstractTypeExtension
{
    /**
     * @var CypherInterface|null
     */
    private $cypher;

    /**
     * @param CypherInterface|null $cypher
     */
    public function __construct(CypherInterface $cypher = null)
    {
        $this->cypher = $cypher;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (null === $this->cypher) {
            return;
        }

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $gatewayConfig = $event->getData();

                if (!$gatewayConfig instanceof CryptedInterface) {
                    return;
                }

                $gatewayConfig->decrypt($this->cypher);

                $event->setData($gatewayConfig);
            })
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $gatewayConfig = $event->getData();

                if (!$gatewayConfig instanceof CryptedInterface) {
                    return;
                }

                $gatewayConfig->encrypt($this->cypher);

                $event->setData($gatewayConfig);
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return GatewayConfigType::class;
    }
}
