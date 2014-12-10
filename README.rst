===========
AdminBundle
===========

Provides settings API and admin interface for ONGR projects.

.. image:: https://magnum.travis-ci.com/ongr-io/AdminBundle.svg?token=X35UxnxC4zoxXhsTMzw8&branch=master
    :target: https://magnum.travis-ci.com/ongr-io/AdminBundle

It includes:

- Admin settings functionality:
- Common settings functionality;
- FlashBag functionality:
- Using environment variables functionality:

Depends on:

- `ONGR/CookiesBundle <https://github.com/ongr-io/CookiesBundle>`_
- `ONGR/ElasticsearchBundle <https://github.com/ongr-io/ElasticsearchBundle>`_
- `ONGR/ContentBundle <https://github.com/ongr-io/ContentBundle>`_
- `ONGR/FilterManagerBundle <https://github.com/ongr-io/FilterManagerBundle>`_

Installation
~~~~~~~~~~~~

`Instalation documentation </Resources/doc/install.rst>`_

Usage
~~~~~

`Usage examples </Resources/doc/examples.rst>`_


### Setting it up

`AdminBundle` requires minimal efforts to get it working. Firstly, install package using Composer:

.. code-block:: bash

composer require ongr-io/AdminBundle 0.1.*

..

Then register it in `AppKernel.php`:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new ONGR\AdminBundle\ONGRAdminBundle(),
        );
    }

    // ...
}
```

Enabling Authentication support:

## Users

To enable authentication support, please add this to your main `routing.yml`

```yaml
_power_user:
    resource: "@FoxUtilsBundle/Resources/config/routing_authentication.yml"
    prefix: /power_user_prefix
```

Then add some users to you `config.yml` parameters section:

```yaml
parameters:
    fox_utils.authentication.users:
        foo_user:
            password: 'foo_password'
        foo_user_bar:
            password: 'foo_bar_password'
```

Login page is at `/power_user_prefix/login`. There is also a logout page at `/power_user_prefix/logout`.

Some auth cookie properties:

* Login credentials are stored in a signed tamper-proof authentication cookie that is **valid for X hours** (see fox-utils configuration).
* Authentication cookie's signature **contains username**, **IP address**, expiration **timestamp** and **password**. Therefore if any of the values change, then cookie becomes invalid.

    Values can change in several places. Eg. IP address is dependent on the network, password can change in the configuration file and the expiration timestamp or the username can be modified in the cookie itself.
* Cookie **can be stolen** if sent over *http://*, so do not trust it's security absolutely.




## Enabling Admin settings (PowerUser) functionality:

##Settings

Settings can be changed per user from the settings page and the selected values are stored in a separate cookie.

To enable a user to edit it's settings, add a route:

```yaml
_power_settings:
    resource: "@FoxUtilsBundle/Resources/config/routing_settings.yml"
    prefix: /power_settings_prefix
```

And add some settings that are grouped in categories:

```yaml
parameters:
    fox_utils.settings.settings:
        foo_setting_1:
            name: Foo Setting 1
            category: category_1
            description: 'foo_desc_1'
        foo_setting_2:
            name: Foo Setting 2
            category: category_1
        foo_setting_3:
            name: Foo Setting 3
            category: category_2
            description: 'foo_desc_3'
            cookie: project.cookie.alternative_settings # Setting stored in a separate cookie

    fox_utils.settings.categories:
        category_1:
            name: Category 1
            description: cat_desc_1
        category_2:
            name: Category 2
```

Settings must have a `name` and `category`. `description` is optional but highly recommended.

Categories must have a `name`. `description` is optional.

Settings menu is visible under `/power_settings_prefix/settings`. The user must be logged in to see the page.

Settings can be stored in multiple cookie stating `cookie` parameter and providing cookie service. More info on usage in [[How to work with cookies]].


## TWIG

User selected values can be queried easily from TWIG like this:

```twig
{% if fox_setting_enabled('foo_setting_2') %}
    Text when user is logged in and setting equals to true.
{% else %}
    Otherwise.
{% endif %}
```

Or using a `UserSettingsManager` service:

```php
$this->userSettingsManager = $container->get('fox_utils.settings.user_settings_manager');
$isEnabled = $this->userSettingsManager->getSettingEnabled($settingName);
```

## Settings change API

Boolean type settings can be toggled when the user visits specific URL generated for that setting. E. g.

```
http://example.com/power-user/settings/change/Nqlx9N1QthIaQ9wJz0GNY79LoYeZUbJC6OuNe==
```

## Enabling Common settings functionality





