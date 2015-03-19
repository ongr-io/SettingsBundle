==============
SettingsBundle
==============

------------
Introduction
------------

ONGR Settings Bundle provides settings API and simple user interface for setting management.
It's supported by `ONGR <http://ongr.io/>`_ development team.

As ONGR is created with systems using load balancers in mind, this bundle includes cookie based Sessionless
authentication and cookie based Flash bag.

Using this bundle you can easily create, update and manage your sites' settings.

While using personal settings you can specify which settings can be seen for chosen visitors e.g.: handy for A/B testing.

General settings are for easily configurable setting management and output.

Functionality offered by this bundle can be separated into five parts:

- `Sessionless authentication <ongr_sessionless_authentication.rst>`_
- `Personal settings <personal_settings.rst>`_
- `General settings <general_settings.rst>`_
- `Flash settings <flash_bag.rst>`_
- `Environment variables <env_variable.rst>`_


--------------------------
Enabling and setting it up
--------------------------

SettingsBundle requires minimal efforts to get it working. Firstly, install the package using Composer.
In your shell enter the following:

.. code-block:: bash

    composer require ongr/settings-bundle "~0.1"

..

This will add the required entries to ``composer.json`` and download SettingsBundle and dependent bundles to your
projects' vendor library.

Then register SettingsBundle and it dependant bundles in ``AppKernel.php``:

.. code-block:: php

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            return [
                // ...
                new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
                new Tedivm\StashBundle\TedivmStashBundle(),
                new ONGR\CookiesBundle\ONGRCookiesBundle(),
                new ONGR\ElasticsearchBundle\ONGRElasticsearchBundle(),
                new ONGR\FilterManagerBundle\ONGRFilterManagerBundle(),
                new ONGR\SettingsBundle\ONGRSettingsBundle(),
            ];
        }

        // ...
    }

..

Notice that ``JsRoutingBundle``, ``StashBundle``, ``CookiesBundle``, ``ElasticsearchBundle``
also have to be registered. These bundles were installed as dependencies to SettingsBundle.

Next, enable access to SettingsBundle by adding this to your main ``routing.yml``:

.. code-block:: yaml

    fos_js_routing:
        resource: "@FOSJsRoutingBundle/Resources/config/routing/routing.xml"

    ongr_settings_routing:
        resource: "@ONGRSettingsBundle/Resources/config/routing.yml"
        prefix: /settings_prefix

..

.. warning::

    ``prefix: /settings_prefix`` should be configured accordingly to your project needs.
    All SettingsBundle's functionality will reside under this prefix. e.g.: your login link to settings UI would be
    /settings_prefix/login.

After this is completed, you should configure your Elasticsearch storage for your settings.
It will consist of two parts.


First - Elasticsearch connection details and mappings, for your settings documents should be defined.
You should add an entry to your ``config.yml`` you should add an entry:

.. code-block:: yaml

    ongr_elasticsearch:
        connections:
            settings:
                hosts:
                    - { host: 127.0.0.1:9200 }
                index_name: settings
                settings:
                    number_of_shards: 2
                    number_of_replicas: 0
        managers:
            settings:
                connection: settings
                mappings:
                    - ONGRSettingsBundle

..

    Using this config, console command below will create an Elasticsearch index called ``settings``
    with 2 shards and 0 replicas, after running the console command mentioned above.

In case if you wish to use different Elasticsearch connection options, you can configure manager used in SettingsBundle
with following ``config.yml`` entry:

.. code-block:: yaml

    ongr_settings:
        connection:
            repository: es.manager.default.setting

..

This example shows how you can configure settings repository, that is access in manager called ``default``.

Second - new index in Elasticsearch should be created.
This can be done by running a command in console:

.. code-block:: bash

    app/console es:index:create --manager settings

..

    NOTE: We strongly recommend to have a separate index for your settings (as in example above) so your "working"
    data won't mix up with settings.

More information about Elasticsearch configuration can be found in our ElasticsearchBundle
`documentation <http://ongr.readthedocs.org/en/latest/components/ElasticsearchBundle/index.html>`_.

Also it's recommended to install projects' assets:

.. code-block:: bash

    app/console assets:install

..

And the next step towards victory is: ...

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Enabling Sessionless authentication support
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Systems using load balancers cannot use standard symfony authentication (which is based on sessions).
This bundle is thus offering sessionless authentication functionality. You can read about how it works and how
to enable it
`here <ongr_sessionless_authentication.rst>`_.

------------
Dependencies
------------

- `ONGR/CookiesBundle <https://github.com/ongr-io/CookiesBundle>`_
- `ONGR/ElasticsearchBundle <https://github.com/ongr-io/ElasticsearchBundle>`_
- `ONGR/ContentBundle <https://github.com/ongr-io/ContentBundle>`_
- `ONGR/FilterManagerBundle <https://github.com/ongr-io/FilterManagerBundle>`_
