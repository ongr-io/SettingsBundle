===========
SettingsBundle
===========

Provides settings API and admin interface for ONGR projects.

.. image:: https://magnum.travis-ci.com/ongr-io/SettingsBundle.svg?token=X35UxnxC4zoxXhsTMzw8&branch=master
:target: https://magnum.travis-ci.com/ongr-io/SettingsBundle

It includes:

- `Sessionless cookie authentication </Resources/doc/ongr_sessionless_authentication.rst>`_
- `Personal settings </Resources/doc/personal_settings.rst>`_
- `General settings </Resources/doc/general_settings.rst>`_
- `Flash settings </Resources/doc/flash_bag.rst>`_
- `Environment settings </Resources/doc/env_variable.rst>`_

Depends on:

- `ONGR/CookiesBundle <https://github.com/ongr-io/CookiesBundle>`_
- `ONGR/ElasticsearchBundle <https://github.com/ongr-io/ElasticsearchBundle>`_
- `ONGR/ContentBundle <https://github.com/ongr-io/ContentBundle>`_
- `ONGR/FilterManagerBundle <https://github.com/ongr-io/FilterManagerBundle>`_


=================================
Enabling and setting it up
=================================

``SettingsBundle`` requires minimal efforts to get it working. Firstly, install package using Composer:

.. code-block:: bash

    composer require ongr-io/SettingsBundle 0.1.*

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

After this is completed, you shoud add a type mapping to your Elastic Search configuration:
If You had defined mappings for your system, you should add

.. code-block:: yaml

    - ONGRSettingsBundle

..

to your mapping section. More about mappings can be found (`here <https://github.com/ongr-io/ElasticsearchBundle/blob/master/Resources/doc/mapping.md>`_)

Next Elastic Search types should be updated, by running a command in console:

.. code-block:: bash

    app/console es:type:update --force

..

Enabling Sessionless authentication support
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To enable authentication support, first add this to your main ``routing.yml``

.. code-block:: yaml
    
    fos_js_routing:
        resource: "@FOSJsRoutingBundle/Resources/config/routing/routing.xml"
    
    ongr_settings_routing:
        resource: "@ONGRSettingsBundle/Resources/config/routing.yml"
        prefix: /settings_prefix

..

Then configure, your security.yml as shown in example.
You must set `` ongr_sessionless_authentication: true `` so symfony will be able to tap into ongr_sessionless_authentication.
All other settings (eg.: user providers) are configurable accordingly to symfonys documentation.

.. code-block:: yaml

    security:
        firewalls:
            sessionless_authentication_secured:
                pattern:   .*
                anonymous: ~
                ongr_sessionless_authentication: true
                form_login:
                    login_path: ongr_settings_sessionless_login #path where login form resides
                    check_path: login_check      #default Authentication provider
                    failure_path: /  #on failure
                logout:
                    path:   ongr_settings_sessionless_logout
                    target: /
        access_control:
            - { path: ^/settings_prefix/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/settings_prefix/logout, roles: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/settings_prefix/.* , roles: ROLE_ADMIN }

        providers:
            foo_chain_provider:
                chain:
                    providers: [in_memory, test_provider]
            in_memory:
                memory:
                    users:
                        foo_user:    { password: foo_password, roles: 'ROLE_ADMIN' }
            test_provider:
                memory:
                    users:
                        test:     { password: test, roles: 'ROLE_ADMIN' }

        encoders:
            Symfony\Component\Security\Core\User\User: plaintext

..

Login page is at ``/settings_prefix/login``. There is also a logout page at ``/settings_prefix/logout``.

Using these settings you can configure as much firewalls as you want in your whole project.

Authentications "session" is stored in cookies. Some of their properties:

* Login credentials are stored in a signed tamper-proof authentication cookie that is **valid for X hours**.
* Authentication cookie's signature **contains username**, **IP address**, expiration **timestamp** and **password**. Therefore if any of the values change, then cookie becomes invalid.

Values can change in several places. Eg. IP address is dependent on the network, password can change in the configuration file and the expiration timestamp or the username can be modified in the cookie itself.

* Cookie **can be stolen** if sent over *http://*, so do not trust it's security absolutely.

==============================
Bundles functionalities usage
==============================

- `Sessionless cookie authentication </Resources/doc/ongr_sessionless_authentication.rst>`_
- `Personal settings usage </Resources/doc/personal_settings.rst>`_
- `General settings usage </Resources/doc/general_settings.rst>`_
- `Flash bag usage </Resources/doc/flash_bag.rst>`_
- `Environment variables usage </Resources/doc/env_variable.rst>`_

.. toctree::
:maxdepth: 1
            :glob:

            *
