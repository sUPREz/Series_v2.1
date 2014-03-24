// Plugin definition.
$.fn.CustomSelect = function( options ) {
  // Extend our default options with those provided.
  // Note that the first argument to extend is an empty
  // object – this is to keep from overriding our "defaults" object.
  var opts = $.extend( { callback: function(){} }, $.fn.CustomSelect.defaults, options );


  // build menu
  var menu = '<div class="CustomSelect Menu">';
  var datas = opts.datas;
  for(var i in datas){
    menu += '<div class="CustomSelect MenuItem" data-customselect-valueid="'+i+'">'+datas[i]+'</div>';
  }//id="'+i+'"
  menu += '</div>';

  $('body').append( menu );

  // handle click on menu item
  $('.CustomSelect.MenuItem').each(function(){
    $(this).click(function(){
      var itemID = $(this).attr('data-customselect-valueid');
      var itemText = $(this).text();
      var sourceID = $(this).parent('.Menu').attr('data-CustomSelect-sourceID');
      var Field = $('.CustomSelect.Field[data-CustomSelect-index="'+sourceID+'"]');

      // set new value !
      Field.text( itemText );
      Field.attr( 'data-customselect-valueid' , itemID );

      // callback !
      opts.callback.call( this , sourceID , itemText , itemID );

      //Hide Menu
      Field.trigger('click');
    });
  });

  return this.each(function( index ) {
    // set index for current value button
    $(this).attr('data-CustomSelect-index' , index);
    // style current selected value
    $(this).addClass('CustomSelect');
    $(this).addClass('Field');

    //detect original value
    var Field = -1;
    for( var i in datas){
      if( datas[i] == $(this).text() ){
        Field = i;
        break;
      }
    }
    $(this).attr('data-CustomSelect-valueID' , Field);

    // handle click on Field
    $(this).click( function(){
      var Menu =  $('.CustomSelect.Menu');
      var Field = $(this);

      if( Menu.css('display') != 'none'
      &&  Menu.attr('data-CustomSelect-sourceID') == $(this).attr('data-CustomSelect-index') ){
        Menu.hide();
      } else {
        FieldValueID = $(this).attr('data-CustomSelect-valueID');
        // Clear menu selection
        $('.CustomSelect.MenuItem').each(function(){
          $(this).removeClass('Selected');
        });
        // Select item that match the current value
        $('.CustomSelect.MenuItem[data-customselect-valueid="'+FieldValueID+'"]').addClass('Selected');
        // Write current value index value in menu
        Menu.attr('data-CustomSelect-sourceID' , $(this).attr('data-CustomSelect-index'));
        Menu.show();

        //console.log( Field[0] , Menu[0] );
        Menu.position({
          my: opts.position.my,
          at: opts.position.at,
          of: Field
        });
      }
    });
  });
};

// Plugin defaults – added as a property on our plugin function.
$.fn.CustomSelect.defaults = {
  datas: {
    1: "Nous",
    2: "Wen",
    3: "Steph",
    4: "???"
  },
  position:{
    my: "left top",
    at: "right top"
  }
};