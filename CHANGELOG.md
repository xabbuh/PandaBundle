CHANGELOG
=========

* move event name constants to the event classes

* extend the panda:video:upload command to be able to path profiles used to
  encode the video, a custom path format and an arbitrary payload

* less restrictive type hint in the ``VideoUploaderExtension`` (rely on the
  ``UrlGeneratorInterface`` instead of requiring the complete ``Router`` class
  (by @stof in #1)

* extract event names into their own class constants (by @tgallice in #2)

* reflect the extended abilities from the 1.1.0 release of xabbuh/panda-client
  in the commands

* full support for HHVM

1.0.3
-----

* add tests for the ``EventFactory`` class

1.0.2
-----

* ensure that a form id is set when enabling the upload form widget

* add tests for the ``VideoUploaderExtension`` class

* commands don't terminate unexpectedly when the client library throws an
  exception

1.0.1
-----

* service container extension test improvements (by using test case classes
  from the ``matthiasnoback/symfony-dependency-injection-test`` package)

* do not display misleading pagination information with empty result sets in
  the ``panda:video:list`` command
