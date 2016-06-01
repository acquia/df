(function($, Drupal) {
if (typeof localStorage === 'object') {
    try {
        localStorage.setItem('localStorage', 1);
        localStorage.removeItem('localStorage');
    } catch (e) {
        Storage.prototype._setItem = Storage.prototype.setItem;
        Storage.prototype.setItem = function() {};
        var output =
            '<div id="error-modal" data-reveal data-animation-in="fade-in" data-animation-out="fade-out" class="fin-modal-message reveal">' +
            '  <div>' +
            '    <span class="icon ion-alert-circled fa-4x"></span>' +
            '    <h2>' + Drupal.t('Warning!') + '</h2>' +
            '    <p> Your web browser does not support storing settings locally. In Safari, the most common cause of this is using Private Browsing Mode. Some settings may not save or some features may not work properly for you. </p>' +
            '  </div>' +
            '</div>';
        $('html').append(output);
         if ($.cookie('modal_shown') == null) {
      $.cookie('modal_shown', 'yes', { expires: 7, path: '/' });
         $('#error-modal').foundation().foundation('open');
   }
    }
}

$(document).ready(function() {

});


})(jQuery, Drupal);

