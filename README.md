# Stamp Plugin

The **Stamp** Plugin is an extension for [Grav CMS](https://github.com/getgrav/grav). The Stamp Plugin automatically adds or modifies the `date`, `date_modified`, `author`, `editor`, `revision` and `taxonomy.author` to frontmatter when the page is saved via the Grav Admin Plugin. Optionally adds Twig variable users which contains data from user accounts.

The plugin extends and complements the [Auto Date Plugin](https://github.com/getgrav/grav-plugin-auto-date) and the [Auto Author Plugin](https://github.com/naucon/grav-plugin-auto-author) (wrong link, plugin is [here](https://github.com/naucon/grav-auto-author)), but is not dependent on them and works completely independently.

The dependency on Grav v1.6.0 is now set, but it is being developed on Grav v1.7 and Admin Panel v1.10.

You can always find the latest version of this [documentation](https://github.com/petira/grav-plugin-stamp/blob/develop/README.md) on the project [homepage](https://github.com/petira/grav-plugin-stamp).

If you find a problem or have a suggestion for improvement, please send me an [issue](https://github.com/petira/grav-plugin-stamp/issues).

If you translate the Stamp Plugin into another language, please send me the [strings](https://github.com/petira/grav-plugin-stamp/blob/develop/languages.yaml) via [pull request](https://github.com/petira/grav-plugin-stamp/pulls) or [issue](https://github.com/petira/grav-plugin-stamp/issues).

The [demo](https://www.grav.cz/demo/stamp) is available on the [Grav.cz](https://www.grav.cz) website.

## Installation

Installing the Stamp Plugin can be done in one of three ways: The GPM (Grav Package Manager) installation method lets you quickly install the plugin with a simple terminal command, the manual method lets you do so via a zip file, and the admin method lets you do so via the Admin Plugin.

### GPM Installation (Preferred)

To install the plugin via the [GPM](https://learn.getgrav.org/cli-console/grav-cli-gpm), through your system's terminal (also called the command line), navigate to the root of your Grav installation, and enter:

    bin/gpm install stamp

This will install the Stamp Plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/stamp`.

### Manual Installation

To install the plugin manually, download the zip-version of this repository and unzip it under `/your/site/grav/user/plugins`. Then rename the folder to `stamp`. You can find these files on [GitHub](https://github.com/petira/grav-plugin-stamp) or via [GetGrav.org](https://getgrav.org/downloads/plugins).

You should now have all the plugin files under

    /your/site/grav/user/plugins/stamp

> NOTE: This plugin is a modular component for Grav which may require other plugins to operate, please see its [blueprints.yaml file on GitHub](https://github.com/petira/grav-plugin-stamp/blob/develop/blueprints.yaml).

### Admin Plugin

If you use the Admin Plugin, you can install the plugin directly by browsing the `Plugins` menu and clicking on the `Add` button.

## Configuration

Before configuring this plugin, you should copy the `/user/plugins/stamp/stamp.yaml` to `/user/config/plugins/stamp.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
```

Note that if you use the Admin Plugin, a file with your configuration named `stamp.yaml` will be saved in the `/user/config/plugins` folder once the configuration is saved in the Admin.

## Usage

### Automatic generation of variables related to the page modification

The primary purpose of this plugin is to automatically generate variables related to the page modification:

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

#### Name format

Since **Stamp v1.0.3** you can choose between using a full name and a username in the **Name format** (`name`) variable in the `stamp.yaml`. The default option is the **Full name** (`full`) for backward compatibility, but I recommend changing it to the **Username** (`user`), which can be further processed:

```
{% if page.find('/author/' ~ page.header.author) %}
    <i class="fa fa-user"></i> <a href="/author/{{ page.header.author }}">{{ page.find('/author/' ~ page.header.author).title|e }}</a>
{% else %}
    <i class="fa fa-user"></i> {{ page.header.author }}
{% endif %}
```

However, this example can also be used for an `editor`, or it can be combined with the above example for `revision`. It is not necessary to use only **pages**, but also **Flex Objects** or other repositories with details about the **users** (`author` or `editor`).

#### Taxonomy type of `author`

Since **Stamp v1.0.4** support for taxonomy type of `author` has been added, so it is now possible to save **multiple authors** automatically:

`taxonomy.author` - combines the advantages of `author` and `editor` fields in taxonomy

It is necessary to add the taxonomy type of `author` in the `site.yaml` file:

```
taxonomies: [category, tag, author]
```

The following rules apply:

* In all actions, all duplicate values are removed. In the **None** action, duplicate values are also removed. This is a precautionary measure when saving in **Expert mode**.
* The active user aka `editor` is always added as the `author` to the desired location in the array, except **None** and **Current alphabetically** actions.
* In the **Editor only** action, all values will be replaced by the name of the active user aka `editor`!

Specific actions:

1. **None** - everything remains completely unchanged, except for the removal of duplicate values (**Expert mode** does not check for duplicates),
2. **Editor only** - adds only the active user, deletes all other users,
3. **Current alphabetically** - sorts current users alphabetically, does not solve the active user,
4. **All alphabetically** - adds the active user, sorts all users alphabetically,
5. **Editor first, others unchanged** - adds the active user to the beginning, leaves the other users unchanged,
6. **Editor last, others unchanged** - adds the active user to the end, leaves the other users unchanged,
7. **Editor first, others alphabetically** - adds the active user to the beginning, sorts the other users alphabetically,
8. **Editor last, others alphabetically** - adds the active user to the end, sorts the other users alphabetically.

If necessary, you can override page-level behavior through the `taxonomy_author` variable. The following values are allowed: `none`, `editor_only`, `current_alphabetically`, `all_alphabetically`, `editor_first_others_unchanged`, `editor_last_others_unchanged`, `editor_first_others_alphabetically` and `editor_last_others_alphabetically`.

#### Twig variable `users`

Since **Stamp v1.0.5** there is a Twig variable `users` which contains data from user accounts, specifically from the `/user/accounts` folder and the `*.yaml` files it contains. For now, only the variables `user.username`, `user.fullname`, `user.title` and `user.email` are available, which can be obtained from the `users` area in this way, for example:

```
{% set control = false %}
{% for user in users %}
    {% if page.header.author == user.username %}
        <i class="fa fa-user"></i> {{ user.fullname|e }} <i class="fa fa-users"></i> {{ user.title|e }} <i class="fa fa-envelope"></i> <a href="mailto:{{ user.email|e }}">{{ user.email|e }}</a>
        {% set control = true %}
    {% endif %}
{% endfor %}
{% if control == false %}
    <i class="fa fa-user"></i> {{ page.header.author }}
{% endif %}
```

**Important!** For a Twig variable `users` to be available, the `Twig variables` button must be active, or the `twig` variable in the `stamp.yaml` must be set to `true` (default is `false`).

Keep in mind that the Twig variable `users` contains information about all registered users, so when combined with `taxonomy.author`, information about multiple users can be obtained at once.

### Able to add or modify `date` and `author` variables when the page is first saved

The secondary purpose of this plugin is to be able to add or modify `date` and `author` variables **when the page is first saved via the Admin Plugin**. The Stamp Plugin can replace previous plugins (Auto Date Plugin and Auto Author Plugin), but it behaves differently, which must be taken into account. The main difference is that previous plugins generate variables when the page is created, and this plugin generates variables when the page is saved. If the date is to match the first save, using this plugin is the right choice.

Meaning of variables:

* `date` - the date is one of the main variables of the Grav, but nowhere is it specified what the date means. This can be the date the page was created, the date the page was first saved, but also the date the page was first published (or a completely different date - past, present or future). This flexibility is completely unique and perfect.
* `author` - the author is a completely user variable. This can be the author of the page content, but also only the first page editor who has nothing to do with the page content.

It is therefore important to determine exactly what these values mean.

**When the page is first saved via the Admin Plugin**, there are the following options for these variables in the `stamp.yaml`:

#### Date action
* if the `date` does not exist and the **Add** option is selected in the plugin settings, the `date` is added. The `date` is the same as the first `date_modified`.
* if the the **Modify** option is selected in the plugin settings, the `date` is always modified (if the `date` does not exist, it will be added). The `date` is the same as the first `date_modified`.

#### Author action
* if the `author` does not exist and the **Add** option is selected in the plugin settings, the `author` is added. The `author` is the same as the first `editor`.
* if the the **Modify** option is selected in the plugin settings, the `author` is always modified (if the `author` does not exist, it will be added). The `author` is the same as the first `editor`.

### Modifying the value of the `revision` variable

There are many reasons to modify the value of the `revision` variable. For example, if the page has been re-saved with only minor adjustments, then it is certainly not necessary for those adjustments to take effect in the `revision` value. Therefore, it is possible to modify the value of the `revision` variable to the last known one before editing and the page must be saved again.

If it is necessary to set the value of the `revision` variable to 0 to simulate the original version of the page (for example, to hide modified values in the twig template, or simply to restart numbering), the value of the `revision` variable must be adjusted to `-1`, `null`, or `reset`, and the page must be saved again. Frankly, this can now be achieved with any value other than an integer of `0` or higher and a special value named `new`. Alternatively, the `revision` variable can also be completely deleted.

The `revision` variable can be modified via expert mode (control value) or directly in the `.md` file (final value).

### Simulate the behavior as when the page is first saved

In practice, there are many cases where it is necessary to repeatedly save the page as a draft and publish only the final version. For this reason, it is possible to overwrite the values of the `date` and `author` variables by setting the value `new` to the `revision` variable. To prevent accidental overwriting, **Override behavior** (`override`) variable must also be enabled in the plugin settings. Otherwise, a value of `new` will only set the `revision` variable to `0`, just like `null` in the previous setting.

Other words, if the value of the `revision` variable is set to `new` and the `override` variable in the `stamp.yaml` is set to `true` (default is `false`), **it provides the same behavior as when the page is first saved via the Grav Admin Plugin**.

## Recommendation!

It is recommended that you add the value of seconds to the `dateformat.default` variable in the `system.yaml` file for accurate time determination:

```
dateformat:
  default: 'Y-m-d H:i:s'
```

## Credits

[Grav.cz](https://www.grav.cz) - Czech portal about [Grav CMS](https://github.com/getgrav/grav) containing lots of instructions and tips

[Timestamp Plugin](https://github.com/petira/grav-plugin-timestamp) - plugin for [Grav CMS](https://github.com/getgrav/grav) written by the same [author](https://github.com/petira)

## To Do

- [x] Add support for taxonomy type of `author`
- [ ] Add the optional ability to automatically modify the parent page (usually a `modular` page)
- [x] Add new variables to Twig, containing complete information about registered users, or from other sources
- [ ] Translantions (after standard terms have been introduced)
- [ ] Sample templates
- [x] Standardize the contents of the `README.md` file
- [ ] Add option for modification only (now merged with addition), if necessary
- [ ] Add other variables, for example: `date_created`, `creator`, `date_accessed` (probably within a storage other than the frontmatter), `visitor` (probably within a storage other than the frontmatter), etc.
- [ ] Add alternative `stamp` variables: `stamp.created.on`, `stamp.created.by`, `stamp.modified.on`, `stamp.modified.by`, etc., if necessary
- [ ] Additional control values for the `revision` variable, for example: change the value of the `revision` variable without affecting the values of other variables
- [ ] All changes made through the `revision` variable will be conditional on enabling the `override` variable
