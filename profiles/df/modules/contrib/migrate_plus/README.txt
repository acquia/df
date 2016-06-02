The migrate_plus module extends the core migration system with API enhancements
and additional functionality, as well as providing practical examples.

Extensions to base API
======================
* A Migration configuration entity is provided, enabling persistance of dynamic
migration configuration.
* A ConfigEntityDiscovery class is implemented which enables plugin configuration
to be based on configuration entities. This is fully general - it can be used
for any configuration entity type, not just migrations.
* A MigrationConfigEntityPluginManager class and corresponding
plugin.manager.config_entity_migration service is provided, to enable discovery
and instantiation of migration plugins based on the Migration configuration
entity.
* A MigrationGroup configuration entity is provided, which enables migrations to
be organized in groups, and to maintain shared configuration in one place.
* A MigrateEvents::PREPARE_ROW event is provided to dispatch hook_prepare_row()
invocations as events.
* A SourcePluginExtension class is provided, enabling one to define fields and
IDs for a source plugin via configuration rather than requiring PHP code.

Plugins
=======
* A Url source plugin is provided, implementing a common structure for
file-based data providers.
* XML and JSON fetchers and parsers for the Url source plugin are provided.

Examples
========
* The migrate_example submodule provides a fully functional and runnable
example migration scenario demonstrating the basic concepts and most common
techniques for SQL-based migrations.
* The migrate_example_advanced submodule provides examples of migration from
different kinds of sources, as well as less common techniques.
