<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bridge\Mailer\Adapter;

abstract class AbstractAdapter
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param string $templateName
     * @param array  $context
     *
     * @return array
     */
    protected function parseTemplate($templateName, $context)
    {
        $context  = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($templateName);

        return array(
            $template->renderBlock('subject', $context),
            $template->renderBlock('body_text', $context),
            $template->renderBlock('body_html', $context),
        );
    }
}
