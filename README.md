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

## Captcha

Place this Twig tag that renders the initialization scripts:

```twig
{{ captcha_script() }}
```

Place the default config in `config/packages/oh_media_antispam.yaml`:

```yaml
oh_media_antispam:
    captcha:
```

Under `captcha` you can specify `type` as "hcaptcha" or "recaptcha" (default).

_**Note:** `sitekey` and `secretkey` are omitted in the default config because the bundle
will provide test keys._


Override on the live site with `config/packages/prod/oh_media_antispam.yaml`:

```yaml
oh_media_antispam:
    captcha:
        type: 'recaptcha' # or 'hcaptcha'
        sitekey: 'my_publishable_key'
        secretkey: 'my_secret_key'
```

You will want to ignore this prod file in your repository.

Add the field to a form:

```php
use OHMedia\AntispamBundle\Form\Type\CaptchaType;

//...
$builder->add('captcha', CaptchaType::class);
```

The validation will happen automatically.

### Custom Captcha

If you need more from Captcha (like resetting after a JS submit) or you need
to custom render it, you can initialize it yourself like so:

```js
const captcha = await OHMEDIA_ANTISPAM_CAPTCHA_PROMISE(container, parameters);

// call "captcha.getResponse()" to populate a hidden input / posted data
// call "captcha.reset()" to make the user redo the challenge
```
