# ACF Date and Time Picker Polyfill

Advanced Custom Fields Pro version 5.3.9 comes with a new field type called DateTime Picker. The functionality of this field type has previously been solved by an addon called ACF Date and Time Picker Field - represented on [github](https://github.com/soderlind/acf-field-date-time-picker) and [the Wordpress Plugin Repository](https://da.wordpress.org/plugins/acf-field-date-time-picker/).

If your Wordpress installation is using this plugin, it will break when updating to ACF Pro 5.3.9 due to a naming comflict between the two field types. Besides from the breaking change, there are some fundamental differences between the two plugins resulting existing datetime fields resetting when edited and different behavior when querying the database.

This polyfill serves to temporarily resolve these conflicts by making the now native date time picker behave as the add-on to avoid loss of data and breaking of sites.

The plugin automatically deactivates the date-time-picker add-on if ACF Pro version 5.3.9 or greater is detected. It also changes the way date time fields are saved to immitate the add-on behavior, while still not breaking the native date time picker.

Beware that the date time picker addon supports only displaying a time picker which is not an option in the native date time picker. This issue is not addressed in this plugin

The plugin is created based on our own issues with having live sites using the add-on so use at your own risk. :-)
