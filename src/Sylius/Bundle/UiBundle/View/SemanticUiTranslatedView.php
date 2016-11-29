<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UiBundle\View;

use WhiteOctober\PagerfantaBundle\View\TranslatedView;

/**
 * SemanticUiTranslatedView
 *
 * This view renders the semantic ui view with the text translated.
 */
class SemanticUiTranslatedView extends TranslatedView
{
    protected function previousMessageOption()
    {
        return 'prev_message';
    }

    protected function nextMessageOption()
    {
        return 'next_message';
    }

    protected function buildPreviousMessage($text)
    {
        return sprintf('&larr; %s', $text);
    }

    protected function buildNextMessage($text)
    {
        return sprintf('%s &rarr;', $text);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'semantic_ui_translated';
    }
}