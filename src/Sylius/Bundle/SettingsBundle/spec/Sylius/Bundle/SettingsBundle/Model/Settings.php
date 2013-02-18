<?php

namespace spec\Sylius\Bundle\SettingsBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Settings model spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Settings extends ObjectBehavior
{
    function let()
    {
        $parameters = array(
            'title'      => 'Sylius, Symfony2 ecommerce',
            'percentage' => 12,
            'page'       => 1,
            'zone'       => new \stdClass()
        );

        $this->beConstructedWith($parameters);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Model\Settings');
    }

    function it_should_implement_Sylius_settings_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SettingsBundle\Model\SettingsInterface');
    }

    function it_should_return_parameter_by_name()
    {
        $this->get('page')->shouldReturn(1);
    }

    function it_should_set_parameter_by_name()
    {
        $this->set('limit', 50);
        $this->geT('limit')->shouldReturn(50);
    }

    function it_should_overwrite_parameter()
    {
        $this->set('page', 12);
        $this->get('page')->shouldReturn(12);
    }

    function it_should_check_for_parameter_existence_by_name()
    {
        $this->has('zone')->shouldReturn(true);
        $this->has('cache')->shouldReturn(false);
    }

    function it_should_implement_array_access_interface()
    {
        $this->shouldImplement('ArrayAccess');
    }

    function it_should_allow_to_get_parameters_via_array_access()
    {
        $this['page']->shouldReturn(1);
    }

    function it_should_allow_to_set_parameters_via_array_access()
    {
        $this['page'] = 10;

        $this['page']->shouldReturn(10);
        $this->get('page')->shouldReturn(10);
    }

    function it_should_allow_to_unset_parameters_via_array_access()
    {
        unset($this['title']);
        $this->has('title')->shouldReturn(false);
    }
}
