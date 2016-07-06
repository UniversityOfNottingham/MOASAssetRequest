<?php
$head = array(
    'title' => html_escape(__('Asset Requests')),
    'content_class' => 'horizontal-nav'
);
echo head($head);
?>
<?php echo flash(); ?>

<?php if (!has_loop_records('asset_requests')): ?>
    <p><?php echo __('There have been no asset requests'); ?></p>
<?php else: ?>
    <table class="full" id="asset-request-table">
        <thead>
        <tr>
            <th>Item</th>
            <th>By</th>
            <th>Type</th>
            <th>On</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach (loop('asset_requests') as $assetRequest): ?>
            <tr>
                <td><?php if (!is_null($item = $assetRequest->Item)): ?>
                        <a href="<?php echo html_escape(record_url($item)); ?>">
                            <?php echo metadata($item, array('Dublin Core', 'Title')); ?>
                        </a>
                    <?php else: ?>
                        <i>N/A</i>
                    <?php endif; ?>
                </td>
                <td><?php echo $assetRequest->requester_name . ' (' . $assetRequest->requester_org . ')' ?></td>
                <td><?php echo $assetRequest->requester_org_type; ?></td>
                <td><?php echo __('%s',
                        html_escape(format_date($assetRequest->added, Zend_Date::DATE_LONG))); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <script type="application/javascript">
        jQuery(document).ready( function ($) {
            $('#asset-request-table').DataTable({
                buttons: [
                    'pdf', 'csv'
                ],
                dom: 'lftipB',
                pagingType: 'input',
                stateSave: true
            });
        } )
    </script>
<?php endif; ?>
<?php echo foot(); ?>
