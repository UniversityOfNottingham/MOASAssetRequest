<?php

/**
 * @package     omeka
 * @subpackage  moas-asset-request
 * @copyright   2016 University of Nottingham
 * @license     MIT
 * @author      Adam Cooper <adam.cooper@nottingham.ac.uk>
 */

class AssetRequest extends Omeka_Record_AbstractRecord
{
    public $id;
    public $record_id;
    public $download_token;
    public $added;
    public $requester_name;
    public $requester_org;
    public $requester_org_type;
    public $accept_terms;

    /** @const */
    static $REQUESTER_ORG_TYPES = [
        1 => 'Personal use',
        2 => 'NGO or charity representative',
        3 => 'Community group',
        4 => 'School or university educator',
        5 => 'Museum or heritage organisation',
        6 => 'Government official',
        7 => 'Journalist',
        8 => 'Other'
    ];

    /** @const */
    static $REQUESTER_ORG_TYPES_CUTOFF = 2;

    public function _validate()
    {
        // record_id
        // required integer and should exist
        $recordValdator = new Zend_Validate();
        $recordValdator
            ->addValidator(new Zend_Validate_Int())
            ->addValidator(new Zend_Validate_Db_RecordExists([
                'table' => $this->getTable('Item')->getTableName(),
                'field' => 'id'
            ]));
        if (!$recordValdator->isValid($this->record_id)) {
            $this->addError('record_id', __('Item does not exist'));
        }

        // token
        // meets token requirements (exactly 16 characters)
        $tokenValidator = new Zend_Validate_StringLength(['min' => 16, 'max' => 16]);
        if ($this->download_token !== null && !$tokenValidator->isValid($this->download_token)) {
            $this->addError('download_token', __('Download token not valid'));
        }

        // requester_name
        // required string
        $nameValidator = new Zend_Validate_StringLength(['min' => 1]);
        if ($this->requester_name === null || !$nameValidator->isValid($this->requester_name)) {
            $this->addError('requester_name', __('You must enter a name'));
        }

        // requester_org_type
        // required and in list of possible values
        $orgTypeValidator = new Zend_Validate_InArray(array_keys(AssetRequest::$REQUESTER_ORG_TYPES));
        if ($this->requester_org_type === null || !$orgTypeValidator->isValid($this->requester_org_type)) {
            $this->addError('requester_org_type', __('You must choose an organisation type'));
        }

        // requester_org
        // required string if not personal org type
        $orgValidator = new Zend_Validate_StringLength(['min' => 1]);
        if ($this->requester_org_type >= AssetRequest::$REQUESTER_ORG_TYPES_CUTOFF &&
            ($this->requester_org === null || !$orgValidator->isValid($this->requester_org))) {
            $this->addError('requester_org', __('You must enter an organisation name'));
        }

        // accept_terms
        // required
        if ($this->accept_terms === null || $this->accept_terms === 0) {
            $this->addError('accept_terms', __('You must accept the terms and conditions'));
        }
    }
}
