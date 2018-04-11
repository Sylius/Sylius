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

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\AddressType;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Resolver\ShippableCountriesResolverInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class AddressTypeExtension extends AbstractTypeExtension
{
    /**
     * @var ShippableCountriesResolverInterface
     */
    private $shippableCountriesResolver;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    public function __construct(ShippableCountriesResolverInterface $countriesResolver, ChannelContextInterface $channelContext)
    {
        $this->shippableCountriesResolver = $countriesResolver;
        $this->channelContext = $channelContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('countryCode', ChoiceType::class, [
                'label' => 'sylius.form.address.country',
                'choices' => $this->shippableCountriesResolver->getShippableCountries($this->channelContext->getChannel()),
                'placeholder' => 'sylius.form.address.select',
            ]);
    }

    public function getExtendedType(): string
    {
        return AddressType::class;
    }
}
