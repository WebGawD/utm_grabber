# SearchKings Africa UTM Grabber

**Plugin Name:** SearchKings Africa UTM Grabber  
**Description:** A WordPress plugin that dynamically updates links with UTM parameters, populates form fields with UTM data, and detects traffic channel and source.  
**Version: 1.0.3**
**Author:** SearchKings Africa  
**License:** MIT  

## Description

The SearchKings Africa UTM Grabber plugin enhances your WordPress site's tracking capabilities. It ensures that UTM parameters persist as users navigate through your site, populates form fields with UTM data, and detects whether the traffic is from paid search or organic sources. This plugin is particularly useful for tracking marketing campaigns and maintaining consistent UTM tracking for analytics.

## Features

- Stores UTM parameters in session storage to persist them across page navigations.
- Populates form fields with UTM data.
- Detects and populates form fields with traffic channel (Paid Search/Organic) and source (Google/Bing/Yahoo).
- Compatible with standard WordPress forms and Elementor forms.

## Installation

1. **Upload the Plugin:**
   - Download the plugin ZIP file.
   - Navigate to `Plugins > Add New` in your WordPress admin dashboard.
   - Click `Upload Plugin` and select the downloaded ZIP file.
   - Click `Install Now` and then `Activate`.

2. **Manual Installation:**
   - Unzip the downloaded plugin file.
   - Upload the `sk-utm-grabber-plugin` directory to the `/wp-content/plugins/` directory.
   - Activate the plugin through the `Plugins` menu in WordPress.

## Usage

### Form Integration

The plugin populates form fields with UTM data and traffic channel/source information. For this to work, you must add specific hidden fields to your forms.

#### Required Hidden Fields

Add the following hidden fields to your forms:

1. utm_source
2. utm_medium
3. utm_campaign
4. utm_term
5. utm_content
6. device
7. network
8. placement
9. adposition
10. gclid
11. channel
12. source

Example for standard WordPress forms:
```html
<form>
    <!-- Your existing form fields -->
    <input type="hidden" name="utm_id" value="">
    <input type="hidden" name="utm_source" value="">
    <input type="hidden" name="utm_medium" value="">
    <!-- Add all other UTM fields similarly -->
    <input type="hidden" name="channel" value="">
    <input type="hidden" name="source" value="">
    <!-- Your form submit button -->
</form>
```

For Elementor forms:
1. Add a new field to your form
2. Set the field type to "Hidden"
3. Set the field ID to match the required field names (e.g., "utm_source", "channel", "source")

The plugin will automatically populate these hidden fields with UTM data from the URL or session storage, and detect the traffic channel and source.

## Operating Procedures

1. **Form Integration:**
   - Add hidden input fields to your forms for each of the UTM parameters listed above, plus "channel" and "source".
   - Ensure the field names match exactly with the supported parameters.
   - The plugin will automatically populate these hidden fields with UTM data from the URL or session storage, and detect the traffic channel and source.
   - No additional configuration is needed for this feature to work once the fields are added.

2. **Troubleshooting:**
   - If UTM parameters or channel/source information are not being added to forms, check if the form fields with correct names exist.
   - Ensure JavaScript is enabled in the browser.
   - Check the browser console for any error messages related to the plugin.
   - Verify that the UTM parameters are present in the URL or session storage.

## Changelog


## Frequently Asked Questions

**Q1: Do I need to modify my forms to include UTM parameters and channel/source information?**  
A1: Yes, you need to add hidden input fields to your forms for each of the UTM parameters, plus "channel" and "source". The plugin will then automatically populate these fields with UTM data and detected channel/source information.

**Q2: What fields should I add to my forms?**  
A2: You should add hidden fields for the following parameters: utm_source, utm_medium, utm_campaign, utm_term, utm_content, device, network, placement, adposition, gclid channel, and source.

**Q3: How do I add these fields to my forms?**  
A3: Add hidden input fields to your form HTML. For example:
```html
<input type="hidden" name="utm_source" value="">
<input type="hidden" name="channel" value="">
<input type="hidden" name="source" value="">
```
Repeat this for each parameter, changing the name attribute accordingly.

**Q4: How does the plugin determine if traffic is from paid search or organic?**  
A4: The plugin checks the utm_source parameter for known search engine names (Google, Bing, Yahoo) and the presence of a gclid parameter. It also checks if the utm_medium contains 'cpc'. If any of these conditions are met, it's considered paid search; otherwise, it's organic.

**Q5: Is this plugin compatible with Elementor forms?**  
A5: Yes, the plugin is compatible with both standard WordPress forms and Elementor forms. For Elementor forms, make sure to add hidden fields with the correct field IDs as described in the Form Integration section of this README.

## License

This plugin is licensed under the MIT License. See the LICENSE file for more details.

## Support

For support and further information, please contact SearchKings Africa.

