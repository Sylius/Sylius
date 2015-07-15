<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\LocaleBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class LocaleProviderSpec extends ObjectBehavior
{
    public function let(RepositoryInterface $localeRepository)
    {
        $this->beConstructedWith($localeRepository, 'fr_FR');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\LocaleBundle\Provider\LocaleProvider');
    }

    public function it_is_a_locale_provider()
    {
        $this->shouldImplement('Sylius\Bundle\LocaleBundle\Provider\LocaleProviderInterface');
    }
}
