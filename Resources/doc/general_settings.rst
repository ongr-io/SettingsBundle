==================================================
Enabling and using General settings functionality
==================================================

Introduction
----------------
This component provides functionality to configure website in real time.

Enabling
----------------

To enable this functionality, You simply need to add routes to `routing.yml`:

.. code-block:: yaml

    fos_js_routing:
        resource: "@FOSJsRoutingBundle/Resources/config/routing/routing.xml"

    ongr_settings:
        resource: "@ONGRSettingsBundle/Resources/config/routing.yml"
        prefix:   /settings/

..

That's it. Now you should be able to open settings list which should be empty until you add some settings.7

Usage
--------

Adding Setting
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Default way to add settings is through edit buttons in front-end.

Everywhere you want to have something configurable just render settings button with setting name. For example:

.. code-block:: twig

    <div>{{ ongr_show_setting('count_per_page') }}</div>

..

By passing second parameter any of **string**, **boolean**, **array** or **object** you can force setting type:

.. code-block:: twig

    <div>{{ ongr_show_setting('count_per_page', 'object') }}</div>

..

To see this button you need to log in as Admin and enable "live settings". After this button appears just click on it and you will be redirected to edit page where you can set or update value of the setting.

Injecting Settings to Services
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Injecting settings we made as simple as it can be. To inject setting you only need to create setter method and add `ongr_settings.setting_aware`` tag to service declaration:

.. code-block:: yaml

    services:
        ongr_settings.demo_service:
            class: %ongr_settings.demo_service.class%
            tags:
                # This is an example how ongr-settings can be used
                - { name: 'ongr_settings.setting_aware', setting: 'count_per_page' }

..

What happens in background? Actual service will be replaced with proxy service using service factory. Factory service gets actual service as parameter and on demand injects tagged settings.


   Note: ``ongr-settings`` tries to guess setter name by transforming setting name to camel case. If you want to specify custom setter name, add tag attribute `method`.

Getting Setting in Template
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You can easily access setting value in any template using ``personal_setting`` function. Example:

.. code-block:: html

    <p>Default items count per page: {{ personal_setting('count_per_page') }}</p>

..

Settings Cache
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

`ongr-settings` uses `StashBundle <https://github.com/tedious/TedivmStashBundle>`_ to cache settings. By default Filesystem cache driver is used. To ensure best performance change it `Memcache` or other fast cache engine.

To enable stash cache, please add this to your main ``config.yml``

.. code-block:: yaml

   stash:
   caches:
       default:
           drivers: [ FileSystem ]
           FileSystem: ~

..

To enable stash cache, please add this to your main ``settings.yml``

.. code-block:: yaml

   stash:
   caches:
       default:
           drivers: [ FileSystem ]
           FileSystem: ~

..

Tags
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Setting aware
--------------

Sets service values from personal. Service must have a setter.

- ``ongr_settings.setting_aware``
- Parameters
    - ``setting`` - specify setting name set in personal
    - ``method`` - setter method name (optional)


Example YAML configuration:

.. code-block:: yaml

    parameters:
        my_bundle.db_driver.class: Vendor\MyBundle\Service\MyService

    services:
        my_bundle.service:
            class: %my_bundle.service.class%
            tags:
             - { name: ongr_settings.setting_aware, setting: my_setting, method: setMySetting}

..


More about
~~~~~~~
- `Personal settings usage </Resources/doc/general_settings.rst>`_
- `Flash bag usage </Resources/doc/flash_bag.rst>`_
- `Environment variables usage </Resources/doc/env_variable.rst>`_
