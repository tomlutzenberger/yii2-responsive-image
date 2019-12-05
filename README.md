# Yii2 Responsive Image

Create thumbnails with custom presets and use them in responsive widgets

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist tomlutzenberger/yii2-responsive-image "*"
```

or add

```
"tomlutzenberger/yii2-responsive-image": "*"
```

to the require section of your `composer.json` file.


## Usage

Once the extension is installed, add the component to your `web.php`:

```php
    // ...
    'components' => [
        // ...
        'responsiveImage' => [
            'class'   => 'TomLutzenberger\ResponsiveImage\components\ResponsiveImage',
            'presets' => [
                // Your presets here
            ],
        ],
    ],
```

### Defining a preset

A preset is like a template for both the thumbnail generator and the picture widget.

TODO: Complete this

### Using the picture widget

Just set the source image and the presets you want to use.

**Important:**
* Path of the source image must be an alias and web-accessible, so either `@web`
or `@webroot`
* Path of the source image need to match `srcPath` of the preset

```php
<?= TomLutzenberger\ResponsiveImage\widgets\Picture::widget([
    'image'   => '@web/image/content/my-image.jpg',
    'presets' => [
        'content-xs',
        'content-sm',
        'content-md',
        'content-lg',
        'content-xl',
    ],
]) ?>
```

You may als set `pictureOptions` and `imageOptions` depending on your needs.

### Using the console command

The console command is intended to generate or flush thumbnails for all or just 
a single preset. If there are no thumbnails, they will be generated on demand
(not recommended).

To use it, you need to add the same config to `console.php` as you did in 
`web.php`. Therefore it is recommended, that you place your presets into
`params.php` to keep things clean and consistent.
Additionally, to be able to call the command, define the controller 
in the controller map:

```php
    // ...
    'components' => [
        // ...
        'responsiveImage' => [
            // ...
        ],
    ],
    'controllerMap'       => [
        // ...
        'image' => [
            'class' => 'TomLutzenberger\ResponsiveImage\commands\ImageController',
        ],
    ],
`````
