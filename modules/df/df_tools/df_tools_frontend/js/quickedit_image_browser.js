/**
 * @file
 * Overrides the Drupal.quickedit.editors.image prototype for our demo.
 */

(function ($, _, Drupal) {

  'use strict';

  if (Drupal.quickedit.editors.image && !Drupal.quickedit.editors.image.prototype.overriden) {
    var stateChange = Drupal.quickedit.editors.image.prototype.stateChange;
    // Override the default stateChange handler for quickedit_image.
    Drupal.quickedit.editors.image.prototype.stateChange = function (fieldModel, state, options) {
      // Call the normal handler.
      stateChange.call(this, fieldModel, state, options);
      if (state == 'active') {
        // Append a "Browse" button.
        var $button = $('<button type="button" class="action-browse quickedit-button icon">' + Drupal.t('Browse') + '</button>');
        fieldModel.get('entity').toolbarView._find('ops').prepend($button);
        var self = this;
        $button.on('click', function () {
          // Embed File Browser in an iFrame.
          var $modal = $('<div><iframe width="100%" height="100%" style="display: none; border: 0;" src="/entity-browser/iframe/media_browser" /></div>');
          var dialog = Drupal.dialog($modal, {});
          dialog.showModal();
          $modal.find('iframe').on('load', function () {
            // Reset styling on the iFrame.
            var $contents = $(this).contents();

            // Switch tabs to the browser automatically.
            var $upload = $contents.find('[data-button-id="edit-tab-selector-735d146c-a4b2-4327-a057-d109e0905e05"]').hide();
            var $browse = $contents.find('[data-button-id="edit-tab-selector-a4ad947c-9669-497c-9988-24351955a02f"]');
            if ($upload.hasClass('active')) {
              $browse[0].click();
              return;
            }

            // Fade in the contents.
            $(this).fadeIn();
            // When "Select" is clicked, trigger our Quickedit logic and block
            // the normal form submit.
            $contents.find('#edit-actions-wrap input').on('click', function (e) {
              e.preventDefault();
              e.stopPropagation();

              var $input = $(this).closest('form').find('input[type="checkbox"]:checked');
              if ($input.length) {
                var parts = $input.attr('name').match(/\[file:(\d+)\]/);
                if (parts && parts[1]) {
                  var fieldID = fieldModel.get('fieldID');
                  var url = Drupal.quickedit.util.buildUrl(fieldID, Drupal.url('quickedit_image/!entity_type/!id/!field_name/!langcode/!view_mode/existing'));

                  // Construct form data that our endpoint can consume.
                  var data = new FormData();
                  data.append('fid', parts[1]);

                  self.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    success: function (response) {
                      var $el = $(self.fieldModel.get('el'));
                      // Indicate that the field has changed - this enables the
                      // "Save" button.
                      self.fieldModel.set('state', 'changed');
                      self.fieldModel.get('entity').set('inTempStore', true);
                      self.removeValidationErrors();

                      // Replace our html with the new image. If we replaced our entire
                      // element with data.html, we would have to implement complicated logic
                      // like what's in Drupal.quickedit.AppView.renderUpdatedField.
                      var $content = $(response.html).closest('[data-quickedit-field-id]').children();
                      $el.empty().append($content);

                      // Hide the browse button.
                      $button.hide();
                    }
                  });

                  // Indicate AJAX.
                  self.renderDropzone('upload loading', Drupal.t('Using new image...'));
                }
              }
              dialog.close();
            });
          });
        });
      }
    };
  }

})(jQuery, _, Drupal);
