# SitemapBundle

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This Bundle provides a way to create a xml sitemap using any source
you want (Doctrine, Propel, MongoDB, Faker, etc.).

This bundle aims to generate standards compliant sitemaps. For more information
about sitemaps go to [sitemaps.org](http://www.sitemaps.org/).

The sitemap generation part is handled by the [SitemapGenerator](https://github.com/sitemap-php/SitemapGenerator)
library, this bundle eases its integration into a Symfony2 application.

## Main features

  * static sitemap generation
  * dynamic sitemap generation
  * sitemap index generation
  * memory efficient
  * datasource independent
  * support for media content (currently images and videos)

***

## Install

Via Composer

``` bash
$ composer require larapulse/sitemap-bundle
```

Register the `SitemapBundle` in `app/AppKernel.php`:

```php
# app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // ...
        new Larapulse\SitemapBundle\SitemapBundle(),
    ];
}
```

***

## Configuration

Add the following options to `app/config/config.yml` file:

```yaml
larapulse_sitemap:
    base_host:         http://www.foo.com
    base_host_sitemap: http://www.foo.com
    limit:             50000
```

**Note:**

* The `base_host` will be prepended to relative urls added to the sitemap.
* The `base_host_sitemap` will be prepended to the sitemap filename (used for sitemap index)
* The `limit` is the number of url allowed in the same sitemap, if defined it will create a sitemap index

#### Routing

If you don't want to use the console to generate the sitemap, import the
routes:

```yaml
larapulse_sitemap:
    resource: "@SitemapBundle/Resources/config/routing.yml"
```

This will make the sitemap available from the `/sitemap.xml` URL.

***

## Usage

Add this line `/web/sitemap.xml*` to your `.gitignore` to prevent tracking `sitemap.xml` files by version control system.

### Providers

In order to support any kind of datasource, the sitemap uses providers to fetch
the data.

Exemple provider:

```php
<?php

namespace SitemapGenerator\Provider;

use SitemapGenerator\Entity\Url;
use SitemapGenerator\Provider\ProviderInterface;
use SitemapGenerator\Sitemap\Sitemap;

class CustomProvider implements ProviderInterface
{
    public function populate(Sitemap $sitemap)
    {
        $url = new Url();
        $url->setLoc('http://www.google.de');
        $url->setChangefreq(Url::CHANGEFREQ_NEVER);
        $url->setLastmod('2012-12-19 02:28');
        $sitemap->add($url);
    }
}
```

All the providers implement the `ProviderInterface`, which define the
`populate()` method.

**Note**: so they can be automatically used by the sitemap, providers have to be
described in the DIC with the `sitemap.provider` tag:

```yaml
services:
    sitemap_custom_provider:
        class: SitemapGenerator\Provider\CustomProvider
        tags:
            -  { name: sitemap.provider }
```

All the services tagged as `sitemap.provider` will be used to generate the
sitemap.

#### Simple provider

A provider to add static routes into the sitemap easily.

```yaml
parameters:
    sitemap.simple_options:
        routes:
            - {name: homepage}
            - name: foo
              params: {foo: bar}
              lastmod: '2017-11-23'
              changefreq: monthly
              priority: 0.5
        # the following parameters are optionnal
        lastmod:        '2015-01-01'
        changefreq:     never
        priority:       0.2

services:
    sitemap_simple_provider:
        class:      SitemapGenerator\Provider\SimpleProvider
        arguments:  [ @router, %sitemap.simple_options% ]
        tags:
            -  { name: sitemap.provider }
```


#### Propel provider

A propel provider is included in the bundle. It allows to populate a sitemap
with the content of a table.

Here is how you would configure the provider:

```yaml
# app/config/parameters.yml
parameters:
    sitemap.propel_options:
        model:      ACME\DemoBundle\Model\News
        # /news/{id}
        loc:        {route: news_show, params: {id: slug}}
        # the following parameters are optionnal
        filters:    ['filterByIsValid']
        lastmod:    date
        changefreq: daily
        priority:   0.2

# app/config/services.yml
services:
    sitemap_propel_provider:
        class:      SitemapGenerator\Provider\PropelProvider
        arguments:  
            - "@router"
            - "%sitemap.propel_options%"
        tags:
            -  { name: sitemap.provider }
```


#### Doctrine provider

A doctrine provider is included in the bundle. It allows to populate a sitemap
with the content of a table.

Here is how you would configure the provider:

```yaml
# app/config/parameters.yml
parameters:
    sitemap.doctrine_options:
        entity:         AcmeDemoBundle:News
        # /news/{id}
        loc:            {route: news_show, params: {id: slug}}
        # the following parameters are optionnal
        query_method:   findValidQuery
        lastmod:        updatedAt
        changefreq:     daily
        priority:       0.2

# app/config/services.yml
services:
    sitemap_doctrine_provider:
        class:      SitemapGenerator\Provider\DoctrineProvider
        arguments:
            - "@doctrine.orm.entity_manager"
            - "@router"
            - "%sitemap.doctrine_options%"
        tags:
            -  { name: sitemap.provider }
```

***

## Change log

Please see [CHANGELOG](docs/CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](docs/CONTRIBUTING.md) and [CODE_OF_CONDUCT](docs/CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email :author_email instead of using the issue tracker.

## Credits

- [Sergey Podgornyy][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
This project was forked from [sitemap-php/KPhoenSitemapBundle](https://github.com/sitemap-php/KPhoenSitemapBundle/)

[ico-version]: https://img.shields.io/packagist/v/larapulse/sitemap-bundle.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/larapulse/sitemap-bundle/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/larapulse/sitemap-bundle.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/larapulse/sitemap-bundle.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/larapulse/sitemap-bundle.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/larapulse/sitemap-bundle
[link-travis]: https://travis-ci.org/larapulse/sitemap-bundle
[link-scrutinizer]: https://scrutinizer-ci.com/g/larapulse/sitemap-bundle/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/larapulse/sitemap-bundle
[link-downloads]: https://packagist.org/packages/larapulse/sitemap-bundle
[link-author]: https://github.com/SergeyPodgornyy
[link-contributors]: ../../contributors
