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

$projectDir = $container->getParameter('kernel.project_dir');
$container->setParameter('kernel.api_bundle_path', str_replace('/Tests/Application', '', $projectDir));
