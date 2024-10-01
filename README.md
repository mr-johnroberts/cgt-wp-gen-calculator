# CGT Generator Calculator

**CGT Generator Calculator** is a WordPress plugin that allows users to easily calculate Capital Gains Tax (CGT). It provides an easy-to-use interface for site visitors to input necessary data and get the calculated CGT. The plugin can be customized via the WordPress admin dashboard and integrated into posts and pages via shortcodes.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Shortcodes](#shortcodes)
- [Customization](#customization)
- [Contributing](#contributing)
- [Changelog](#changelog)
- [License](#license)

## Features

- Easy integration via shortcode.
- Responsive design that fits any theme.
- Customizable UI through CSS.
- Backend options for setting custom tax rules and calculations.
- Real-time result updates with AJAX.

## Installation

### From the WordPress Plugin Repository

1. Go to your WordPress admin dashboard.
2. Navigate to **Plugins** > **Add New**.
3. Search for `CGT Generator Calculator`.
4. Click **Install** and then **Activate**.

### Manually via FTP

1. Download the plugin zip file from the [Releases page](https://github.com/mr-johnroberts/cgt-wp-gen-calculator/releases).
2. Unzip the file.
3. Upload the unzipped folder to `/wp-content/plugins/` on your WordPress site.
4. Go to the WordPress dashboard and activate the plugin under **Plugins**.

## Usage

Once installed and activated, the plugin can be added to any page or post using the following shortcode:

```plaintext
[cgt_calculator]

Example
plaintext
Copy code
[cgt_calculator title="Generator Purchase Calculator"]
You can also customize the title and parameters of the calculator using the available attributes.

Shortcodes
[cgt_calculator]: Renders the CGT calculator form on any page or post.
Available Parameters:
title (string): The title of the calculator. The default is "CGT Calculator".
Customization
CSS Customization
You can modify the styling of the calculator by overriding the plugin's CSS. The default styles are loaded from:

plaintext
Copy code
/public/assets/css/cgt-generator-calculator-style.css
To ensure the latest version of the CSS is always loaded (bypassing cache), a dynamic versioning system has been implemented using filemtime():

php
Copy code
$css_file = CGT_GENERATOR_CALCULATOR_PATH . 'public/assets/css/cgt-generator-calculator-style.css';
$css_version = filemtime($css_file);
wp_enqueue_style('cgt-generator-calculator-style', CGT_GENERATOR_CALCULATOR_URL . 'public/assets/css/cgt-generator-calculator-style.css', [], $css_version, 'all');
This ensures the CSS will update whenever you modify the file.

JavaScript
AJAX-based form submissions ensure that the calculator functions smoothly without reloading the page. The JavaScript file is loaded from:

plaintext
Copy code
/public/assets/js/cgt-generator-calculator.js
You can enqueue your custom scripts and styles to override default behavior.

Contributing
We welcome contributions to this plugin! To contribute:

Fork this repository:
bash
Copy code
git clone https://github.com/mr-johnroberts/cgt-wp-gen-calculator.git
Create a new feature branch: git checkout -b feature/my-feature.
Make your changes.
Commit your changes: git commit -m 'Add some feature'.
Push to the branch: git push origin feature/my-feature.
Submit a pull request.
For major changes, please open an issue first to discuss what you'd like to change.

Development Setup
Clone the repository:
bash
Copy code
git clone https://github.com/mr-johnroberts/cgt-wp-gen-calculator.git
Navigate to the plugin folder:
bash
Copy code
cd cgt-wp-gen-calculator
Run local development with a tool like Local by Flywheel or WP CLI.

Changelog
Version 1.0.0
- Initial release of CGT Generator Calculator.
- Features include dynamic CGT calculations, AJAX-based result fetching, and customizable CSS/JS.

License
This plugin is licensed under the MIT License. See the LICENSE file for more information.
