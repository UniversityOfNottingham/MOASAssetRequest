<?php

/**
 * @package     omeka
 * @subpackage  moas-asset-request
 * @copyright   2016 University of Nottingham
 * @license     MIT
 * @author      James Hodgson <james.hodgson@nottingham.ac.uk>
 */
class MOASAssetRequest_AdminController extends Omeka_Controller_AbstractActionController
{
    public function indexAction()
    {
        // Always go to browse.
        $this->forward('browse');
        return;
    }

    /**
     * Controller-wide initialization. Sets the underlying model to use.
     */
    public function init()
    {
        $this->_helper->db->setDefaultModelName('AssetRequest');
    }
}