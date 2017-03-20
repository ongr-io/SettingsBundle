<?php

namespace ONGR\SettingsBundle\Tests\Unit\Filter;

use ONGR\ElasticsearchDSL\Search;
use ONGR\SettingsBundle\Filter\SkipHiddenSettingsFilter;

class SkipHiddenSettingsFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testModifySearch()
    {
        $expected = [
            'query' => [
                'bool' => [
                    'must_not' => [
                        [
                            'term' => [
                                'type' => 'hidden',
                            ],
                        ],
                        [
                            'term' => [
                                'type' => 'experiment',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $filter = new SkipHiddenSettingsFilter();
        $search = new Search();
        $filter->modifySearch($search);

        $this->assertEquals($expected, $search->toArray());
    }
}
