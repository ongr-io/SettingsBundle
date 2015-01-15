==============
SettingsBundle
==============

============
Introduction
============

ONGR Settings Bundle provides settings API and simple user interface for setting management. It's supported by `ONGR <http://ongr.io/>`_ development team.

As ONGR is created with systems using load balancers in mind, this bundle includes cookie based Sessionless authentication and cookie based Flash bag.

Using this bundle you can easily create, update and manage your sites' settings.

While using personal settings you can specify which settings can be seen for chosen visitors e.g.: handy for A/B testing.

General settings are for easily configurable setting management and output.


.. image:: https://magnum.travis-ci.com/ongr-io/SettingsBundle.svg?token=X35UxnxC4zoxXhsTMzw8&branch=master
    :target: https://magnum.travis-ci.com/ongr-io/SettingsBundle


Functionality offered by this bundle can be separated into five parts:

- `Sessionless authentication </Resources/doc/ongr_sessionless_authentication.rst>`_
- `Personal settings </Resources/doc/personal_settings.rst>`_
- `General settings </Resources/doc/general_settings.rst>`_
- `Flash settings </Resources/doc/flash_bag.rst>`_
- `Environment variables </Resources/doc/env_variable.rst>`_


==========================
Enabling and setting it up
==========================

``SettingsBundle`` requires minimal efforts to get it working. Firstly, install package using Composer:

.. code-block:: bash

    composer require ongr/settings-bundle dev-master

..

Then register it in ``AppKernel.php``:

.. code-block:: php

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            return [
            // ...
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Tedivm\StashBundle\TedivmStashBundle(),
            new ONGR\CookiesBundle\ONGRCookiesBundle(),
            new ONGR\SettingsBundle\ONGRSettingsBundle(),
            );
        }

        // ...
    }

..

Add this to your main ``routing.yml``

.. code-block:: yaml

    fos_js_routing:
        resource: "@FOSJsRoutingBundle/Resources/config/routing/routing.xml"

    ongr_settings_routing:
        resource: "@ONGRSettingsBundle/Resources/config/routing.yml"
        prefix: /settings_prefix

..

After this is completed, you shoud add a type mapping to your Elastic Search configuration.
If You had defined mappings for your system, you should add:

.. code-block:: yaml

    - ONGRSettingsBundle

..

to your mapping section. More about Elastic Search mappings can be found (`here <https://github.com/ongr-io/ElasticsearchBundle/blob/master/Resources/doc/mapping.md>`_)

Next Elastic Search types should be updated, by running a command in console:

.. code-block:: bash

    app/console es:type:update --force

..

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Enabling Sessionless authentication support
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Systems using load balancers cannot use standard symfony authentication (which is based on sessions).
This bundle is thus offering sessionless authentication functionality. You can read about how it works and how to enable it
`here </Resources/doc/ongr_sessionless_authentication.rst>`_.

=====
Usage
=====

- `Sessionless authentication usage </Resources/doc/ongr_sessionless_authentication.rst>`_
- `Personal settings usage </Resources/doc/personal_settings.rst>`_
- `General settings usage </Resources/doc/general_settings.rst>`_
- `Flash bag usage </Resources/doc/flash_bag.rst>`_
- `Environment variables usage </Resources/doc/env_variable.rst>`_

This bundle depends on:

- `ONGR/CookiesBundle <https://github.com/ongr-io/CookiesBundle>`_
- `ONGR/ElasticsearchBundle <https://github.com/ongr-io/ElasticsearchBundle>`_
- `ONGR/ContentBundle <https://github.com/ongr-io/ContentBundle>`_
- `ONGR/FilterManagerBundle <https://github.com/ongr-io/FilterManagerBundle>`_

~~~~~~~
License
~~~~~~~

This bundle is under the MIT license. Please, see the complete license in the bundle `LICENSE </LICENSE>`_ file.

.. toctree::
    :maxdepth: 1
        :glob:

        *