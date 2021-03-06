# Stamp Plugin

The Grav **Stamp** Plugin is designed for [Grav CMS](http://github.com/getgrav/grav) and automatically adds or modifies the `date`, `date_modified`, `author`, `editor` and `revision` to frontmatter when the page is saved via the Grav Admin Plugin.

## Description

The plugin extends and complements the [Auto Date Plugin](https://github.com/getgrav/grav-plugin-auto-date) and the [Auto Author Plugin](https://github.com/naucon/grav-plugin-auto-author) (wrong link, plugin is [here](https://github.com/naucon/grav-auto-author)), but is not dependent on them and works completely independently.

The dependency on Admin Plugin v1.2.3 is now set (same version as now set for Auto Date and Auto Author plugins). If this causes problems, send me an [issue](https://github.com/petira/grav-plugin-stamp/issues) and I will update the version to the latest one.

You can always find the latest version of this [documentation](https://github.com/petira/grav-plugin-stamp/blob/develop/README.md) on the project [homepage](https://github.com/petira/grav-plugin-stamp).

### Primary purpose

**The primary purpose** of this plugin is to automatically generate variables related to the page modification:

* `date_modified` - the date the page was last saved
* `editor` - the user who last saved the page
* `revision` - the number of page revisions (0 means original, 1 means first revision, etc.)

These variables are overwritten **each time the page is saved via the Admin Plugin**. For information: moving the page via the Admin Plugin is the same as saving it!

The `revision` variable serves several purposes. It primarily shows how many page revisions have been made. However, it can also be used in a twig template to display variables related to the page modification:

```
Published <i class="fa fa-calendar"></i> {{ page.date|date(system.pages.dateformat.long) }}
by <i class="fa fa-user"></i> {{ page.header.author }}
{% if page.header.revision %}
    | Modified <i class="fa fa-calendar"></i> {{ page.header.date_modified|date(system.pages.dateformat.long) }}
    by <i class="fa fa-user"></i> {{ page.header.editor }}
    | Revision <i class="fa fa-save"></i> {{ page.header.revision }}
{% endif %}
```

### Secondary purpose

**The secondary purpose** of this plugin is to be able to add or modify `date` and `author` variables **when the page is first saved via the Admin Plugin**. The Stamp Plugin can replace previous plugins (Auto Date Plugin and Auto Author Plugin), but it behaves differently, which must be taken into account. The main difference is that previous plugins generate variables when the page is created, and this plugin generates variables when the page is saved. If the date is to match the first save, using this plugin is the right choice.

Meaning of variables:

* `date` - the date is one of the main variables of the Grav, but nowhere is it specified what the date means. This can be the date the page was created, the date the page was first saved, but also the date the page was first published (or a completely different date - past, present or future). This flexibility is completely unique and perfect.
* `author` - the author is a completely user variable. This can be the author of the page content, but also only the first page editor who has nothing to do with the page content.

It is therefore important to determine exactly what these values mean.

**When the page is first saved via the Admin Plugin**, there are the following options for these variables:

#### Date action
* if the `date` does not exist and the Add option is selected in the plugin settings, the `date` is added. The `date` is the same as the first `date_modified`.
* if the the Modify option is selected in the plugin settings, the `date` is always modified (if the `date` does not exist, it will be added). The `date` is the same as the first `date_modified`.

#### Author action
* if the `author` does not exist and the Add option is selected in the plugin settings, the `author` is added. The `author` is the same as the first `editor`.
* if the the Modify option is selected in the plugin settings, the `author` is always modified (if the `author` does not exist, it will be added). The `author` is the same as the first `editor`.

## Specific settings

### Modifying the value of the `revision` variable

There are many reasons to modify the value of the `revision` variable. For example, if the page has been re-saved with only minor adjustments, then it is certainly not necessary for those adjustments to take effect in the `revision` value. Therefore, it is possible to modify the value of the `revision` variable to the last known one before editing and the page must be saved again.

If it is necessary to set the value of the `revision` variable to 0 to simulate the original version of the page (for example, to hide modified values in the twig template, or simply to restart numbering), the value of the `revision` variable must be adjusted to `-1`, `null`, or `reset`, and the page must be saved again. Frankly, this can now be achieved with any value other than an integer of `0` or higher and a special value named `new`. Alternatively, the `revision` variable can also be completely deleted.

The `revision` variable can be modified via expert mode (control value) or directly in the `.md` file (final value).

### Simulate the behavior as when the page is first saved

In practice, there are many cases where it is necessary to repeatedly save the page as a draft and publish only the final version. For this reason, it is possible to overwrite the values of the `date` and `author` variables by setting the value `new` to the `revision` variable. To prevent accidental overwriting, 'Override values' must also be enabled in the plugin settings. Otherwise, a value of `new` will only set the `revision` variable to `0`, just like 'null' in the previous setting.

Other words, if the value of the `revision` variable is set to `new` and the `override` variable in the `stamp.yaml` is set to `true` (default is `false`), **it provides the same behavior as when the page is first saved via the Grav Admin Plugin**.

## Recommendation!

It is recommended that you add the value of seconds to the `dateformat.default` variable in the `site.yaml` file for accurate time determination. It must be done directly in the `system.yaml` file, it is not possible via the Admin Plugin. The value must not be subsequently changed via the Admin Plugin.

```
dateformat:
  default: 'H:i:s d-m-Y'
```

## To Do

- [ ] Add support for taxonomy type of `author`
- [ ] Add the optional ability to automatically modify the parent page (usually a `modular` page)
- [ ] Translantions (after standard terms have been introduced)
- [ ] Sample templates
- [ ] Standardize the contents of the `README.md` file
- [ ] Add option for modification only (now merged with addition), if necessary
- [ ] Add other variables, for example: `date_created`, `creator`, `date_accessed` (probably within a storage other than the frontmatter), `visitor` (probably within a storage other than the frontmatter), etc.
- [ ] Add alternative `stamp` variables: `stamp.created.on`, `stamp.created.by`, `stamp.modified.on`, `stamp.modified.by`, etc., if necessary
- [ ] Additional control values for the `revision` variable, for example: change the value of the `revision` variable without affecting the values of other variables
- [ ] All changes made through the `revision` variable will be conditional on enabling the `override` variable