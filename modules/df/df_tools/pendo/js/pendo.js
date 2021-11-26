(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.pendo = {
    attach: function (context, settings) {

      // Immediately-invoked function expression to prevent scope pollution...
      (function(apiKey){ // <-- passed in at the bottom
        (function(p,e,n,d,o){var v,w,x,y,z;o=p[d]=p[d]||{};o._q=[];
          v=['initialize','identify','updateOptions','pageLoad'];for(w=0,x=v.length;w<x;++w)(function(m){
            o[m]=o[m]||function(){o._q[m===v[0]?'unshift':'push']([m].concat([].slice.call(arguments,0)));};})(v[w]);
          // Create a `<script>` tag linked to your application-specific Agent build from our CDN...
          y=e.createElement(n);y.async=!0;y.src='https://cdn.pendo.io/agent/static/'+apiKey+'/pendo.js';
          z=e.getElementsByTagName(n)[0];z.parentNode.insertBefore(y,z);
        })(window,document,'script','pendo');

        // Call this whenever information about your visitors becomes available
        // Please use Strings, Numbers, or Bools for value types.
        pendo.initialize(drupalSettings.pendo.data);
      })(drupalSettings.pendo.api_key); //
    }
  };
})(jQuery, Drupal, drupalSettings);
