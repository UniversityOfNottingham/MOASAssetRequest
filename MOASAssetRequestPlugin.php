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
        'initialize',
        'install',
        'uninstall',
        'uninstall_message',
        'upgrade',
        'define_routes',
        'public_head',
        'define_acl',
        'admin_head'
    );

    protected $_filters = array(
        'admin_navigation_main'
    );

    public function hookInitialize()
    {
        add_shortcode('asset_request_button', array($this, 'assetRequestButtonShortCode'));
    }

    public function hookInstall()
    {
        $db = $this->_db;
        $sql = "
            CREATE TABLE IF NOT EXISTS `$db->AssetRequest` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `record_id` int(10) unsigned NOT NULL,
              `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `download_token` varchar(32) COLLATE utf8_unicode_ci,
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

    public function hookPublicHead($args)
    {
        queue_css_file('assetrequest.min');
        queue_js_file('assetrequest.min');
    }

    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $indexResource = new Zend_Acl_Resource('MOASAssetRequest_Admin');
        $acl->add($indexResource);

        $acl->allow(array('super', 'admin'), array('MOASAssetRequest_Admin'));
    }

    public function hookAdminHead($args)
    {
        queue_css_file('datatables.min');
        queue_css_file('assetrequest.min');
        queue_js_file('datatables.min');
        queue_js_file('datatables/pagination/input');
    }

    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('MOAS Asset Requests'),
            'uri' => url('asset-requests'),
        );
        return $nav;
    }

    /**
     * @param $args
     * @param Omeka_View $view
     * @return string
     */
    public function assetRequestButtonShortCode($args, $view)
    {
        $record = new AssetRequest();
        $record->record_id = $args['id'];

        $csrf = new Omeka_Form_SessionCsrf;

        $intro = get_theme_option('reuse_form_intro');
        $terms = get_theme_option('reuse_form_tsandcs');

        $email = metadata('item', array('MOAS Elements', 'Download Contact'), array('all' => true));

        return $view->partial('index/request.php', array(
            'asset_request' => $record,
            'csrf' => $csrf,
            'formIntro' => $intro,
            'formTerms' => $terms,
            'contact' => (is_array($email) && key_exists(0, $email)) ? $email[0] : ""
        ));
    }
}
