<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle\Twig;


/**
 * Twig extension for displaying microtime and difference between two microtimes
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class MicrotimeExtension extends \Twig_Extension
{

    /**
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('microtime_date', array($this, 'microtimeDateFilter')),
            new \Twig_SimpleFilter('microtime_exec_time', array($this, 'microtimeExecTimeFilter')),
        );
    }

    /**
     * Returns microtime in a human readable format
     *
     * @param $microtime
     *
     * @return null|string
     */
    public function microtimeDateFilter($microtime)
    {
        if (is_null($microtime)) {
            return null;
        }

        $date = date("Y-m-d H:i:s", $microtime);

        return "$date";
    }

    /**
     * Returns duration
     *
     * @param $microtimeStart
     * @param $microtimeEnd
     *
     * @return string
     */
    public function microtimeExecTimeFilter($microtimeStart, $microtimeEnd)
    {
        if (is_null($microtimeStart) || is_null($microtimeEnd)) {
            return '0:0';
        }

        $duration = date('i:s', $microtimeEnd - $microtimeStart);

        return "$duration";
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return 'microtime_extension';
    }
} 