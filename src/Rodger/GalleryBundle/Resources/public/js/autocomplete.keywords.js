$.fn.autocomplete_keywords = function() {
  var $field = this;
  var split = function ( val ) { return val.split( /,\s*/ ); };
  var extractLast = function ( term ) { return split( term ).pop(); };

	$field
  // don't navigate away from the field on tab when selecting an item
  .bind( "keydown", function( event ) {
    if ( event.keyCode === $.ui.keyCode.TAB &&
        $( this ).data( "autocomplete" ).menu.active ) {
      event.preventDefault();
    }
  })
  .autocomplete({
    source: function( request, response ) {
      $.getJSON( $field.attr('source'), {
        search_string: extractLast( request.term )
      }, response );
    },
    search: function() {
      // custom minLength
      var term = extractLast( this.value );
      if ( term.length < 2 ) {
        return false;
      }
    },
    focus: function() {
      // prevent value inserted on focus
      return false;
    },
    select: function( event, ui ) {
      var terms = split( this.value );
      // remove the current input
      terms.pop();
      // add the selected item
      terms.push( ui.item.value );
      // add placeholder to get the comma-and-space at the end
      terms.push( "" );
      this.value = terms.join( ", " );
      return false;
    }
  });
};

$(function(){
  $('.autocomplete.keywords').each(function(){ $(this).autocomplete_keywords() });
})