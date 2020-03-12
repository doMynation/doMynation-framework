---
description: Getting started with the doMynaton framework.
---

# Getting Started

## TL;DR Version

To get started quickly, clone the [starter project](https://github.com/domynation/domynation-starter).

```bash
# Clone the repo
git clone https://github.com/doMynation/doMynation-starter.git

# Install dependencies
composer install
```

Once you're done, head over to the [Module](routing.md) section to create your first module.

## Longer Version

### Install the Framework

The first step is to clone the repository.

```bash
git clone https://github.com/doMynation/doMynation-framework.git
```

You'll then need to create the following directory structure and files:

```text
/config
  - application.php  # Your app configuration
/public
  - index.php        # The entry point to your app
  - .htaccess        # Only applicable if you use Apache
/storage
  /files             # Default location for uploaded files
    - .gitkeep
  /logs              # Default location for logs
    - .gitkeep
```

### Prepare the Entry Point to Your App

Open `/public/index.php` and paste the following code in it.

```php
// Include composer dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Create an application instance with the path corresponding to the route of your project
$app = new Domynation\Application(__DIR__ . '/../');

// Boot the application
$app->boot();

// Execute the request
$app->run();
```

### Configure Your App

The next step is to configure your app through `/config/application.php`. For an example along with an explanation of each setting, see the [sample file](https://github.com/doMynation/doMynation-framework/blob/master/src/application.sample.php) in the main repository.

