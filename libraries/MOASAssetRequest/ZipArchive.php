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

    /** @var AssetRequest */
    protected $asset_request;

    /** @var Omeka_Storage */
    protected $storage;

    /** @var ZipArchive */
    protected $zip_archive;

    public function __construct(AssetRequest $assetRequest, Omeka_Storage $storage = null)
    {
        $this->asset_request = $assetRequest;
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
        $filePath = self::$ZIP_PATH . DIRECTORY_SEPARATOR . $this->asset_request->record_id . '.zip';
        if ($this->storage->isFile($filePath)) {
            return;
        }
        
        // create zip file
        $zipPath = $this->_createZip($this->asset_request->Item->Files);

        // store it in files folder
        $this->storage->store($zipPath, $filePath);
    }

    /**
     * @param array $assets An array of Omeka_File objects.
     * @throws Omeka_Storage_Exception
     */
    private function _createZip(array $assets)
    {
        $zip = new ZipArchive();
        $tmpPath = $this->storage->getTempDir() . DIRECTORY_SEPARATOR . $this->asset_request->record_id . '.zip';

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
        } finally {
            $zip->close();
        }

        return $tmpPath;
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
