Overview
========

Offers a few methods for anti-spam in forms.

Installation
------------

Enable the bundle in `config/bundles.php`:

```php
return [
    // ...
    OHMedia\AntispamBundle\OHMediaAntispamBundle::class => ['all' => true],
];
```


Honeypot
--------

If you want to enable it on a particular form, set the `honeypot_protection`
option to `true`. Validation will happen automatically.

Let the default form rendering handle the output of the honeypot fields to avoid
issues.

reCAPTCHA
---------

Include `<script src="https://www.google.com/recaptcha/api.js" async defer></script>`
on the appropriate pages.

Place the test keys in `config/dev/oh_media_antispam.yaml`
and the live keys in `config/prod/oh_media_antispam.yaml`:

```yaml
oh_media_antispam:
    recaptcha:
        sitekey: 'my_publishable_key'
        secretkey: 'my_secret_key'
```

Add the field to a form:

```php
use OHMedia\AntispamBundle\Form\Type\RecaptchaType;

//...
$builder->add('recaptcha', RecaptchaType::class);
```

The validation will happen automatically.
