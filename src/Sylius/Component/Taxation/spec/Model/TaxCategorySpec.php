<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Taxation\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class TaxCategorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Taxation\Model\TaxCategory');
    }

    function it_should_implement_Sylius_tax_category_interface()
    {
        $this->shouldImplement(TaxCategoryInterface::class);
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_mutable_code()
    {
        $this->setCode('TC1');
        $this->getCode()->shouldReturn('TC1');
    }

    function it_should_be_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_should_be_mutable()
    {
        $this->setName('Taxable goods');
        $this->getName()->shouldReturn('Taxable goods');
    }

    function it_should_not_have_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_should_be_mutable()
    {
        $this->setDescription('All taxable goods');
        $this->getDescription()->shouldReturn('All taxable goods');
    }

    function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType(\DateTime::class);
    }

    function it_should_not_have_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
