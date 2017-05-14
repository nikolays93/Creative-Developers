jq = jQuery.noConflict();
jq(function( $ ) {
  //on.load
  $(function(){
    /*get message (wpcf7)*/
    $( '#get-recall, .product #get-product' ).on('click', function(){
      $('[name="from-page"]').val( $('.entry-title').text() );
      $('[name="from-url"]').val( document.URL );
    });

    $( '.product #get-product' ).on('click', function(){
      var title = $('.product_title').text();
      $( '[name="your-message"]' ).val( 'Доброго времени суток, хочу приобрести '+title+'. \n\nПожалуйста, перезвоните мне.');
    });
  });
});

