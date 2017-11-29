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

namespace spec\Sylius\Component\Addressing\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

final class CountrySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Country::class);
    }

    public function it_implements_Sylius_country_interface(): void
    {
        $this->shouldImplement(CountryInterface::class);
    }

    public function it_is_toggleable(): void
    {
        $this->shouldImplement(ToggleableInterface::class);
    }

    public function it_implements_code_aware_interface(): void
    {
        $this->shouldImplement(CodeAwareInterface::class);
    }

    public function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_returns_name_when_converted_to_string(): void
    {
        $this->setCode('VE');
        $this->__toString()->shouldReturn('Venezuela');
    }

    public function it_has_no_code_by_default(): void
    {
        $this->getCode()->shouldReturn(null);
    }

    public function its_code_is_mutable(): void
    {
        $this->setCode('MX');
        $this->getCode()->shouldReturn('MX');
    }

    public function it_initializes_provinces_collection_by_default(): void
    {
        $this->getProvinces()->shouldHaveType(Collection::class);
    }

    public function it_has_no_provinces_by_default(): void
    {
        $this->hasProvinces()->shouldReturn(false);
    }

    public function it_adds_province(ProvinceInterface $province): void
    {
        $this->addProvince($province);
        $this->hasProvince($province)->shouldReturn(true);
    }

    public function it_removes_province(ProvinceInterface $province): void
    {
        $this->addProvince($province);
        $this->hasProvince($province)->shouldReturn(true);

        $this->removeProvince($province);
        $this->hasProvince($province)->shouldReturn(false);
    }

    public function it_sets_country_on_added_province(ProvinceInterface $province): void
    {
        $province->setCountry($this)->shouldBeCalled();
        $this->addProvince($province);
    }

    public function it_unsets_country_on_removed_province(ProvinceInterface $province): void
    {
        $this->addProvince($province);
        $this->hasProvince($province)->shouldReturn(true);

        $province->setCountry(null)->shouldBeCalled();

        $this->removeProvince($province);
    }

    public function it_is_enabled_by_default(): void
    {
        $this->isEnabled()->shouldReturn(true);
    }

    public function it_can_be_disabled(): void
    {
        $this->disable();
        $this->isEnabled()->shouldReturn(false);
    }

    public function it_can_be_enabled(): void
    {
        $this->disable();
        $this->isEnabled()->shouldReturn(false);

        $this->enable();
        $this->isEnabled()->shouldReturn(true);
    }

    public function it_can_set_enabled_value(): void
    {
        $this->setEnabled(false);
        $this->isEnabled()->shouldReturn(false);

        $this->setEnabled(true);
        $this->isEnabled()->shouldReturn(true);

        $this->setEnabled(false);
        $this->isEnabled()->shouldReturn(false);
    }
}
