<?php

namespace App\SearchBuilders;

use App\Models\Category;

class ProductSearchBuilder
{
    // 初始化查询
    protected $params = [
        'index' => 'products',
        'type'  => '_doc',
        'body'  => [
            'query' => [
                'bool' => [
                    'filter' => [],
                    'must'   => [],
                ]
            ]
        ]
    ];

    // 添加分页查询
    public function paginate($size, $page)
    {
        $this->params['body']['size'] = $size;
        $this->params['body']['from'] = ($page - 1) * $size;
        return $this;
    }

    public function onSale()
    {
        $this->params['body']['query']['bool']['filter'][] = ['term' => ['on_sale' => true]];
        return $this;
    }

    public function orderBy($field, $direction)
    {
        if (!isset($this->params['body']['sort'])) {
            $this->params['body']['sort'] = [];
        }
        $this->params['body']['sort'][] = [$field => $direction];
        return $this;
    }

    public function category(Category $category)
    {
        // 如果这是一个父类目
        if ($category->is_directory) {
            $this->params['body']['query']['bool']['filter'][] = [
                'prefix' => ['category_path' => $category->path . $category->id . '-']
            ];
        } else {
            $this->params['body']['query']['bool']['filter'][] = [
                'term' => ['category_id' => $category->id]
            ];
        }
        return $this;
    }

    public function keywords($keywords)
    {
        foreach ($keywords as $keyword) {
            $this->params['body']['query']['bool']['must'][] = [
                'multi_match' => [
                    'query'  => $keyword,
                    'type'   => 'best_fields',
                    'fields' => [
                        'title^3',
                        'long_title^2',
                        'category^2', // 类目名称
                        'description',
                        'skus_title',
                        'skus_description',
                        'properties_value',
                    ],
                ],
            ];
        }
        return $this;
    }

    // 分面搜索的聚合
    public function aggregateProperties()
    {
        $this->params['body']['aggs'] = [
            'properties' => [
                'nested' => [
                    'path' => 'properties',
                ],
                'aggs'   => [
                    'properties' => [
                        'terms' => [
                            'field' => 'properties.name',
                        ],
                        'aggs'  => [
                            'value' => [
                                'terms' => [
                                    'field' => 'properties.value',
                                ]
                            ]
                        ],
                    ],
                ],
            ],
        ];
        return $this;
    }

    // 添加一个按商品属性筛选的条件
    public function propertyFilter($name, $value, $type = 'filter')
    {
        $this->params['body']['query']['bool'][$type][] = [
            'nested' => [
                'path'  => 'properties',
                'query' => [
                    ['term' => ['properties.search_value' => $name . ':' . $value]],
                ],
            ],
        ];
        return $this;
    }

    // 设置 minimum_should_match 参数
    public function minShouldMatch($count)
    {
        return $this->params['body']['query']['bool']['minimum_should_match'] = (int)$count;
    }

    public function getParams()
    {
        return $this->params;
    }
}
