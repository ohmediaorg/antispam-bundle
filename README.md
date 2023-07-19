### Overview

Offers a few methods for anti-spam in forms.

## Installation

Enable the bundle in `config/bundles.php`:

```php
return [
    // ...
    OHMedia\AntispamBundle\OHMediaAntispamBundle::class => ['all' => true],
];
```


## Honeypot

If you want to enable it on a particular form, set the `honeypot_protection`
option to `true`. Validation will happen automatically.

```php
$formBuilder = $this->createFormBuilder(null, [
    'honeypot_protection' => true
]);
```

Let the default form rendering handle the output of the honeypot fields to avoid
issues.

## reCAPTCHA

Place this Twig tag that renders the initialization scripts:

```twig
{{ recaptcha_script() }}
```

Place the default config in `config/packages/oh_media_antispam.yaml`:

```yaml
oh_media_antispam:
    recaptcha:
```

_**Note:** `sitekey` and `secretkey` are omitted in the default config because the bundle
will provide the test reCAPTCHA keys by default._


Override on the live site with `config/packages/prod/oh_media_antispam.yaml`:

```yaml
oh_media_antispam:
    recaptcha:
        sitekey: 'my_publishable_key'
        secretkey: 'my_secret_key'
```

You will want to ignore this prod file in your repository.

Add the field to a form:

```php
use OHMedia\AntispamBundle\Form\Type\RecaptchaType;

//...
$builder->add('recaptcha', RecaptchaType::class);
```

The validation will happen automatically.

### Custom reCAPTCHA

If you need more from reCAPTCHA (like resetting after a JS submit) or you need
to custom render it, you can initialize it yourself like so:

```js
const recaptcha = await ohmedia_antispam_bundle_recaptcha_promise(container, parameters);

// call "recaptcha.getResponse()" to populate a hidden input / posted data
// call "recaptcha.reset()" to make the user redo the challenge
```
