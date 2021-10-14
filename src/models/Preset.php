<?php /** @noinspection UnknownInspectionInspection */

namespace TomLutzenberger\ResponsiveImage\models;

use Yii;
use yii\base\Model;

/**
 * Class Preset
 *
 * @package   TomLutzenberger\ResponsiveImage\models
 * @copyright 2019 Tom Lutzenberger
 * @author    Tom Lutzenberger <lutzenbergertom@gmail.com>
 *
 * @property string $targetPath
 */
class Preset extends Model
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $srcPath;

    /**
     * @var string
     */
    public $targetExtension;

    /**
     * @var integer
     */
    public $width;

    /**
     * @var integer
     */
    public $height;

    /**
     * @var integer
     */
    public $quality;

    /**
     * @var integer
     */
    public $breakpointMax;

    /**
     * @var integer
     */
    public $breakpointMin;

    /**
     * @var boolean
     */
    public $cacheBusting = true;

    /**
     * @var string
     */
    protected $targetPath;

    /**
     * @var string
     */
    protected $targetPathResolved;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['name', 'srcPath', 'targetPath', 'targetExtension', 'width', 'height', 'quality', 'breakpointMax', 'breakpointMin', 'cacheBusting'], 'trim'],
            [['name', 'srcPath', 'targetPath', 'targetExtension', 'width', 'height', 'quality', 'cacheBusting'], 'default'],
            [['breakpointMax', 'breakpointMin'], 'default', 'value' => -1],

            [['name', 'srcPath'], 'required'],
            [['width'], 'required', 'when' => static function ($model) {
                return $model->height === 0;
            }],
            [['height'], 'required', 'when' => static function ($model) {
                return $model->width === 0;
            }],
            [['breakpointMax'], 'required', 'when' => static function ($model) {
                return $model->breakpointMin === -1;
            }],
            [['breakpointMin'], 'required', 'when' => static function ($model) {
                return $model->breakpointMax === -1;
            }],

            [['name', 'srcPath', 'targetPath', 'targetExtension'], 'string'],
            [['width', 'height', 'quality', 'breakpointMax', 'breakpointMin'], 'integer'],
            [['cacheBusting'], 'boolean'],
        ];
    }

    /**
     * Getter for $targetPath
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function getTargetPath()
    {
        if (isset($this->targetPathResolved)) {
            return $this->targetPathResolved;
        }

        /** @var \TomLutzenberger\ResponsiveImage\components\ResponsiveImage $ri */
        /** @noinspection PhpUndefinedFieldInspection */
        $ri = Yii::$app->responsiveImage;

        $this->targetPathResolved = empty($this->targetPath) ? $ri->defaultTargetPath : $this->targetPath;

        foreach ($this->getAttributes() as $attrName => $attrValue) {
            $this->targetPathResolved = str_replace('{' . $attrName . '}', $attrValue, $this->targetPathResolved);
        }

        return $this->targetPathResolved;
    }

    /**
     * Setter for $targetPath
     *
     * @param string $targetPath
     * @noinspection PhpUnused
     */
    public function setTargetPath($targetPath)
    {
        $this->targetPath = $targetPath;
    }
}
