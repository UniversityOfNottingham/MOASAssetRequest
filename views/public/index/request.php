<div class="modal js-close-modal">
  <div class="modal__box js-modal-content">
    <script type="text/javascript">
      var item_id = <?= $asset_request->record_id ?>;
    </script>
    <div class="modal__header">
      <h4 class="modal__title">Use This Resource</h4>
      <svg class="icon icon--cross cursor-pointer js-close-modal" role="img">
        <title><?= __('Close Form'); ?></title>
        <use xlink:href="#icon-cross">
      </svg>
    </div>
    <div class="modal__content useresource__form">
      <?= text_to_paragraphs($formIntro); ?>
      <form class="modal__form" id="use-resource-form" action="" method="post">
        <?= $csrf ?>

        <div class="modal__form-row">
          <label class="modal__label" for="requester_name"><?= __('Name'); ?>:</label>
          <input class="modal__form-element"
                 type="text"
                 name="requester_name"
                 id="requester_name"
                 autocomplete="off"
                 autocapitalize="off"
                 autocorrect="off"
                 value="<?= $asset_request->requester_name ?>">
        </div>

        <div class="modal__form-row">
          <label class="modal__label" for="requester_org_type"><?= __('Organisation Type'); ?>:</label>
          <select class="modal__form-element" name="requester_org_type"  id="requester_org_type">
            <option value="0">- <?= __('Select'); ?> -</option>
            <?php foreach (AssetRequest::getRequesterOrgTypes() as $key => $value) : ?>
              <option value="<?= $key ?>"><?= $value ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="modal__form-row">
          <label class="modal__label" for="requester_org"><?= __('Organisation Name'); ?>:</label>
          <input class="modal__form-element"
                 type="text"
                 name="requester_org"
                 id="requester_org"
                 autocomplete="off"
                 autocapitalize="off"
                 autocorrect="off"
                 value="<?= $asset_request->requester_org ?>">
        </div>

        <div class="modal__form-row modal__form-row--float-right">
          <input type="checkbox" id="accept_terms" name="accept_terms" value="1" <?php echo $asset_request->accept_terms ? 'checked="checked"' : '' ?>>
          <label class="modal__label modal__label--small" for="accept_terms"><?= __('I accept the <a href="#" class="js-show-terms">Terms and Conditions</a>.'); ?></label>
        </div>

        <div class="modal__terms js-terms">
          <?= $formTerms; ?>
        </div>

        <div class="modal__form-row modal__form-row--right">
          <h6 class="error visually-hidden js-validation-message js-form-error"><?= __('Please check you\'ve entered all your information.'); ?></h6><br>
          <h6 class="error visually-hidden js-validation-message js-terms-error"><?= __('Please confirm that you agree to the terms and conditions.'); ?></h6>
        </div>

        <div class="modal__form-row modal__form-row--right">
          <input class="modal__submit" type="submit" value="<?= __('Request'); ?>">
        </div>
      </form>
    </div>
    <div class="modal__content useresource__download visually-hidden">
      <p><?= __('Download all the resources in this item as a Zip file.'); ?></p>
      <form class="modal__form" id="download-resource-form" action="" method="post">
        <?= $csrf ?>

        <div class="modal__form-row modal__form-row">
          <input class="modal__submit" type="submit" value="<?= __('Download'); ?>">
        </div>
      </form>
    </div>
    <div class="modal__content useresource__contact visually-hidden">
      <p><?= __('Please contact the rights holder to use this resource.'); ?></p>
      <p><a href="mailto:<?= $contact ?>"><?= $contact ?></a></p>
    </div>
  </div>
</div>

<div class="item-title__use">
  <button class="use js-use"><?= __('Use this resource'); ?></button>
</div>
