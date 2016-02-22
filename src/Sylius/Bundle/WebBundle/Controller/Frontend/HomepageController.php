<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Frontend homepage controller.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class HomepageController extends Controller
{
    /**
     * Store front page.
     *
     * @return Response
     */
    public function mainAction()
    {
        $zone = $this->get('sylius.repository.zone')->findAll()[0];

        $manager = $this->get('sylius.settings.manager');
        $settings = $manager->load('sylius_taxation');

        echo '<pre>';
        echo $settings['default_tax_zone']->getId();


        $settings['default_tax_zone'] = null;


        $manager->save($settings);

        die;


        return $this->render('SyliusWebBundle:Frontend/Homepage:main.html.twig');
    }
}
