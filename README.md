# Stamp Plugin

The Grav **Stamp** Plugin is designed for [Grav CMS](http://github.com/getgrav/grav) and automatically adds or changes `date_modified`, `editor` and `revision` to frontmatter when a page is saved via the Grav Admin Plugin. 

## Description

The plugin extends and complements the [Auto Date Plugin](https://github.com/getgrav/grav-plugin-auto-date) and the [Auto Author Plugin](https://github.com/naucon/grav-plugin-auto-author) (wrong link, plugin is [here](https://github.com/naucon/grav-auto-author)), but is not dependent on them and works completely independently.

**The primary purpose** of this plugin is to automatically generate variables related to page modification:

* `date_modified` - the date the page was last saved
* `editor` - the user who last saved the page
* `revision` - the number of page revisions (0 means original, 1 means first revision, etc.)
 
The `revision` variable serves several purposes. It primarily shows how many page revisions have been made. However, it can also be used in a twig template to display variables related to the modification:

```
Published <i class="fa fa-calendar"></i> {{ page.date|date(system.pages.dateformat.default) }}
by <i class="fa fa-user"></i> {{ page.header.author }}
{% if page.header.revision %}
    | Modified <i class="fa fa-calendar"></i> {{ page.header.date_updated|date(system.pages.dateformat.default) }}
    by <i class="fa fa-user"></i> {{ page.header.editor }}
    | Revision <i class="fa fa-save"></i> {{ page.header.revision }}
{% endif %}
```

The `revision` variable can be modified via expert mode or directly in the `.md` file. If necessary, the `revision` variable is reset by setting the value to -1, null, or any value other than an integer value of 0 or higher. Alternatively, the `revision` variable can also be completely deleted.

**The secondary purpose** of this plugin is to be able to add or override `date` and `author` variables when the page is first saved via the Admin Plugin. The Stamp Plugin can replace previous plugins, but it behaves differently, which must be taken into account. The main difference is that previous plugins generate variables when the page is created, and this plugin generates variables when the page is saved. If the date is to match the first save, using this plugin is the right choice.

### Warning!

If the `override` variable in the `stamp.yaml` is set to `true` and the `revision` variable in the frontmatter is not set, `date` and `author` variables in the frontmatter are automatically added or overriden without warning.


---

### Recommendation!

It is recommended that you add the value of seconds to the `dateformat.default` variable in the `site.yaml` file for accurate time determination. It must be done directly in the `system.yaml` file, it is not possible via the Admin Plugin. The value must not be subsequently changed via the Admin Plugin.

```
dateformat:
  default: 'H:i:s d-m-Y'
```
