# ONGR Settings Bundle

Settings Bundle provides settings API and simple user interface for setting management.

What's inside:

* Settings features toggle (for features on/off).
* Personal settings for every user, that only he can change (cookie based).
* API for every setting control.
* Simple and minimal web interface.
* Multi type settings: yaml, boolean, text.
* Cache layer for global settings.

This bundle is independent from ONGR platform and can be used in your project on its own with few dependencies.

If you need any help, [stack overflow](http://stackoverflow.com/questions/tagged/ongr)
is the preferred and recommended way to ask questions about ONGR bundles and libraries.

[![Build Status](https://travis-ci.org/ongr-io/SettingsBundle.svg?branch=master)](https://travis-ci.org/ongr-io/SettingsBundle)
[![Coverage Status](https://coveralls.io/repos/ongr-io/SettingsBundle/badge.svg?branch=master&service=github)](https://coveralls.io/github/ongr-io/SettingsBundle?branch=master)
[![Latest Stable Version](https://poser.pugx.org/ongr/settings-bundle/v/stable)](https://packagist.org/packages/ongr/settings-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ongr-io/SettingsBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ongr-io/SettingsBundle/?branch=master)

## Documentation

The online documentation of the bundle can be found in [http://docs.ongr.io](http://docs.ongr.io/SettingsBundle).
Docs source is stored within the repo under `Resources/doc/`, so if you see a typo or problem, please submit a PR to fix it!

For contribution to the documentation you can find it in the [contribute](http://docs.ongr.io/common/Contributing) topic.



## Installation

### Step 1: Install Settings bundle

FilterManager bundle is installed using [Composer](https://getcomposer.org).

```bash
$ composer require ongr/settings-bundle "~1.0"
```

> Please note that settings bundle requires Elasticsearch bundle, guide on how to install and configure it can be found [here](https://github.com/ongr-io/ElasticsearchBundle).

### Step 2: Enable ONGR bundles

Register Settings bundle and all required dependencies in your AppKernel:

```php
// app/AppKernel.php
<?php

public function registerBundles()
{
    $bundles = array(
        // ...
        new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
        new JMS\SerializerBundle\JMSSerializerBundle(),
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
    managers:
        settings:
            index:
                index_name: settings
                settings:
                    number_of_shards: 2
                    number_of_replicas: 0
            mappings:
                - ONGRSettingsBundle
```
> If you already using ONGR Elasticsearch bundle, merge your current configuration with this one: add `settings` connection to your current list of connections and add `settings` manager to current list of managers. [Read more about ElasticsearchBundle configuration](https://github.com/ongr-io/ElasticsearchBundle/blob/master/Resources/doc/configuration.md).

Also add routing configuration for settings API and web interface.

```yaml
#app/config/routing.yml
    
# FOS JS bundle route map, if you have it already skip this one.
fos_js_routing:
    resource: "@FOSJsRoutingBundle/Resources/config/routing/routing.xml"
    
# ONGR settings management route map. You might want to secure it under some firewall.
ongr_settings_private_routing:
    resource: "@ONGRSettingsBundle/Resources/config/routing/management.yml"
    prefix: /settings
    
# Public routes for ONGR settings. Do not add them to the firewall unless you know what you are doing.
ongr_settings_public_routing:
    resource: "@ONGRSettingsBundle/Resources/config/routing/public.yml"
    prefix: /s
```

Under `/settings` there will be all the parts of urls for settings management. For example general settings panel will be at `/settings/general_settings_list` and personal settings panel at `/settings/settings`.
You can change map prefix as you like.

Under `/s/` in public route map there will be a route for settings profile enabling by link. This is a landing page to enable or disable profile for the user. It's up to you to secure this prefix or not. You can map both resources under the same prefix, there will never be the same routes in both of them.

### Step 4: Create index and install assets

Create an Elasticsearch index by running this command in your terminal:

```bash

    bin/console ongr:es:index:create -m settings

```

> More info about all commands can be found in the [Elasticsearch bundle commands chapter](https://github.com/ongr-io/ElasticsearchBundle/blob/master/Resources/doc/commands.md).

We strongly recommend to have a separate index for your settings (as in example above) so your business data won't mix up with settings.

Web interface won't work without assets. Install them by using this command in your terminal:

```bash

    bin/console assets:install --symlink

```


### Step 5: Check how it works

Yes, you can check how it works, but it is NOT ready for production yet. At this moment all settings management is public. Don't forget to setup firewall and protect `your_settings_prefix` endpoint. 

Visit `/{your_settings_prefix}/settings`. You should see admin panel of general settings.


## Usage

### Global settings.

Visit `/{your_management_settings_prefix}/settings` and create settings you want to use in your application.
There is a selection of profiles when you create a setting. After you create a setting and want to use it in website you have to activate a profile (see profile section).
 
There is a Twig extension to use settings in the templates.
 
 ```
 {{ ongr_setting('setting_name', 'default value') }}
 ```
 
 e.g. if you want to have features toggle you can create bool setting and use extension in `if` statement:
 
 ```
 {% if ongr_setting('setting_name') %}
    
    show something cool
    
 {% endif %}
 ```
 
### Profiles
 
 Profiles are like grouped settings where you can enable or disable all of them at once. The setting can be assigned for multiple profiles.
 You can find profiles management page at `/your_management_settings_prefix/profiles`.
  
 To create a profile simply create your first setting with a new profile and it will appear in the profiles page.

## License

This bundle is covered by the MIT license. Please see the complete license in the bundle [LICENSE](LICENSE) file.
