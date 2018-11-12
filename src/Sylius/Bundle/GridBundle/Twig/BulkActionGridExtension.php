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

namespace Sylius\Bundle\GridBundle\Twig;

use Sylius\Bundle\GridBundle\Templating\Helper\BulkActionGridHelper;

final class BulkActionGridExtension extends \Twig_Extension
{
    /**
     * @var BulkActionGridHelper
     */
    private $bulkActionGridHelper;

    public function __construct(BulkActionGridHelper $bulkActionGridHelper)
    {
        $this->bulkActionGridHelper = $bulkActionGridHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_Function(
                'sylius_grid_render_bulk_action',
                [$this->bulkActionGridHelper, 'renderBulkAction'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
