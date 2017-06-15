# SilverWare Model Filters Module

[![Latest Stable Version](https://poser.pugx.org/silverware/model-filters/v/stable)](https://packagist.org/packages/silverware/model-filters)
[![Latest Unstable Version](https://poser.pugx.org/silverware/model-filters/v/unstable)](https://packagist.org/packages/silverware/model-filters)
[![License](https://poser.pugx.org/silverware/model-filters/license)](https://packagist.org/packages/silverware/model-filters)

Extends the [SilverStripe v4][silverstripe] `ModelAdmin` to support filtering of versioned data objects.

## Contents

- [Background](#background)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Issues](#issues)
- [Contribution](#contribution)
- [Maintainers](#maintainers)
- [License](#license)

## Background

Traditionally, versioning in SilverStripe has been the sole domain of
`SiteTree` objects within the CMS, such as the ubiquitous `Page` class.
With the advent of SilverStripe v4, versioning is now used in
other areas of the CMS, such as assets.

You can add versioning to your own model classes by using the
[`Versioned`][silverstripe-versioned] extension. `ModelAdmin` supports
versioned data objects out of the box, however filtering these objects
by their status is a bit trickier.

Enter `silverware/model-filters`. After installation of this module,
your versioned data objects will have a status dropdown field added to
the filter form that appears within `ModelAdmin`, allowing the user
to filter records by their versioning status:

![Record Status Filter](http://i.imgur.com/841RCcE.png)

You can also add `StatusBadges` to your `$summary_fields` to show
the versioning status within your model admin grid field:

![Record Status Badges](http://i.imgur.com/8PUxsQ3.png)

## Requirements

- [SilverStripe Framework v4][silverstripe]
- [SilverStripe Admin][silverstripe-admin]
- [SilverStripe Versioned][silverstripe-versioned]

## Installation

Installation is via [Composer][composer]:

```
$ composer require silverware/model-filters
```

## Usage

Once installed, `Versioned` data objects within `ModelAdmin` will
be automatically detected and a status field will appear within
the filter form for each object.

In order to show status badges within the grid field for your
versioned objects, simply add `StatusBadges` to your `$summary_fields` static,
for example:

```php
private static $summary_fields = [
  ...
  'StatusBadges',
  ...
];
```

### Status Field Positioning

By default, the status dropdown field will be added to the end of
your search filter fields for each versioned object.  To control
where the field appears, add one of the following methods to
your `ModelAdmin` subclass:

```php
public function getStatusFieldBefore()
{
    return 'NameOfField';  // will appear before this field
}
```

```php
public function getStatusFieldAfter()
{
    return 'NameOfField';  // will appear after this field
}
```

You could also detect the current model class via `$this->modelClass` and answer a
different field name, if required:

```php
public function getStatusFieldAfter()
{
    switch ($this->modelClass) {
        case FirstModel::class:
            return 'AfterThisField';
        case SecondModel::class:
            return 'AfterAnotherField';
    }
}
```

### Status Field Title

By default, the title of the status dropdown field is "Record status". The title
is obtained by calling `getStatusFieldTitle()` on the `ModelAdmin` subclass.  The method
added via the extension supports `i18n` via the usual SilverStripe
conventions, however you can also override the method in your
`ModelAdmin` subclass to answer a different field title:

```php
public function getStatusFieldTitle()
{
    return 'New field title';
}
```

## Issues

Please use the [GitHub issue tracker][issues] for bug reports and feature requests.

## Contribution

Your contributions are gladly welcomed to help make this project better. Please see [contributing](CONTRIBUTING.md)
for more information.

## Maintainers

[![Colin Tucker](https://avatars3.githubusercontent.com/u/1853705?s=144)](https://github.com/colintucker) | [![Praxis Interactive](https://avatars2.githubusercontent.com/u/1782612?s=144)](http://www.praxis.net.au)
---|---
[Colin Tucker](https://github.com/colintucker) | [Praxis Interactive](http://www.praxis.net.au)

## License

[BSD-3-Clause](LICENSE.md) &copy; Praxis Interactive

[silverstripe]: https://github.com/silverstripe/silverstripe-framework
[silverstripe-admin]: https://github.com/silverstripe/silverstripe-admin
[silverstripe-versioned]: https://github.com/silverstripe/silverstripe-versioned
[issues]: https://github.com/praxisnetau/silverware-model-filters/issues
[composer]: https://getcomposer.org
