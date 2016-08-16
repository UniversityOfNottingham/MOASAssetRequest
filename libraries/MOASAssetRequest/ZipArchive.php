<?php

/**
 * @package     omeka
 * @subpackage  moas-asset-request
 * @copyright   2016 University of Nottingham
 * @license     MIT
 * @author      Adam Cooper <adam.cooper@nottingham.ac.uk>
 */

/**
 * Class MOASAssetRequest_ZipArchive
 *
 * Responsible for building out a zip file containing the assets of an Omeka item.
 */
class MOASAssetRequest_ZipArchive
{
    public static $ZIP_PATH = 'asset_download';

    /** @var string The absolute filesystem path to the zip file */
    public $path;

    /** @var string The intended filename (including extension) of the zip file to be represented to the client. */
    public $name;

    /** @var AssetRequest */
    protected $assetRequest;

    /** @var Omeka_Storage */
    protected $storage;

    /** @var ZipArchive */
    protected $zip_archive;

    public function __construct(AssetRequest $assetRequest, Omeka_Storage $storage = null)
    {
        $this->assetRequest = $assetRequest;
        $this->storage = ($storage === null) ? Zend_Registry::get('storage') : $storage;

        $this->_setupStorage();
        $this->_prepare();
    }

    /**
     * Attempts to gather the artifacts that make up this asset request and bundle them into a single
     * zip file for download. If the artifact has already been created then returns immediately.
     *
     * @throws Omeka_Storage_Exception
     */
    protected function _prepare()
    {
        $filePath = self::$ZIP_PATH . DIRECTORY_SEPARATOR . $this->assetRequest->record_id . '.zip';

        if ( ! $this->storage->isFile($filePath)) {
            // create zip file
            $zipPath = $this->_createZip($this->assetRequest->Item->Files);

            // store it in files folder
            $this->storage->store($zipPath, $filePath);
        }

        $this->name = $this->_getFilename();
        $this->path = $this->storage->getLocalPath($filePath);
    }

    /**
     * @param array $assets An array of Omeka_File objects.
     * @throws Omeka_Storage_Exception
     */
    private function _createZip(array $assets)
    {
        $zip = new ZipArchive();
        $tmpPath = $this->storage->getTempDir() . DIRECTORY_SEPARATOR . $this->assetRequest->record_id . '.zip';

        if ( ! $zip->open($tmpPath, ZipArchive::CREATE)) {
            throw new Omeka_Storage_Exception("Unable to create temporary zip file at " . $tmpPath);
        }

        try {
            /** @var File $file */
            foreach ($assets as $file) {
                $path = $this->storage->getLocalPath($file->getStoragePath());
                $zip->addFile($path, $file->original_filename);
            }
        } catch (Exception $ex) {
            throw new Omeka_Storage_Exception("Error whilst adding files to zip file at " . $tmpPath, 500, $ex);
        }

        return $tmpPath;
    }
    
    private function _getFilename()
    {
        /** @var Item $name */
        $item = $this->assetRequest->Item;

        $titleText = $item->getElementTexts('Dublin Core', 'Title');
        if (count($titleText) > 0) {
            $title = $titleText[0]->getText();

            return $title . '.zip';
        }

        return 'Your download.zip';
    }

    /**
     * Ensures the correct directories are registered as storage locations for our zip files.
     *
     * @throws Omeka_Storage_Exception
     */
    private function _setupStorage()
    {
        $adapter = $this->storage->getAdapter();

        if ( ! $adapter instanceof MOAS_Storage_Adapter_Filesystem) {
            throw new Omeka_Storage_Exception('Not an instance of MOAS_Storage_Adapter_Filesystem');
        }

        $this->storage->registerSubDir(self::$ZIP_PATH);
    }
}
