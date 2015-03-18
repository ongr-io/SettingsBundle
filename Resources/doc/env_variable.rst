=====================
Environment variables
=====================

    This functionality is enabled by default.

ONGR provides the ability to override container parameters by specifying them in your environment.
ONGR will grab all variables predefined with ONGR__ and set it as a parameter in the service container.
Double underscores are replaced with a period, as a period is not a valid character in an environment variable name.
Note that unlike Symfony environment variables, we override parameters after everything is loaded i.e. your parameters in configuration files will be overwritten.

More about
~~~~~~~~~~

- `Sessionless authentication usage <ongr_sessionless_authentication.rst>`_
- `Personal settings usage <personal_settings.rst>`_
- `General settings usage <general_settings.rst>`_
- `Flash bag usage <flash_bag.rst>`_
