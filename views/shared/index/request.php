<?php $formIntro = get_theme_option('reuse_form_intro'); ?>
<?php $formTerms = get_theme_option('reuse_form_tsandcs'); ?>

<div class="modal__header">
  <h4 class="modal__title">Use This Resource</h4>
  <svg class="icon icon--cross cursor-pointer js-close-modal" role="img">
    <title><?= __('Close Form'); ?></title>
    <use xlink:href="#icon-cross">
  </svg>
</div>
<?= text_to_paragraphs($formIntro); ?>
<form class="modal__form" id="use-resource-form" action="/asset/<?= $asset_request->record_id ?>" method="post">
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
    <?php if ($errors['requester_name']) : ?><span class="modal_form-error"><?= $errors['requester_name']?></span><?php endif; ?>
  </div>

  <div class="modal__form-row">
    <label class="modal__label" for="requester_org_type"><?= __('Organisation Type'); ?>:</label>
    <select class="modal__form-element" name="requester_org_type"  id="requester_org_type">
      <option value="0">- <?= __('Select'); ?> -</option>
      <?php foreach (AssetRequest::$REQUESTER_ORG_TYPES as $key => $value) : ?>
      <option value="<?= $key ?>"><?= $value ?></option>
      <?php endforeach; ?>
    </select>
    <?php if ($errors['requester_org_type']) : ?><span class="modal_form-error"><?= $errors['requester_org_type']?></span><?php endif; ?>
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
    <?php if ($errors['requester_org']) : ?><span class="modal_form-error"><?= $errors['requester_org']?></span><?php endif; ?>
  </div>

  <div class="modal__form-row modal__form-row--float-right">
    <input type="checkbox" id="accept_terms" name="accept_terms" <?php echo $asset_request->accept_terms ? 'checked="checked"' : '' ?>>
    <label class="modal__label--small" for="accept_terms"><?= __('I accept the <a href="#" class="js-show-terms">Terms and Conditions</a>.'); ?></label>
    <?php if ($errors['accept_terms']) : ?><span class="modal_form-error"><?= $errors['accept_terms']?></span><?php endif; ?>
  </div>

  <div class="modal__terms js-terms">
    <?= $formTerms; ?>
  </div>

  <div class="modal__form-row modal__form-row--right">
    <h6 class="error visually-hidden js-validation-message js-form-error"><?= __('Please check you\'ve entered all your information.'); ?></h6>
    <h6 class="error visually-hidden js-validation-message js-terms-error"><?= __('Please confirm that you agree to the terms and conditions.'); ?></h6>
  </div>

  <div class="modal__form-row modal__form-row--right">
    <input type="hidden" id="download-rights" value="">
    <input class="modal__submit" type="submit" value="<?= __('Request'); ?>">
  </div>
</form>
