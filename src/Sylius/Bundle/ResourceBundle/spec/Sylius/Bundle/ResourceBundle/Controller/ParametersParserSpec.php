<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Arnaud Langade <arn0d.dev@gmail.com>
 */
class ParametersParserSpec extends ObjectBehavior
{
    function let(ExpressionLanguage $expression)
    {
        $this->beConstructedWith($expression);
    }

    function it_should_parse_parameters(Request $request)
    {
        $request->get('criteria')->willReturn('New criteria');
        $request->get('sorting')->willReturn('New sorting');

        $this->parse(
            array(
                'criteria' => '$criteria',
                'sortable' => '$sorting'
            ),
            $request
        )->shouldReturn(array(
            array(
                'criteria' => 'New criteria',
                'sortable' => 'New sorting',
            ),
            array(
                'criteria' => 'criteria',
                'sortable' => 'sorting',
            )
        ));
    }

    function it_should_parse_complex_parameters(Request $request)
    {
        $request->get('enable')->willReturn(true);
        $request->get('sorting')->willReturn('New sorting');

        $this->parse(
            array(
                'criteria' => array(
                    'enable' => '$enable'
                ),
                'sortable' => '$sorting'
            ),
            $request
        )->shouldReturn(array(
                array(
                    'criteria' => array(
                        'enable' => true,
                    ),
                    'sortable' => 'New sorting',
                ),
                array(
                    'criteria' => array(
                        'enable' => 'enable',
                    ),
                    'sortable' => 'sorting',
                )
            ));
    }
}
