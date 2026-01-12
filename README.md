# Quicksilver Autopilot Notification

This project was developed from a template for new Quicksilver projects to utilize so that 
Quicksilver scripts can be installed through Composer.

Original template: https://github.com/pantheon-quicksilver/quicksilver-template

Requirements:

- PHP 8.0 or higher
- Composer
- Drupal 9+ site running on Pantheon
- `autopilot_webhook_url` secret set via Terminus Secrets
- `autopilot_webook_token` secret set via Terminus Secrets
- [Autopilot Toolbar](https://www.drupal.org/project/pantheon_autopilot_toolbar) module installed


## Installation

This project is designed to be included from a site's `composer.json` file, and placed in its 
appropriate installation directory by [Composer Installers](https://github.com/composer/installers).

In order for this to work, you should have the following in your composer.json file:

```json
{
  "require": {
    "composer/installers": "^1"
  },
  "extra": {
    "installer-paths": {
      "web/private/scripts/quicksilver": ["type:quicksilver-script"]
    }
  }
}
```

Then, you can install this package via Composer:

`composer require university-of-denver/quicksilver-autopilot-notification:^1`

### Add to `pantheon.yml`

Here's what you need to add to your `pantheon.yml` file to run the Quicksilver script after an 
Autopilot visual regression test:

```yaml
api_version: 1

workflows:
  autopilot_vrt:
    after:
      - type: webphp
        description: Send VRT status to Drupal
        script: private/scripts/quicksilver/pantheon-quicksilver/autopilot-webhook.php
```

### Add secrets via Terminus

Follow the instructions in the 
[Terminus plugin docs](https://github.com/pantheon-systems/terminus-secrets-manager-plugin) to add 
the Terminus Secrets Manager plugin to your local machine.

You'll need to add the following secrets to your Pantheon site via Terminus:

```bash
terminus secret:site:set <site-name> autopilot_webhook_url <your-webhook-url>
terminus secret:site:set <site-name> autopilot_webhook_token <your-webhook-token>
```

### Pantheon Autopilot Toolbar Module

Please see the [Pantheon Autopilot Toolbar Module](https://www.drupal.org/project/pantheon_autopilot_toolbar) 
documentation for more information on how to install and configure the module.

This Quicksilver code will send a webhook that will trigger the Pantheon Autopilot Toolbar module to 
display a notification in the browser with the VRT status.