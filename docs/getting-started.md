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

## Longer Version

### Install and Setup

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
  /files
    - .gitkeep
  /logs
    - .gitkeep
```

### Prepare The Entry To Your App

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

