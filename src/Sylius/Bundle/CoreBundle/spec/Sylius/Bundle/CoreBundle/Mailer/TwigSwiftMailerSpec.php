<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Mailer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TwigSwiftMailerSpec extends ObjectBehavior
{
    /**
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $twigEnvironment
     */
    function let($mailer, $twigEnvironment)
    {
        $this->beConstructedWith($mailer, $twigEnvironment);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Mailer\TwigSwiftMailer');
    }

    function it_implements_Sylius_Twig_mailer_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Mailer\TwigMailerInterface');
    }

    /**
     * @param \Twig_Template $template
     */
    function it_prepares_email_properly($mailer, $twigEnvironment, $template)
    {
        $from = 'test-email@sylius.org';
        $to = 'test-recipient@sylius.org';
        $templateName = 'test-template';
        $context = array('dummy', 'context');

        $template->renderBlock('subject', $context)->shouldBeCalled();
        $template->renderBlock('body_text', $context)->shouldBeCalled();
        $template->renderBlock('body_html', $context)->shouldBeCalled();

        $twigEnvironment->mergeGlobals($context)->shouldBeCalled()->willReturn($context);
        $twigEnvironment->loadTemplate($templateName)->willReturn($template);

        $mailer->send(Argument::any())->shouldBeCalled();

        $this->sendEmail($templateName, $context, $from, $to);
    }
}