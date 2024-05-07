# Installation

Update `composer.json` by adding this to the `repositories` array:

```json
{
    "type": "vcs",
    "url": "https://github.com/ohmediaorg/accordion-bundle"
}
```

Then run `composer require ohmediaorg/accordion-bundle:dev-main`.

Import the routes in `config/routes.yaml`:

```yaml
oh_media_accordion:
    resource: '@OHMediaAccordionBundle/config/routes.yaml'
```

Run `php bin/console make:migration` then run the subsequent migration.

# Frontend

The bundle includes templates that output the Accordions/FAQs using Bootstrap's
Accordion component.

If custom output is needed, override the following templates:

1. `templates/bundles/OHMediaAccordionBundle/accordion.html.twig`
1. `templates/bundles/OHMediaAccordionBundle/faq.html.twig`
