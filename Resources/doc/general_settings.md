# General settings

This component provides functionality to configure website in real time and provide easily configurable setting management and output.


## Configuration

General settings can be managed in two ways:
 * API. Check [API specifications](api.md).
 * Web UI. Visit (`/{settings_prefix}/general_settings_list`).


## Usage

Once setting is created, it can be used in services or templates directly.

### Templates

You can easily access setting value in any template using `ongr_show_setting_value` twig function. It returns the value of setting. If requested setting is not defined, function returns null.

For example. Imagine that you have two settings `our_email` and `is_public`.

Then this snippet will show setting `our_email` only if `is_public` is `true.`

```twig

  {% if ongr_show_setting_value('is_public') %}
      <p>Contact us {{ ongr_show_setting_value('our_email') }}</p>
  {% endif %}

```

For easy settings management it's possible to enable setting management UI in front end:

```twig

    <div>{{ ongr_show_setting_widget('our_email') }}</div>
```

This will provide you with button link to setting administration screen.
To see this button you need to log in as Admin and enable `ongr_settings_live_settings` user setting.
After this button appears just click on it and you will be redirected to edit page where you can set or update value of the setting.


### Injecting Settings to Services

Settings injecting is super simple. To inject setting you only need to create setter method and add `ongr_settings.setting_aware` tag to service declaration:

```yaml
# app/config/services.yml

services:
    ongr_settings.demo_service:
        class: AppBundle\Service\MyService
        tags:
            - { name: 'ongr_settings.setting_aware', setting: 'count_per_page' }

```
> In this case `MyService` class has to have `setCountPerPage($setting)` setter.

Settings bundle tries to guess setter name by transforming setting name to camel case. If you want to specify custom setter name, add tag attribute `method`.

```yaml
# app/config/services.yml

services:
    ongr_settings.demo_service:
        class: AppBundle\Service\MyService
        tags:
            - { name: 'ongr_settings.setting_aware', setting: 'count_per_page', method: setPageSize }

```
> In this case `MyService` class has to have `setPageSize($setting)` setter.

Current setting value will be passed to setter method at the service creation moment.

What happens in background? Actual service will be replaced with proxy service using service factory. Factory service gets actual service as parameter and on demand injects tagged settings.


## Settings Cache

Settings bundle uses [`StashBundle`](https://github.com/tedious/TedivmStashBundle) to cache settings.
By default `FileSystem` cache driver is used. To ensure best performance change it to `Memcache`
or other fast cache engine.

```yaml
# app/config/config.yml

stash:
    drivers: [ Memcache, FileSystem ]
    Memcache: ~
    FileSystem: ~

```

Cache entry will be invalidated on every setting change.
