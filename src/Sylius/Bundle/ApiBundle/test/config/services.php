<?php

$projectDir = $container->getParameter('kernel.project_dir');
$container->setParameter('kernel.api_bundle_path', str_replace('/test', '', $projectDir));
