<?php

/**
 * @package     omeka
 * @subpackage  moas-asset-request
 * @copyright   2016 University of Nottingham
 * @license     MIT
 * @author      Adam Cooper <adam.cooper@nottingham.ac.uk>
 * @author      James Hodgson <james.hodgson@nottingham.ac.uk>
 */

class Table_AssetRequest extends Omeka_Db_Table
{
    public function findByDownloadToken($token)
    {
        $select = $this->getSelect();
        $select->where("download_token = ?");
        $select->limit(1);
        return $this->fetchObject($select, array($token));
    }

    public function applySearchFilters($select, $params)
    {
        parent::applySearchFilters($select, $params);
    }
}
