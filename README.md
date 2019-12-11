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

```php
    'preset-name' => [
        // Path where the source images are stored
        // Must be absolute and web-accessible -> @webroot
        // Will be used to bulk-generate via console command
        // Required
        'srcPath'         => '@webroot/img/some_path',

        // Path where the thumbnails should be stored
        // Must be absolute and web-accessible -> @webroot
        // If not set, component's defaultTargetPath will be used
        // Optional
        'targetPath'      => '@webroot/img/some_path/preset-name',

        // File extension of the thumbnails
        // If not set, thumbnail will have the same extension as source file
        // Optional
        'targetExtension' => 'jpg',

        // Thumbnail width and height in pixels
        // At least one of them is required
        'width'           => 480,
        'height'          => 400,

        // Image quality in percent
        // Optional
        'quality'         => 80,

        // Viewport breakpoints for the Picture widget
        // Thumbnails gets only displayed whitin this breakpoint (min and/or max)
        // At least one of them is required
        'breakpointMin'   => 992,
        'breakpointMax'   => 1200,
    ],
```

### Using the picture widget

Just set the source image and the presets you want to use.

**Important:**
* Path of the source image must be an alias and web-accessible, so either `@web` or `@webroot`
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

The console command is intended to generate or flush thumbnails for all or just a single preset. If there are no thumbnails, they will be generated on demand (not recommended).

To use it, you need to add the same config to `console.php` as you did in `web.php`. Therefore it is recommended, that you place your presets into `params.php` to keep things clean and consistent.

Additionally, to be able to call the command, define the controller in the controller map:

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
```

Also, you need to define the aliases `@web` and `@webroot` in the `yii` file:

```php
    // Put this after the require of the Yii.php
    Yii::setAlias('@webroot', __DIR__ . '/web');
    Yii::setAlias('@web', '/');
```
