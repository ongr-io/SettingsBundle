# ONGR Settings Bundle

Settings Bundle provides settings API and simple user interface for setting management.

What's inside:

* [Global settings](Resources/doc/general_settings.md) with restricted access (for features on/off).
* [Personal settings](Resources/doc/personal_settings.md) for every user, that only he can change (cookie based).
* API for every setting control.
* Simple and minimal web interface.
* Multi type settings: scalar, boolean, array, object.
* Cache layer for global settings.

This bundle is independent from ONGR platform and can be used in your project on its own with few dependencies.


[![Build Status](https://travis-ci.org/ongr-io/SettingsBundle.svg?branch=master)](https://travis-ci.org/ongr-io/SettingsBundle)
[![Coverage Status](https://coveralls.io/repos/ongr-io/SettingsBundle/badge.svg?branch=master&service=github)](https://coveralls.io/github/ongr-io/SettingsBundle?branch=master)
[![Latest Stable Version](https://poser.pugx.org/ongr/settings-bundle/v/stable)](https://packagist.org/packages/ongr/settings-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ongr-io/SettingsBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ongr-io/SettingsBundle/?branch=master)


## Documentation

The online documentation of the bundle is in [Github](Resources/doc/index.md).


## Installation

### Step 1: Install Settings bundle

FilterManager bundle is installed using [Composer](https://getcomposer.org).

```bash
$ composer require ongr/settings-bundle "~0.1"
```

> Please note that settings bundle requires Elasticsearch bundle, guide on how to install and configure it can be found [here](https://github.com/ongr-io/ElasticsearchBundle).

### Step 2: Enable ONGR bundles

Register Settings bundle and all other dependencies (`FOSJsRoutingBundle`, `TedivmStashBundle`, `ONGRCookiesBundle`, `ONGRElasticsearchBundle`, `ONGRFilterManagerBundle`) in your AppKernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
        new Tedivm\StashBundle\TedivmStashBundle(),
        new ONGR\CookiesBundle\ONGRCookiesBundle(),
        new ONGR\ElasticsearchBundle\ONGRElasticsearchBundle(),
        new ONGR\FilterManagerBundle\ONGRFilterManagerBundle(),
        new ONGR\SettingsBundle\ONGRSettingsBundle(),
        // ...
    );

    // ...
}
```

### Step 3: Add configuration

Add minimal configuration for Elasticsearch bundle. With these configs all settings will be saved to separate Elasticsearch index named `settings`.

```yaml
# app/config/config.yml

ongr_elasticsearch:
    connections:
        settings:
            index_name: settings
            settings:
                number_of_shards: 2
                number_of_replicas: 0
    managers:
        settings:
            connection: settings
            mappings:
                - ONGRSettingsBundle
```
> If you already using ONGR Elasticsearch bundle, merge your current configuration with this one: add `settings` connection to your current list of connections and add `settings` manager to current list of managers. [Read more about ElasticsearchBundle configuration](https://github.com/ongr-io/ElasticsearchBundle/blob/master/Resources/doc/configuration.md).

Also add routing configuration for settings API and web interface.

```yaml
# app/config/routing.yml

fos_js_routing:
    resource: "@FOSJsRoutingBundle/Resources/config/routing/routing.xml"

ongr_settings_routing:
    resource: "@ONGRSettingsBundle/Resources/config/routing.yml"
    prefix: /settings_prefix
```

`settings_prefix` here will be the part of urls. For example general settings panel will be at `/{settings_prefix}/general_settings_list` and personal settings panel at `{settings_prefix}/settings`.


### Step 4: Create index and install assets

Create an Elasticsearch index by running this command in your terminal:

```bash

    app/console ongr:es:index:create --manager=settings

```

> More info about all commands can be found in the [Elasticsearch bundle commands chapter](https://github.com/ongr-io/ElasticsearchBundle/blob/master/Resources/doc/commands.md).

We strongly recommend to have a separate index for your settings (as in example above) so your business data won't mix up with settings.

Web interface won't work without assets. Install them by using this command in your terminal:

```bash

    app/console assets:install

```


### Step 5: Check how it works

Yes, you can check how it works, but it is NOT ready for production yet. At this moment all settings management is public. **Check [how to secure access to settings](Resources/doc/ongr_sessionless_authentication.md)**.

Visit `/{settings_prefix}/general_settings_list`, where `{settings_prefix}` is a prefix you defined at *Step 3*. You should see admin panel of general settings.

> Screenshot???

Read more about [General settings](Resources/doc/general_settings.md) to find out how to use these settings in your application.

## License

This bundle is covered by the MIT license. Please see the complete license in the bundle [LICENSE](LICENSE) file.
