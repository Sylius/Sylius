<?php

namespace spec\Sylius\Bundle\ApiBundle\ApiPlatform;

use PhpSpec\ObjectBehavior;

final class ApiResourceConfigurationMergerSpec extends ObjectBehavior
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

    public function it_allows_to_unset_and_redeclare_endpoint(): void
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
                'admin_post (unset)' => null,
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

    public function it_allows_to_merge_unsigned_endpoints(): void
    {
        $this->mergeConfigs(
            [
                [
                    'method' => 'GET',
                    'path' => 'admin/path/{tokenValue}'
                ],
                [
                    'method' => 'POST',
                    'path' => 'admin/path-old'
                ]
            ],
            [
                [
                    'method' => 'POST',
                    'path' => 'admin/path-new'
                ]
            ]
        )->shouldReturn(
            [
                [
                    'method' => 'GET',
                    'path' => 'admin/path/{tokenValue}'
                ],
                [
                    'method' => 'POST',
                    'path' => 'admin/path-old'
                ],
                [
                    'method' => 'POST',
                    'path' => 'admin/path-new'
                ]
            ]
        );
    }

    public function it_merges_non_array_configs(): void
    {
        $this->mergeConfigs(
            [
                'test_config_one' => 'test_value_one'
            ],
            [
                'test_config_two' => 'test_value_two'
            ]
        )->shouldReturn(
            [
                'test_config_one' => 'test_value_one',
                'test_config_two' => 'test_value_two'
            ]
        );
    }
}
