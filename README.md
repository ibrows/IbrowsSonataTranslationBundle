# SonataTranslationBundle


The SonataTranslationBundle is an integration of the LexikTranslationBundle into the SonataAdminBundle.

![image](https://raw.github.com/ibrows/IbrowsSonataTranslationBundle/master/Resources/doc/screen/overview.png)

## Installation

Add the following lines in your composer.json:

```
{
    "require": {
		"ibrows/sonata-translation-bundle" : "dev-master",
    }
}
```


To start using the bundle, register the SonataTranslationBundle and LexikTranslationBundle in your application's kernel class:

``` 
<php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
		new Lexik\Bundle\TranslationBundle\LexikTranslationBundle(),
		new Ibrows\SonataTranslationBundle\IbrowsSonataTranslationBundle(),
    );
)
```

## Configuration

#### SonataTranslationBundle
No initial configuration is necessary.

However there are some options you can configure in order to edit the look. Below the default configuration is shown:

```
# app/config/config.yml
ibrows_sonata_translation:
    defaultDomain: messages
	editable:
    	mode: inline		 # Mode of editable, can be popup or inline
		type: textarea		 # Type of input. Can be text|textarea|select|date|checklist and more
		emptytext: Empty 	 # text to display on empty translations
		placement: top		 # ingnored for inline mode
```

#### LexikTranslationBundle

The minimal configuration consists of:

```
# app/config/config.yml
lexik_translation:
    fallback_locale: en      # (required) default locale to use
    managed_locales: [en]    # (required) locales that the bundle have to manage
```

For additional configuration options please refer to the official [documentation](https://github.com/lexik/LexikTranslationBundle/blob/master/Resources/doc/index.md#configuration)

In order to have some keys shown up in the admin panel, you have to import the translation files first. This is done through a command offered by LexikTranslationBundle:

```
./app/console lexik:translations:import [bundleName] [--cache-clear] [--force] [--globals]

```
Once again refer to the official [documentation](https://github.com/lexik/LexikTranslationBundle/blob/master/Resources/doc/index.md#import-translations) for additional information.


## Usage

To include the bundle in the admin dashboard, add group "group.translation" to dashboard

```
# app/config/config.yml
sonata_admin:
    dashboard:
        groups:
            group.translation:
                label: Translation
                items: ~ 
```


 There really is not more to say about it ;).
