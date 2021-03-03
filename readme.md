# Font Awesome Plugin

This plugin adds a Font Awesome icon dropdown, and more!

## Backend Forms

### Icon Select Form Widget

The simplest configuration for a backend form field:

```yaml
fields:
    icon:
        label: 'Choose an icon'
        type: fontawesome
```

This displays a searchable dropdown form widget. The same options for the built-in type `dropdown` will work here. The necessary JS/CSS assets will be automatically injected into the page.

## Backend Lists

### Icon Column Type

The simplest configuration for a backend list column:

```yaml
columns:
    icon:
        label: 'Icon'
        type: fontawesome
```

**Note:** The backend list controller must implement the `Zaxbux\FontAwesome\Behaviors\FontAwesomeIconAssets` behavior for the necessary JS/CSS assets to be injected.