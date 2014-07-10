CHANGELOG
=========

* less restrictive type hint in the ``VideoUploaderExtension`` (rely on the
  ``UrlGeneratorInterface`` instead of requiring the complete ``Router`` class
  (by @stof in #1)

* extract event names into their own class constants (by @tgallice in #2)

* reflect the extended abilities from the 1.1.0 release of xabbuh/panda-client
  in the commands

* full support for HHVM
