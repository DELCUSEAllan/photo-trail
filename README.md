# Photo Trail

Photo Trail is a lightweight WordPress plugin that creates an interactive image trail effect following the user's cursor.

The plugin automatically loads images from a server directory and displays them dynamically as visitors move their mouse across a page.

The project was originally developed by Allan Delcuse during a web development internship carried out with Proximale and the COOPNUM (Coopérative Numérique des 7 Vallées).

## Features

* Interactive image trail effect
* Random image selection
* Dedicated WordPress settings page
* Configurable target page
* Configurable image folder
* Adjustable animation duration
* Adjustable image size
* Mobile-friendly (disabled on small screens)
* Lightweight and easy to use

## Installation

1. Upload the plugin to the `/wp-content/plugins/` directory.
2. Activate the plugin through the WordPress administration panel.
3. Create a folder containing your images.
4. Open **Settings > Photo Trail**.
5. Configure:

   * Target page slug
   * Image folder
   * Animation settings
6. Save your settings.

## Example Configuration

Target page slug:

`pele-mele`

Image folder:

`coopnum/photo-trail/images`

## Folder Structure

```text
wp-content/
└── coopnum/
    └── photo-trail/
        └── images/
```

## Requirements

* WordPress 6.0+
* PHP 7.4+

## Author

Allan Delcuse

## License

GPL v2 or later.
