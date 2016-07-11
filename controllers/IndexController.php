<?php

/**
 * @package     omeka
 * @subpackage  moas-asset-request
 * @copyright   2016 University of Nottingham
 * @license     MIT
 * @author      Adam Cooper <adam.cooper@nottingham.ac.uk>
 */

class MOASAssetRequest_IndexController extends Omeka_Controller_AbstractActionController
{

    protected $_autoCsrfProtection = true;

    /**
     * Controller-wide initialization. Sets the underlying model to use.
     */
    public function init()
    {
        $this->_helper->db->setDefaultModelName('AssetRequest');
    }

    public function requestAction()
    {
        $response = [];

        $class = $this->_helper->db->getDefaultModelName();
        $varName = $this->view->singularize($class);
        $record_id = $this->_getParam('record_id', null);

        $csrf = new Omeka_Form_SessionCsrf;
        $response['csrf'] = $csrf->getElement('csrf_token')->getToken();

        /** @var AssetRequest $record */
        $record = new $class();
        $record->record_id = $record_id;

        if ($this->getRequest()->isPost()) {
            if (!$csrf->isValid($_POST)) {
                $response[$varName] = $record;
                $response['error'] = 'csrf invalid';
                $this->_helper->json($response);
            }
            
            // get item from db
            $item = $this->_helper->db->getTable('Item')->find($record->record_id);

            $record->setPostData($_POST);
            if ($record->save(false)) {
                // determine from metadata the asset type (download, contact)
                $rights = $item->getElementTexts('MOAS Elements', 'Download Rights');
                if (count($rights) > 0) {
                    $option = $rights[0]->getText();

                    // This amounts to "if the metadata is download, or if it's a certain type of organisation and
                    // contact isn't explicitly stated.
                    if ($option === 'Download' ||
                        ($option !== 'Contact' &&
                            array_search($record->requester_org_type, $class::getRequesterOrgTypes()) < 5
                        )) {
                        $record->download_token = $record->generateDownloadToken();
                        $record->save();
                    }
                }
            } else {
                $response['field_errors'] = $record->getErrors();
            }
        }

        $response['fields'] = $record;
        $this->_helper->json($response);
    }

    public function downloadAction()
    {
        $token = $this->_getParam('token', null);

        $csrf = new Omeka_Form_SessionCsrf;

        /** @var AssetRequest $record */
        $record = $this->_helper->db->getTable()->findByDownloadToken($token);

        if (isset($record) && $this->getRequest()->isPost()) {
            if ($csrf->isValid($_POST)) {
                // bundle assets.
                $zip = new MOASAssetRequest_ZipArchive($record);
                
                // stream assets.
            }
        } else {
            throw new Omeka_Controller_Exception_404;
        }
    }
}
