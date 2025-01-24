/**
 * @file
 * api modal
 */

(function (Drupal) {
  Drupal.behaviors.apiDemo = {
    attach(context) {
      const button = document.querySelector('.open-apiModal');
      const modal = document.querySelector('.apiResponseModal');

      const options = {
        title: 'API Response',
        width: '100%',
      };

      const dialog = Drupal.dialog(modal, options);

      button.addEventListener('click', function (e) {
        e.preventDefault();
        dialog.showModal();
      });
    },
  };
})(Drupal);
