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

    public function orginisationAction()
    {
        /** @var AssetRequest $class */
        $class = $this->_helper->db->getDefaultModelName();
        $this->_helper->json($class::$REQUESTER_ORG_TYPES);
    }

    public function requestAction()
    {
        $response = [];

        $class = $this->_helper->db->getDefaultModelName();
        $varName = $this->view->singularize($class);
        $record_id = $this->_getParam('record_id', null);

        if ($this->_autoCsrfProtection) {
            $csrf = new Omeka_Form_SessionCsrf;
            $response['csrf'] = $csrf->getElement('csrf_token')->getToken();
        }

        $record = new $class();
        $record->record_id = $record_id;

        if ($this->getRequest()->isPost()) {
            if ($this->_autoCsrfProtection && !$csrf->isValid($_POST)) {
                $response[$varName] = $record;
                $response['error'] = 'csrf invalid';
                $this->_helper->json($response);
            }
            
            // get item from db

            $record->setPostData($_POST);
            if ($record->save(false)) {

                // determine from metadata the asset type (download, contact)

                // if "download" or organisation is non-commercial return view of download button after generating download token

                // if "contact" or anything else return view of contact information

            } else {
                $response['field_errors'] = $record->getErrors();
            }
        }

        $response['fields'] = $record;
        $this->_helper->json($response);
    }

    public function downloadAction($token)
    {
        // get request record using token
        // bundle up referenced assets that make up this item and return to the user
        $this->_helper->viewRenderer('request');
    }
}
