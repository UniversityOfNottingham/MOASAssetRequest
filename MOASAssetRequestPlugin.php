<?php

/**
 * @package     omeka
 * @subpackage  moas-asset-request
 * @copyright   2016 University of Nottingham
 * @license     MIT
 * @author      Adam Cooper <adam.cooper@nottingham.ac.uk>
 */

class MOASAssetRequestPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install',
        'uninstall',
        'uninstall_message',
        'upgrade',
        'define_routes'
    );

    public function hookInstall()
    {
        $db = $this->_db;
        $sql = "
            CREATE TABLE IF NOT EXISTS `$db->AssetRequest` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `record_id` int(10) unsigned NOT NULL,
              `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `download_token` varchar(16) COLLATE utf8_unicode_ci,
              `requester_name` tinytext NOT NULL COLLATE utf8_unicode_ci,
              `requester_org` tinytext NOT NULL COLLATE utf8_unicode_ci,
              `requester_org_type` tinytext NOT NULL COLLATE utf8_unicode_ci,
              `accept_terms` tinyint(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY `record_id` (`record_id`),
              KEY `download_token` (`download_token`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";
        $db->query($sql);
    }

    public function hookUninstall()
    {
        $db = get_db();
        $sql = "DROP TABLE IF EXISTS `$db->AssetRequest`";
        $db->query($sql);
    }

    /**
     * Display the uninstall message.
     */
    public function hookUninstallMessage()
    {
        echo __('%sWarning%s: This will remove the records of all requests made for assets.%s',
            '<p><strong>', '</strong>', '</p>');
    }

    public function hookUpgrade($args)
    {
        $old = $args['old_version'];
        $new = $args['new_version'];
    }

    /**
     * Register the application routes.
     *
     * @param array $args With `router`.
     */
    public function hookDefineRoutes($args)
    {
        $args['router']->addConfig(new Zend_Config_Ini(
            __DIR__.'/routes.ini'
        ));
    }
}