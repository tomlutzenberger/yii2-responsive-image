<?php

/**
 * @noinspection PhpUnused
 * @noinspection UnknownInspectionInspection
 */

namespace TomLutzenberger\ResponsiveImage\widgets;


use Yii;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Class Picture
 *
 * @package   TomLutzenberger\ResponsiveImage\widgets
 * @copyright 2019 Tom Lutzenberger
 * @author    Tom Lutzenberger <lutzenbergertom@gmail.com>
 */
class Picture extends Widget
{
    /**
     * @var string[]
     */
    public $presets = [];

    /**
     * @var array
     */
    public $pictureOptions = [];

    /**
     * @var array
     */
    public $imageOptions = [];

    /**
     * @var string
     */
    public $image;


    /**
     * {@inheritdoc}
     * @throws \yii\base\ErrorException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        /** @var \TomLutzenberger\ResponsiveImage\components\ResponsiveImage $ri */
        /** @noinspection PhpUndefinedFieldInspection */
        $ri = Yii::$app->responsiveImage;
        $sources = '';

        if (!isset($this->image) || count($this->presets) === 0) {
            return;
        }

        foreach ($this->presets as $presetName) {
            $thumbnail = $ri->getThumbnail($this->image, $presetName);
            $preset = $ri->getPreset($presetName);
            $media = [];

            if ($preset->breakpointMin > -1) {
                $media[] = sprintf('(min-width: %dpx)', $preset->breakpointMin);
            }
            if ($preset->breakpointMax > -1) {
                $media[] = sprintf('(max-width: %dpx)', $preset->breakpointMax);
            }


            if ($thumbnail === null) {
                continue;
            }

            $srcset = $thumbnail;
            $thumbUrlParts = explode('?', $thumbnail);
            $pathInfo = pathinfo(parse_url($thumbnail)['path']);
            if (!empty($preset->pixelDensity)) {
              foreach($preset->pixelDensity as $pixelDensity) {
                $thumb = $pathInfo['dirname']
                    . '/'
                    . $pathInfo['filename']
                    . '-' . $pixelDensity
                    . 'x.'
                    . $pathInfo['extension'];
                $srcset .= ', ' . $thumb . ($thumbUrlParts[1] ? '?' . $thumbUrlParts[1] : '') . ' ' . $pixelDensity . 'x';
              }
            }
            $sources .= Html::tag('source', '', [
                'srcset' => $srcset,
                'media'  => implode(' AND ', $media),
            ]);
        }

        echo Html::beginTag('picture', $this->pictureOptions);
        echo $sources;
        if ($ri->cacheBustingEnabled && $ri->getAbsolutePath($this->image)) {
          $url = $this->image . '?v=' . filemtime($ri->getAbsolutePath($this->image));
        } else {
            $url = $this->image;
        }
        echo Html::img(Yii::getAlias($url), $this->imageOptions);
        echo Html::endTag('picture');
    }
}
