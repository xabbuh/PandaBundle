UPGRADE
=======

Upgrading from 1.x to 2.0
-------------------------

* the `CloudFactoryInterface` and the `CloudFactory` class have been removed
* the command classes require to inject arguments through the constructor
* the `setContainer()` and `getContainer()` methods have been removed from
  the command classes
* extending the command or event classes is no longer supported
