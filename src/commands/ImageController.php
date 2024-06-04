<?php

/**
 * @noinspection PhpUnused
 * @noinspection UnknownInspectionInspection
 */

namespace TomLutzenberger\ResponsiveImage\commands;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use TomLutzenberger\ResponsiveImage\components\ResponsiveImage;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\FileHelper;

/**
 * Class ImageController
 *
 * @package   TomLutzenberger\ResponsiveImage\commands
 * @copyright 2019 Tom Lutzenberger
 * @author    Tom Lutzenberger <lutzenbergertom@gmail.com>
 */
class ImageController extends Controller
{

    /**
     * @var string[] The supported image file extensions to search for
     */
    protected $imgFileExtensions = ['*.jpg', '*.jpeg', '*.png', '*.gif', '*.svg', '*.webp'];


    /**
     * Generates thumbnails based on configured presets
     *
     * @param string|null $presetName
     * @return int Exit code
     * @throws \yii\base\ErrorException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @noinspection DisconnectedForeachInstructionInspection
     */
    public function actionGenerate($presetName = null)
    {
        /** @var ResponsiveImage $ri */
        /** @noinspection PhpUndefinedFieldInspection */
        $ri = Yii::$app->responsiveImage;

        if ($presetName !== null && !array_key_exists($presetName, $ri->getPresets())) {
            $this->stderr(ExitCode::getReason(ExitCode::DATAERR) . ": Preset `$presetName` does not exist." . PHP_EOL);
            $this->stderr('Aborting.' . PHP_EOL);
            return ExitCode::DATAERR;
        }

        $presets = $presetName === null ? $ri->getPresets() : [$presetName => $ri->getPreset($presetName)];

        foreach ($presets as $preset) {
            $imgFiles = $this->getDirFiles($preset->srcPath);
            $pb = new ProgressBar((new ConsoleOutput()), count($imgFiles));
            $this->stdout("Preset `$preset->name`" . PHP_EOL);

            foreach ($imgFiles as $img) {
                $ri->getThumbnail($img, $preset->name);
                $pb->advance();
            }

            $this->stdout(PHP_EOL . PHP_EOL);
        }

        return ExitCode::OK;
    }


    /**
     * This command generates thumbnails based on configured presets
     *
     * @return int Exit code
     * @throws \yii\base\ErrorException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @noinspection DisconnectedForeachInstructionInspection
     */
    public function actionFlush()
    {
        /** @var ResponsiveImage $ri */
        /** @noinspection PhpUndefinedFieldInspection */
        $ri = Yii::$app->responsiveImage;

        foreach ($ri->getPresets() as $preset) {
            $imgFiles = $this->getDirFiles($preset->targetPath);
            $pb = new ProgressBar((new ConsoleOutput()), count($imgFiles));
            $this->stdout("Flush Preset `$preset->name`" . PHP_EOL);

            foreach ($imgFiles as $img) {
                FileHelper::unlink($img);
                $pb->advance();
            }

            $this->stdout(PHP_EOL . PHP_EOL);
        }

        return ExitCode::OK;
    }

    /**
     * Gets an array with all direct files in a given path
     *
     * @param string $path
     * @return array
     */
    protected function getDirFiles($path)
    {
        $abs = FileHelper::normalizePath(Yii::getAlias(str_replace('@web/', '@webroot/', $path)));
        return FileHelper::findFiles($abs, [
            'only'          => $this->imgFileExtensions,
            'caseSensitive' => false,
            'recursive'     => false,
        ]);
    }
}
