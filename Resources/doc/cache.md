# Cache

ONGR Settings Bundle uses Doctrines `PhpFileCache`. It works very well, however,
it operates correctly only on a single server. If your system uses 
numerous servers to improve its performance, the `FileSystemCache` will
not be able to clear the cache correctly throughout your architecture.

There are, in essence, two ways that you can fix this. Below is information
about both of them.

## Overriding the service

First one is simply overriding the cache service. You will need to use a different cache 
system from `Doctrine`. More information on that can be found [here][1].
Once you've picked a right class for the job, say `Memcache`, you will need
to override the `ong_settings.cache_provider` in your projects `services.yml`
file. Here is an example of the configuration:

```yaml
        
services:        
    memcache:
        class: Memcache
        calls:
            - [ addServer, [ "127.0.0.1", 11211 ]]
            - [ addServer, [ "127.0.0.2", 11211 ]]
            
    ong_settings.cache_provider:
      class: Doctrine\Common\Cache\MemcacheCache
      calls:
          - [setMemcache, ["@memcache"]]
```

## Writing an event listener

The second option is to write an event listener that would listen to the
`ONGR\SettingsBundle\Event\Evens::PRE_UPDATE` event and would call the 
`ongr:settings:cache:clear` command from the other servers. Setting name,
that should be provided to the command as an argument, can be fetched from 
an event. 

[1]: https://symfony.com/doc/current/bundles/DoctrineCacheBundle/index.html