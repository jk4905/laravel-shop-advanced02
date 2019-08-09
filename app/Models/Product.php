<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Product extends Model
{
    const TYPE_NORMAL = 'normal';
    const TYPE_CROWDFUNDING = 'crowdfunding';
    public static $typeMap = [
        self::TYPE_NORMAL       => '普通商品',
        self::TYPE_CROWDFUNDING => '众筹商品',
    ];

    protected $fillable = [
        'type',
        'title',
        'long_title',
        'description',
        'image',
        'on_sale',
        'rating',
        'sold_count',
        'review_count',
        'price'
    ];
    protected $casts = [
        'on_sale' => 'boolean', // on_sale 是一个布尔类型的字段
    ];

    // 与商品SKU关联
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function properties()
    {
        return $this->hasMany(ProductProperty::class);
    }

    public function getImageUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://'])) {
            return $this->attributes['image'];
        }
        return \Storage::disk('public')->url($this->attributes['image']);
    }

    public function crowdfunding()
    {
        return $this->hasOne(CrowdfundingProduct::class);
    }

    public function getGroupedPropertiesAttribute()
    {
        return $this->properties
            // 按照属性名聚合，返回的集合的 key 是属性名，value 是包含该属性名的所有属性集合
            ->groupBy('name')->map(function ($properties) {
                // 使用 map 方法将属性集合变为属性值集合
                return $properties->pluck('value')->all();
            });
    }

    public function scopeByIds($query, $ids)
    {
        return $query->whereIn('id', $ids)->orderByRaw(sprintf("FIND_IN_SET(id, '%s')", join(',', $ids)));
    }

    public function toESArray()
    {
        // 只取出需要的字段
        $arr = Arr::only($this->toArray(), [
            'id',
            'type',
            'title',
            'category_id',
            'long_title',
            'on_sale',
            'rating',
            'sold_count',
            'review_count',
            'price',
        ]);


        // 如果商品有类目，则 category 字段为类目名数组，否则为空字符串
        $arr['category'] = $this->category ? explode('-', $this->category->full_name) : '';
        // 类目的 path 字段
        $arr['category_path'] = $this->category ? $this->category->path : '';
        // strip_tags 函数可以将 html 标签去除
        $arr['description'] = strip_tags($this->description);
        // 只取出需要的 SKU 字段
        $arr['skus'] = $this->skus->map(function (ProductSku $sku) {
            return Arr::only($sku->toArray(), ['title', 'description', 'price']);
        });
        // 只取出需要的商品属性字段
        $arr['properties'] = $this->properties->map(function (ProductProperty $property) {
            return array_merge(Arr::only($property->toArray(), ['name', 'value']), [
                'search_value' => $property->name . ':' . $property->value,
            ]);
        });

        return $arr;
    }

    public function demo()
    {
        $params = [
            'index' => 'products',
            'type'  => '_doc',
            'body'  => [
                'from'  => 0, // 分页-页数
                'size'  => 5, // 分页-每页多少个
                'query' => [
                    'bool' => [ // Elasticsearch 布尔查询
                                'filter' => [ // 与 SQL 中的 and 类似, 表示必须过滤, 与 must 一样, 但是不打分. 如果有多个,每个条件都是一个单独的数组,而不是写一起!!!
                                              ['term' => ['on_sale' => true]],
                                              // term 表示这是一个[词项查询],用于搜索一个精确的值
                                              ['prefix' => ['category_path' => '-10-']],
                                              // prefix 表示这是一个[前缀查询],用于搜索以一个值为开头的文档
                                ],
                                'must'   => [
                                    [
                                        'multi_match' => [ // 多项匹配
                                                           'query'  => 'iPhone', // 关键词
                                                           'fields' => [ // 字段
                                                                         'title^3',
                                                                         // ^ 后的数字代表权重, 数字越高权重越大, 最后匹配出来的得分也越高
                                                                         'long_title^2',
                                                                         'description'
                                                           ]
                                        ]
                                    ]
                                ]
                    ],
                ],
                'sort'  => [ // 排序
                             ['price' => 'desc'], // 根据 price 进行倒序排列
                             ['id' => 'desc'] // 如果有多项, 也是单独一个数组
                ],
            ],
        ];
        $result = app('es')->search($params);
        count($result['hits']['hits']);  // $result['hits']['hits'] 表示命中的文档
        $result['hits']['total']; // 总共命中多少个
        collect($result['hits']['hits'])->pluck('_source.price');
        collect($result['hits']['hits'])->pluck('_source.id');
    }

    public function demo2()
    {
        $params = [
            'index' => 'products',
            'type'  => '_doc',
            'body'  => [
                'from'  => 0,
                'size'  => 5,
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['on_sale' => true]],
                        ],
                        'must'   => [
                            'multi_match' => [
                                'query'  => '256G',
                                'fields' => [
                                    // 'skus.title', 直接查询无法命中
                                    // 'skus.description', 直接查询无法命中
                                    // 'properties.value', 直接查询无法命中

                                    'skus_title', // 需要在索引中添加 copy_to 才能命中
                                    'skus_description',
                                    'properties_value',
                                ],
                            ]
                        ]
                    ],
                ]
            ],
        ];
        app('es')->search($params);
    }

    public function demo3()
    {
        $params = [
            'index' => 'products',
            'type'  => '_doc',
            'body'  => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['on_sale' => true]],
                        ],
                        'must'   => [
                            [
                                'multi_match' => [
                                    'query'  => '内存条',
                                    'type'   => 'best_fields',
                                    //对于multi-field的best_fields模式来说，相当于是对每个字段对查询分别进行打分，然后执行max运算获取打分最高的。
                                    'fields' => [
                                        'title^3',
                                        'long_title^2',
                                        'category^2',
                                        'description',
                                        'skus_title',
                                        'skus_description',
                                        'properties_value',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs'  => [  // 聚合的键名, 也可以用 aggregations
                              'one_properties' => [ // 这个是给聚合操作的命名, 可以是任意的
                                                    'nested' => [ // 由于我们要聚合的属性是在 nested 类型字段下的属性，需要在外面套一层 nested 聚合查询
                                                                  'path' => 'properties',
                                                                  // 代表我们需要查询的 nested 字段名为 properties
                                                    ],
                                                    'aggs'   => [ // 在 nested 下嵌套一层聚合, 将第一层下所有的 properties 聚合
                                                                  'two_properties' => [ // 第二层聚合操作名, 任意
                                                                                        'terms' => [ // terms 聚合,聚合相同的值
                                                                                                     'field' => 'properties.name',
                                                                                                     // 我们需要聚合的字段名
                                                                                        ],
                                                                                        'aggs'  => [ // 第三层聚合,聚合同一个属性名的所有值
                                                                                                     'value' => [
                                                                                                         'terms' => [
                                                                                                             'field' => 'properties.value'
                                                                                                             // 聚合属性值
                                                                                                         ]
                                                                                                     ]
                                                                                        ]
                                                                  ],
                                                    ],
                              ]
                ],
            ],
        ];
        app('es')->search($params);


        $params = [
            'index' => 'products',
            'type'  => '_doc',
            'body'  => [
                'query'        => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['on_sale' => true]],
                        ],
                        'must'   => [
                            [
                                'multi_match' => [
                                    'query'  => '内存条',
                                    'fields' => [
                                        'title^3',
                                        'long_title^2',
                                        'category^2',
                                        'description',
                                        'skus_title',
                                        'skus_description',
                                        'properties_value',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'aggregations' => [
                    'properties' => [
                        'nested' => [
                            'path' => 'properties',
                        ],
                    ]
                ],
            ],
        ];

    }


}
