# ACF Date and Time Picker Polyfill

Advanced Custom Fields Pro version 5.3.9 comes with a new field type called DateTime Picker. The functionality of this field type has previously been solved by an addon called ACF Date and Time Picker Field - represented on [github](https://github.com/soderlind/acf-field-date-time-picker) and [the Wordpress Plugin Repository](https://da.wordpress.org/plugins/acf-field-date-time-picker/).

If your Wordpress installation is using this plugin, it will break when updating to ACF Pro 5.3.9 due to a naming comflict between the two field types. Besides from the breaking change, there are some fundamental differences between the two plugins resulting existing datetime fields resetting when edited and different behavior when querying the database.

This polyfill serves to temporarily resolve these conflicts by making the now native date time picker behave as the add-on to avoid loss of data and breaking of sites.

The plugin does the following:
* It automatically deactivates the date-time-picker add-on if ACF Pro version 5.3.9 or greater is detected
* It changes the way date time fields are saved to immitate the add-on behavior, while not breaking the native date time picker.
* It overrides "Display format" and "Return format" with the format previously specified with the add-on. (This essentially means that you cannot change date formats as the option is no longer available with the native field)

Installation instructions
-------------------------
The plugin should be installed as a mu-plugin in order for automatic disabling of the date time picker add-on to work.

Copy the plugin file to wp-content/mu-plugins

It can be installed as a regular plugin as well, but will not be able to auto disable the add-on. To install as a regular plugin simply upload the zip file through wordpress' "Add Plugin" interface.

Beware that the date time picker addon supported displaying a time picker only, which is no longer an option with the native date time picker. This issue is not addressed in this plugin

Please note that this plugin is created based on our own needs to handle live sites using the date-time-picker-add-on - so use it at your own risk. :-)
