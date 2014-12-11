======================================
Enabling and using Admin settings functionality
======================================


Introduction
----------------
    Note: This functionality will require You to login using Sessionless authentication.

While using this functionality, settings can be changed per user, from the settings page and the selected values are stored in a separate cookie.

Enabling
----------------
To enable a user to edit it's settings, to your main ``routing.yml`` add a route:

.. code-block:: yaml

    _admin_settings:
        resource: "@ONGRAdminBundle/Resources/config/routing/admin_settings.yml"
        prefix: /admin_settings_prefix

..

And add some settings that are grouped in categories (``config.yml``):

.. code-block:: yaml

    parameters:
        ongr_admin.settings.settings:
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

        ongr_admin.settings.categories:
            category_1:
                name: Category 1
                description: cat_desc_1
            category_2:
                name: Category 2

..

Usage
-------
Settings must have a ``name`` and ``category``. ``description`` is optional but highly recommended.

Categories must have a ``name``. ``description`` is optional.

Settings menu is visible under ``/admin_settings_prefix/settings``. The user must be logged in to see the page.

Settings can be stored in multiple cookie stating ``cookie`` parameter and providing cookie service.
More info on usage in `How to work with cookies documentation <https://github.com/ongr-io/CookiesBundle>`_.


TWIG
~~~~

User selected values can be queried easily from TWIG like this:

.. code-block:: twig

    {% if ongr_setting_enabled('foo_setting_2') %}
        Text when user is logged in and setting equals to true.
    {% else %}
        Otherwise.
    {% endif %}

..

Or using a ``UserSettingsManager`` service:

.. code-block:: php

    $this->userSettingsManager = $container->get('ongr_admin.settings.user_settings_manager');
    $isEnabled = $this->userSettingsManager->getSettingEnabled($settingName);

..

Settings change API
~~~~~~~~~~~~~~~~~~~~~~~~

Boolean type settings can be toggled when the user visits specific URL generated for that setting. E. g.

- `http://example.com/admin_settings_prefix/settings/change/Nqlx9N1QthIaQ9wJz0GNY79LoYeZUbJC6OuNe== <http://example.com/admin_settings_prefix/settings/change/Nqlx9N1QthIaQ9wJz0GNY79LoYeZUbJC6OuNe==>`_


More about
~~~~~~~
- `Common settings usage </Resources/doc/common_settings.rst>`_
- `Flash bag usage </Resources/doc/flash_bag.rst>`_
- `Environment variables usage </Resources/doc/env_variable.rst>`_