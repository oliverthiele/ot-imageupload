<?php

declare(strict_types=1);

namespace OliverThiele\OtImageupload\Controller;

use OliverThiele\OtImageupload\Domain\Model\Image;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use function OliverThiele\OtImageupload\Validation\Validator\FilesizeValidator;

class UploadController extends ActionController
{
    /**
     * @var ValidatorResolver
     */
    protected $validatorResolver;

    /**
     * The upload path for images
     *
     * @var string
     */
    protected $imageUploadPath = '';

    /**
     * @var FrontendUserAuthentication
     */
    protected $frontendUser;

    /**
     * @var array global extensions settings
     */
    protected $extensionSettings = [];

    public function formAction(): ResponseInterface
    {
        $minFileSize = 10000;
        $maxFileSize = 1000000;

        $this->view->assignMultiple([
            'minFileSize' => $minFileSize,
            'maxFileSize' => $maxFileSize,
            'multiple' => true,
        ]);
        return $this->htmlResponse();
    }

    /**
     * @return void
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function initializeAction(): void
    {
        $this->extensionSettings = GeneralUtility::makeInstance(
            ExtensionConfiguration::class
        )->get('ot_imageupload');

        if (isset($GLOBALS['TSFE']->fe_user->user['uid'])) {
            $this->frontendUser = $GLOBALS['TSFE']->fe_user->user;
        }

        $this->setImageUploadPath();
    }

    public function initializeUploadAction(): void
    {
        $imageObjects = [];

        if ($this->request->hasArgument('images')) {
            $images = $this->request->getArgument('images');

            foreach ($images as $image) {
                $newImage = new Image($image);
                if ($newImage->getError() === 0) {
                    $imageObjects[] = new Image($image);
                }
            }

            $this->request->setArgument('imageObjects', $imageObjects);
        }
    }

    /**
     * @return ResponseInterface
     */
    public function uploadAction(): ResponseInterface
    {
        $imageObjects = [];
        $validatorResult = [];

        $arguments = $this->request->getArguments();

        if (isset($arguments['imageObjects'])) {
            $imageObjects = $arguments['imageObjects'];
        }

        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
        $storage = $storageRepository->getDefaultStorage();

        if (!$storage->hasFolder($this->imageUploadPath)) {
            $storage->createFolder($this->imageUploadPath);
        }

        $errors = [];
        $uploadedFiles = [];

        /** @var Image $imageObject */
        foreach ($imageObjects as $imageObject) {
            $minFileSize = (int)$this->settings['fileSize']['min'];
            $maxFileSize = (int)$this->settings['fileSize']['max'];

            if ($imageObject->getSize() < $minFileSize) {
                $errors[$imageObject->getName()][] = 'Error: Filesize is smaller than ' . $minFileSize . ' byte';
            }

            if ($imageObject->getSize() > $maxFileSize) {
                $errors[$imageObject->getName()][] = 'Error: Filesize is smaller than ' . $maxFileSize . ' byte';
            }

            $allowedMimeTypes = explode(',', $this->extensionSettings['allowedMimeTypes']);

            if (!in_array($imageObject->getType(), $allowedMimeTypes, true)) {
                $errors[$imageObject->getName()][] = 'Error: MimeType "' . $imageObject->getType() . '" is not allowed';
            }

            $minWidth = (int)$this->settings['imageDimensions']['width']['min'];
            $maxWidth = (int)$this->settings['imageDimensions']['width']['max'];
            $minHeight = (int)$this->settings['imageDimensions']['height']['min'];
            $maxHeight = (int)$this->settings['imageDimensions']['height']['max'];

            if ($imageObject->getWidth() < $minWidth) {
                $errors[$imageObject->getName()][] = 'Error: Image width "' . $imageObject->getWidth() . '" is smaller than ' . $minWidth . 'pixel';
            }

            if ($imageObject->getWidth() > $maxWidth) {
                $errors[$imageObject->getName()][] = 'Error: Image width "' . $imageObject->getWidth() . '" is greater than ' . $maxWidth . ' pixel';
            }

            if ($imageObject->getHeight() < $minHeight) {
                $errors[$imageObject->getName()][] = 'Error: Image height "' . $imageObject->getHeight() . '" is smaller than ' . $minHeight . ' pixel';
            }

            if ($imageObject->getHeight() > $maxHeight) {
                $errors[$imageObject->getName()][] = 'Error: Image height "' . $imageObject->getHeight() . '" is greater than ' . $maxHeight . ' pixel';
            }

            if (!isset($errors[$imageObject->getName()]) || count($errors[$imageObject->getName()]) === 0) {
                $newFile = $storage->addFile(
                    $imageObject->getTmpName(),
                    $storage->getFolder($this->imageUploadPath),
                    basename($imageObject->getName()),
                    DuplicationBehavior::REPLACE
                );
                $uploadedFiles[] = $newFile;
            }
        }

        $this->view->assignMultiple([
            'imageObjects' => $imageObjects,
            'uploadedFiles' => $uploadedFiles,
            'errors' => $errors,
            'multiple' => true,
        ]);
        return $this->htmlResponse();
    }

    /**
     * @return void
     */
    private function setImageUploadPath(): void
    {

        $imageUploadPath = '';
        if (isset($this->extensionSettings['imageUploadPath'])) {
            $imageUploadPath = $this->extensionSettings['imageUploadPath'];
        }

        if (isset($this->frontendUser['uid'])) {
            $imageUploadPath .= $this->frontendUser['uid'] . '/';
        }

        if (isset($this->extensionSettings['additionalDirectory']) && trim((string)$this->extensionSettings['additionalDirectory'])) {
            $imageUploadPath .= $this->extensionSettings['additionalDirectory'];
        }

        $subdirectory = trim((string)$this->settings['subdirectory']);
        if ($subdirectory !== '') {
            $imageUploadPath .= $subdirectory;
        }


        $this->imageUploadPath = $imageUploadPath;
    }
}
