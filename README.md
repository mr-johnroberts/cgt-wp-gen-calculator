# **cgt-wp-gen-calculator**
CGT WP Gen Calculator is a custom WordPress-Woocommerce Generator Calculator Plugin built on JavaScript, CSS3, HTML5, WordPress, WooCommerce, AWS, and PHP. It determines a property's total wattage and lists the available generators within that range so that users can select the right generator to power their property during power outages.

# **Technology Stack:** 
JavaScript, CSS3, HTML5, PHP, WordPress, MySQL, AWS, and WooCommerce REST API.

# **Purpose:** 
Determines the total wattage of a property and lists compatible generators based on the calculated wattage range.

# **Functionality:**
-	Provides users with an intuitive interface to calculate their property’s wattage requirements.
-	Displays a curated list of generators from the WooCommerce product database matching the wattage range.

# **Integration:** 
Connects seamlessly with the WooCommerce REST API to retrieve and display product information in real-time.

# **Impact:** 
Helps users select appropriate generators during power outages, enhancing decision-making and user satisfaction.

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
- Backend options are available to set the custom appliance fields and default wattages.
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
