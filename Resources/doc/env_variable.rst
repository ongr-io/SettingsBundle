=====================
Environment variables
=====================

    This functionality is enabled by default.

ONGR provides the ability to override container parameters by specifying them in your environment (``$_SERVER`` variable).
ONGR will grab all variables prefixed with ``ONGR__`` and set it as a parameter in the service container.
Double underscores are replaced with a period, as a period is not a valid character in an environment variable name.
Note that unlike Symfony environment variables, we override parameters after everything is loaded i.e. your parameters in configuration files will be overwritten.

For example, if you would like to override a parameter ``ongr.foo.bar`` with a value of **3** on a server running nginx,
you should change the nginx configuration like this:

.. code::

    location ~ \.php$ {
      include fastcgi_params;
      fastcgi_param ONGR__foo__bar 3; # This will set $_SERVER['ONGR__foo__bar'] to 3.
      fastcgi_pass php;
    }

..

On Apache enable the SetEnv module and use ``SetEnv`` directive.

Refer to PHP and / or your HTTP server documentation for more information on how to set ``$_SERVER`` environment variables.

More about
~~~~~~~~~~

- `Sessionless authentication usage <ongr_sessionless_authentication.rst>`_
- `Personal settings usage <personal_settings.rst>`_
- `General settings usage <general_settings.rst>`_
- `Flash bag usage <flash_bag.rst>`_
