<?php

namespace spec\Sylius\Bundle\ApiBundle\ApiPlatform;

use PhpSpec\ObjectBehavior;

class ConfigMergeManagerSpec extends ObjectBehavior
{
    public function it_merges_configs(): void
    {
        $this->mergeConfigs(
            [
                'admin_get' => [
                    'method' => 'GET',
                    'path' => 'admin/path/{tokenValue}'
                ],
                'admin_post' => [
                    'method' => 'POST',
                    'path' => 'admin/path'
                ]
            ],
            [
                'admin_patch' => [
                    'method' => 'PATCH',
                    'path' => 'admin/path/patch'
                ],
            ]
        )->shouldReturn(
            [
                'admin_get' => [
                    'method' => 'GET',
                    'path' => 'admin/path/{tokenValue}'
                ],
                'admin_post' => [
                    'method' => 'POST',
                    'path' => 'admin/path'
                ],
                'admin_patch' => [
                    'method' => 'PATCH',
                    'path' => 'admin/path/patch'
                ],
            ]
        );
    }

    public function it_removes_config_with_unset_keyword(): void
    {
        $this->mergeConfigs(
            [
                'admin_get' => [
                    'method' => 'GET',
                    'path' => 'admin/path/{tokenValue}'
                ],
                'admin_post' => [
                    'method' => 'POST',
                    'path' => 'admin/path'
                ]
            ],
            [
                'admin_get (unset)' => null
            ]
        )->shouldReturn(
            [
                'admin_post' => [
                    'method' => 'POST',
                    'path' => 'admin/path'
                ]
            ]
        );
    }

    public function it_overwrites_parent_config(): void
    {
        $this->mergeConfigs(
            [
                'admin_get' => [
                    'method' => 'GET',
                    'path' => 'admin/path/{tokenValue}'
                ],
                'admin_post' => [
                    'method' => 'POST',
                    'path' => 'admin/path-old'
                ]
            ],
            [
                'admin_post' => [
                    'method' => 'POST',
                    'path' => 'admin/path-new'
                ]
            ]
        )->shouldReturn(
            [
                'admin_get' => [
                    'method' => 'GET',
                    'path' => 'admin/path/{tokenValue}'
                ],
                'admin_post' => [
                    'method' => 'POST',
                    'path' => 'admin/path-new'
                ]
            ]
        );
    }
}
