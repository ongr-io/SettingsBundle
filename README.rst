===========
AdminBundle
===========

Provides settings API and admin interface for ONGR projects.

.. image:: https://magnum.travis-ci.com/ongr-io/AdminBundle.svg?token=X35UxnxC4zoxXhsTMzw8&branch=master
    :target: https://magnum.travis-ci.com/ongr-io/AdminBundle

It includes:

- `Admin functionality </Resources/doc/admin_settings.rst>`_
- `Common functionality </Resources/doc/common_settings.rst>`_
- `Flash functionality </Resources/doc/flash_bag.rst>`_
- `Environment functionality </Resources/doc/env_variable.rst>`_

Depends on:

- `ONGR/CookiesBundle <https://github.com/ongr-io/CookiesBundle>`_
- `ONGR/ElasticsearchBundle <https://github.com/ongr-io/ElasticsearchBundle>`_
- `ONGR/ContentBundle <https://github.com/ongr-io/ContentBundle>`_
- `ONGR/FilterManagerBundle <https://github.com/ongr-io/FilterManagerBundle>`_


=================================
Enabling and setting it up
=================================

``AdminBundle`` requires minimal efforts to get it working. Firstly, install package using Composer:

.. code-block:: bash

    composer require ongr-io/AdminBundle 0.1.*

..

Then register it in ``AppKernel.php``:

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
            new ONGR\AdminBundle\ONGRAdminBundle(),
            );
        }

        // ...
    }

..

After this is completed, you shoud add a type mapping to your Elastic Search configuration:
If You had defined mappings for your system, you should add

.. code-block:: yaml

    - ONGRAdminBundle

..

to your mapping section. More about mappings can be found (`here <https://github.com/ongr-io/ElasticsearchBundle/blob/master/Resources/doc/mapping.md>`_)

Next Elastic Search types should be updated, by running a command in console:

.. code-block:: bash

    app/console es:type:update --force

..

Enabling Sessionless authentication support
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To enable authentication support, please add this to your main ``routing.yml``

.. code-block:: yaml
    
    fos_js_routing:
        resource: "@FOSJsRoutingBundle/Resources/config/routing/routing.xml"
    
    ongr_admin_routing:
        resource: "@ONGRAdminBundle/Resources/config/routing.yml"
        prefix: /admin

..

Then add some users to you ``config.yml`` parameters section:

.. code-block:: yaml

    parameters:
        ongr_admin.authentication.users:
            foo_user:
                password: 'foo_password'
            foo_user_bar:
                password: 'foo_bar_password'

..

Login page is at ``/admin_prefix/login``. There is also a logout page at ``/admin_prefix/logout``.

Some auth cookie properties:

* Login credentials are stored in a signed tamper-proof authentication cookie that is **valid for X hours**.
* Authentication cookie's signature **contains username**, **IP address**, expiration **timestamp** and **password**. Therefore if any of the values change, then cookie becomes invalid.

Values can change in several places. Eg. IP address is dependent on the network, password can change in the configuration file and the expiration timestamp or the username can be modified in the cookie itself.

* Cookie **can be stolen** if sent over *http://*, so do not trust it's security absolutely.

Enabling Stash cache
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To enable stash cache, please add this to your main ``settings.yml``

.. code-block:: yaml

   stash:
   caches:
       default:
           drivers: [ FileSystem ]
           FileSystem: ~

..

===============
Bundles usage
===============

- `Admin settings usage </Resources/doc/admin_settings.rst>`_
- `Common settings usage </Resources/doc/common_settings.rst>`_
- `Flash bag usage </Resources/doc/flash_bag.rst>`_
- `Environment variables usage </Resources/doc/env_variable.rst>`_

.. toctree::
    :maxdepth: 1
        :glob:

        *
