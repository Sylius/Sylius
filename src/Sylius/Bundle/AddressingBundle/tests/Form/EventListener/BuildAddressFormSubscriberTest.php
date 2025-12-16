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

namespace Tests\Sylius\Bundle\AddressingBundle\Form\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AddressingBundle\Form\EventListener\BuildAddressFormSubscriber;
use Sylius\Bundle\AddressingBundle\Form\Type\ProvinceCodeChoiceType;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class BuildAddressFormSubscriberTest extends TestCase
{
    /** @var RepositoryInterface<CountryInterface>&MockObject */
    private MockObject&RepositoryInterface $countryRepository;

    private FormFactoryInterface&MockObject $formFactory;

    private BuildAddressFormSubscriber $buildAddressFormSubscriber;

    protected function setUp(): void
    {
        $this->countryRepository = $this->createMock(RepositoryInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->buildAddressFormSubscriber = new BuildAddressFormSubscriber($this->countryRepository, $this->formFactory);
    }

    public function testImplementsAnEventSubscriber(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->buildAddressFormSubscriber);
    }

    public function testSubscribesToEvent(): void
    {
        $this->assertSame([
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ], BuildAddressFormSubscriber::getSubscribedEvents());
    }

    public function testAddsProvincesOnPreSetData(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var FormInterface&MockObject $form */
        $form = $this->createMock(FormInterface::class);
        /** @var FormInterface&MockObject $provinceForm */
        $provinceForm = $this->createMock(FormInterface::class);
        /** @var AddressInterface&MockObject $address */
        $address = $this->createMock(AddressInterface::class);
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);

        $event->expects($this->once())->method('getData')->willReturn($address);
        $event->expects($this->once())->method('getForm')->willReturn($form);
        $address->expects($this->once())->method('getCountryCode')->willReturn('IE');
        $address->expects($this->once())->method('getProvinceCode')->willReturn('province');
        $this->countryRepository->expects($this->once())->method('findOneBy')->with(['code' => 'IE'])->willReturn($country);
        $country->expects($this->once())->method('hasProvinces')->willReturn(true);
        $this->formFactory
            ->expects($this->once())
            ->method('createNamed')
            ->with('provinceCode', ProvinceCodeChoiceType::class, 'province', $this->callback(fn (array $options) => is_array($options) &&
                isset($options['country']) &&
                $options['country'] === $country))
            ->willReturn($provinceForm)
        ;
        $form->expects($this->once())->method('add')->with($provinceForm)->willReturn($form);

        $this->buildAddressFormSubscriber->preSetData($event);
    }

    public function testAddsProvinceNameFieldOnPreSetDataIfCountryDoesNotHaveProvinces(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var FormInterface&MockObject $form */
        $form = $this->createMock(FormInterface::class);
        /** @var FormInterface&MockObject $provinceForm */
        $provinceForm = $this->createMock(FormInterface::class);
        /** @var AddressInterface&MockObject $address */
        $address = $this->createMock(AddressInterface::class);
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);

        $event->expects($this->once())->method('getData')->willReturn($address);
        $event->expects($this->once())->method('getForm')->willReturn($form);
        $address->expects($this->once())->method('getCountryCode')->willReturn('US');
        $address->expects($this->once())->method('getProvinceName')->willReturn('Utah');
        $this->countryRepository->expects($this->once())->method('findOneBy')->with(['code' => 'US'])->willReturn($country);
        $country->expects($this->once())->method('hasProvinces')->willReturn(false);
        $this->formFactory->expects($this->once())->method('createNamed')->willReturn($provinceForm);
        $form->expects($this->once())->method('add')->with($provinceForm)->willReturn($form);

        $this->buildAddressFormSubscriber->preSetData($event);
    }

    public function testAddsProvincesOnPreSubmit(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var FormInterface&MockObject $form */
        $form = $this->createMock(FormInterface::class);
        /** @var FormInterface&MockObject $provinceForm */
        $provinceForm = $this->createMock(FormInterface::class);
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);

        $event->expects($this->once())->method('getForm')->willReturn($form);
        $event->expects($this->once())->method('getData')->willReturn(['countryCode' => 'FR']);
        $this->countryRepository->expects($this->once())->method('findOneBy')->with(['code' => 'FR'])->willReturn($country);
        $country->expects($this->once())->method('hasProvinces')->willReturn(true);
        $this->formFactory
            ->expects($this->once())
            ->method('createNamed')
            ->with('provinceCode', ProvinceCodeChoiceType::class, null, $this->callback(fn (array $options) => is_array($options) &&
                isset($options['country']) &&
                $options['country'] === $country))
            ->willReturn($provinceForm)
        ;
        $form->expects($this->once())->method('add')->with($provinceForm)->willReturn($form);

        $this->buildAddressFormSubscriber->preSubmit($event);
    }

    public function testAddsProvinceNameFieldOnPreSubmitIfCountryDoesNotHaveProvinces(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var FormInterface&MockObject $form */
        $form = $this->createMock(FormInterface::class);
        /** @var FormInterface&MockObject $provinceForm */
        $provinceForm = $this->createMock(FormInterface::class);
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);

        $event->expects($this->once())->method('getData')->willReturn(['countryCode' => 'US']);
        $event->expects($this->once())->method('getForm')->willReturn($form);
        $this->countryRepository->expects($this->once())->method('findOneBy')->with(['code' => 'US'])->willReturn($country);
        $country->expects($this->once())->method('hasProvinces')->willReturn(false);
        $this->formFactory->expects($this->once())->method('createNamed')->willReturn($provinceForm);
        $form->expects($this->once())->method('add')->with($provinceForm)->willReturn($form);

        $this->buildAddressFormSubscriber->preSubmit($event);
    }
}
