# 快速流畅的 PHP API 构建和执行 ElasticSearch 查询



这个包是 ElasticSearch 的*轻量级*查询构建器。它是专门为我们的[elasticsearch-search-string-parser](https://github.com/spatie/elasticsearch-search-string-parser)构建的，因此它涵盖了大多数使用方法，但会缺少部分某些功能。如果您需要任何特定的东西，也可以进行补充，这一点不胜荣幸。

## 安装

请确保您安装好composer

```bash
composer require savingfor/elasticsearch-query-builder
```



## 基本用法

您真正需要与之交互的唯一类是`Savingfor\ElasticsearchQueryBuilder\Builder`该类。它需要`\Elasticsearch\Client`在构造函数中传递引用。通过 [ElasticSearch SDK文档](https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/installation.html) 了解更多PHP-ElasticSearch更多信息。

```php
use Elasticsearch\ClientBuilder;
use Savingfor\ElasticsearchQueryBuilder\Aggregations\TermsAggregation;

// 您的es连接配置
$host[] = [
    "host" => "127.0.0.1",
    "port" => "9200",
    "user" => "elastic",
    "pass" => "elastic"
];

// 创建连接客户端
$client = ClientBuilder::create()
    ->setHosts($host)
    ->setConnectionPool('\Elasticsearch\ConnectionPool\SimpleConnectionPool', [])
    ->setRetries(10)->build();

$builder = new Builder($client);

// 生成查询quuery
$builder->index("test")
    ->addQuery(
        TermQuery::create("day", "2022-02-22")
    );

// 通过调用$builder->search()来执行查询
$result = $builder->search();

```



## 新增搜索查询

该`$builder->addQuery()`方法可用于将任何可用`Query`类型添加到构建器。可用的查询类型可以在下面或`src/Queries`此 repo 的目录中找到。每个`Query`都有一个静态`create()`方法来传递其最重要的参数。

可以使用以下查询类型：

#### TermQuery

[官方文档使用详情](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html)

```php
\Savingfor\ElasticsearchQueryBuilder\Queries\TermQuery::create('age', '18');
```

#### RangeQuery

[官方文档使用详情](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html)

```php
\Savingfor\ElasticsearchQueryBuilder\Queries\RangeQuery::create('age')
    ->gte(18)
    ->lte(28);
```

#### BoolQuery

[官方文档使用详情](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html)

```php
\Savingfor\ElasticsearchQueryBuilder\Queries\BoolQuery::create()
    ->add($matchQuery, 'must_not')
    ->add($existsQuery, 'must_not');

```

#### ExistsQuery

[官方文档使用详情](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-exists-query.html)

```php
\Savingfor\ElasticsearchQueryBuilder\Queries\ExistsQuery::create('conditions');
```

#### MatchQuery

[官方文档使用详情](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html)

```php
\Savingfor\ElasticsearchQueryBuilder\Queries\MatchQuery::create('name', 'john saving', fuzziness: 2);
```

#### MultiMatchQuery

[官方文档使用详情](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-multi-match-query.html)

```php
\Savingfor\ElasticsearchQueryBuilder\Queries\MultiMatchQuery::create('john', ['email', 'email'], fuzziness: 'auto');
```

#### NestedQuery

[官方文档使用详情](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-nested-query.html)

```php
\Savingfor\ElasticsearchQueryBuilder\Queries\NestedQuery::create(
    'user', 
    new \Savingfor\ElasticsearchQueryBuilder\Queries\MatchQuery('name', 'john')
);
```

#### WildcardQuery

[官方文档使用详情](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html)

```php
\Savingfor\ElasticsearchQueryBuilder\Queries\WildcardQuery::create('user.id', '*doe');
```



##  多个搜索查询

多个`addQuery()`调用可以链接在一个上`Builder`。多次调用中，它们将被添加到`BoolQuery `中。通过将第二个参数传递给该`addQuery()`方法，您可以选择不同的出现类型：

```php
$builder->index("test")
    ->addQuery(
        TermQuery::create("name", "john"),
  			"must_not"  // 可用类型: must, must_not, should, filter
    )
    ->addQuery(
  		RangeQuery::create("money")->gte("20")
    );
```

有关布尔查询及其出现类型的更多信息，[请参阅Elasticsearch文档](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html)



## 聚合查询

`$builder->addAggregation()`方法可用于将任何可用`Aggregation` 添加到构建器。可用的聚合类型可以在下面或`src/Aggregations`此 repo 的目录中找到。每个`Aggregation`都有一个静态`create()`方法来传递其最重要的参数，有时还有一些额外的方法。

```php
use Savingfor\ElasticsearchQueryBuilder\Aggregations\TermsAggregation;
use Savingfor\ElasticsearchQueryBuilder\Builder;

$client = ClientBuilder::create()
    ->setHosts($host)
    ->setConnectionPool('\Elasticsearch\ConnectionPool\SimpleConnectionPool', [])
    ->setRetries(10)->build();

$builder = new Builder($client);

$results = $builder
    ->addAggregation(TermsAggregation::create('my_agg', 'age'))
    ->search();

$genres = $results['aggregations']['my_agg']['buckets'];
```

### 可以使用以下聚合查询类型：

#### TermsAggregation

[官方文档使用详情](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-terms-aggregation.html)

```php
\Savingfor\ElasticsearchQueryBuilder\Aggregations\TermsAggregation::create(
    'genres',
    'genre'
)
    ->size(10)
    ->order(['_count' => 'asc']) // _count按文档数排序;_term按词项的字符串值的字母顺序排序
    ->missing('N/A')
    ->aggregation(/* $subAggregation */);
```

#### CardinalityAggregation

[官方文档使用详情](https://www.elastic.co/guide/en/elasticsearch/reference/8.0/search-aggregations-metrics-cardinality-aggregation.html)

```php
\Savingfor\ElasticsearchQueryBuilder\Aggregations\CardinalityAggregation::create('team_agg', 'team_name');
```

#### FilterAggregation

[官方文档使用详情](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-filter-aggregation.html)

```php
\Savingfor\ElasticsearchQueryBuilder\Aggregations\FilterAggregation::create(
    'tshirts',
    \Savingfor\ElasticsearchQueryBuilder\Queries\TermQuery::create('type', 'tshirt'),
    \Savingfor\ElasticsearchQueryBuilder\Aggregations\MaxAggregation::create('max_price', 'price')
);
```

#### MaxAggregation

[官方文档使用详情](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-max-aggregation.html)

```php
\Savingfor\ElasticsearchQueryBuilder\Aggregations\MinAggregation::create('min_price', 'price');
```

#### MinAggregation

[官方文档使用详情](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-min-aggregation.html)

```php
\Savingfor\ElasticsearchQueryBuilder\Aggregations\MinAggregation::create('min_price', 'price');
```

#### ReverseNestedAggregation

[官方文档使用详情](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-reverse-nested-aggregation.html)

```php
\Savingfor\ElasticsearchQueryBuilder\Aggregations\ReverseNestedAggregation::create(
    'name',
    ...$aggregations
);

```

#### TopHitsAggregation

[官方文档使用详情](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-top-hits-aggregation.html)

```php
\Savingfor\ElasticsearchQueryBuilder\Aggregations\TopHitsAggregation::create(
    'top_sales_hits',
    size: 10,
);
```



## 添加排序

（`Builder`和一些聚合查询）有一个`addSort()`方法，它需要一个`Sort`实例来对结果进行排序。[您可以在ElasticSearch 文档](https://www.elastic.co/guide/en/elasticsearch/reference/current/sort-search-results.html)中阅读有关排序工作原理的更多信息。

```php
use Savingfor\ElasticsearchQueryBuilder\Sorts\Sort;

$builder
    ->addSort(Sort::create('age', Sort::DESC))
    ->addSort(
        Sort::create('score', Sort::ASC)
            ->unmappedType('long')
            ->missing(0)
    );
```

## 返回特定字段

该`fields()`方法可用于从结果文档中请求特定字段，而无需返回整个`_source`条目。[您可以在ElasticSearch 文档](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-fields.html)中阅读有关 fields 参数细节的更多信息。

```php
$builder->fields('user.id', 'http.*.status');
```

## 分页

最后`Builder`还提供了相应的 ElasticSearch 搜索参数的方法`size()`。`from()`这些可用于构建分页搜索。看看下面的例子来大致了解一下：

```php
use Savingfor\ElasticsearchQueryBuilder\Builder;

$pageSize = 100;
$pageNumber = $_GET['page'] ?? 1;

$pageResults = (new Builder(\Elasticsearch\ClientBuilder::create()))
    ->size($pageSize)
    ->from(($pageNumber - 1) * $pageSize)
    ->search();
```



## License

MIT

