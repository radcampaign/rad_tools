(function($){

  $(document).ready(function(){
    var win = $('<div #id="rdebug-wrapper"></div>'),
        values = window._raddebug;

    win.attr('id', 'rdebug-wrapper');
    for (key in values) {
      var div = $('<div class="rdebug-values" style="display:none"></div>')
                  .addClass('rdebug-inspect-vals')
                  .attr('id', key);
      div.append('<div class="rdebug-title"><strong><h4>' + key + ': </h4></strong></div>');

      for (key2 in values[key]) {
        var elem = $('<div></div>')
          .addClass('rdebug-elem rdebug-key-' + key2);

        elem.append('<span><strong>' + key2 + ': </strong>' + values[key][key2] + '</span>');
        div.append(elem);
      }
      win.append(div);
    }

    // append out action buttons
    win.append(
      '<div class="rdebug-actions">' +
        '<button name="close">Close</button>' +
      '</div>'
    );

    $('body').append(win);

    var elem = $('#rdebug-wrapper');
    // close event
    elem.find('button[name="close"]').click(function(){
      $('#rdebug-wrapper').remove();
    });

    elem.find('.rdebug-values:first-of-type').show();
    var allVals = elem.find('.rdebug-values'),
        showVal = function(ind) {
          allVals.each(function(index, value) {
            var ourelem = $(this);
            if (ind == index) {
              ourelem.show();
            }
            else {
              ourelem.hide();
            }
          });
        },
        addButton = function(type, index) {
          var text = type == 'prev' ? 'Previous' : 'Next',
              button = '<button name="' + type + '" data-index="' + index +  '">' + text + '</button>',
              actions = elem.find('.rdebug-actions');
          if(type == 'prev') {
            actions.prepend(button);
          }
          else {
            actions.append(button);
          }

        }

    if (allVals.length > 1) {
      elem.find('.rdebug-actions').append('<button name="next" data-index="0">Next</button>');
    }

    elem.delegate('button[name="next"]', 'click', function(ev) {
      var that = $(this),
        ind = that.attr('data-index') + 1;
      showVal(ind);
      that.attr('data-index', ind);
      if (that.attr('data-index') != 0 && elem.find('.rdebug-actions button[name="prev"]').length == 0) {
        addButton('prev', ind -1);
      }
      if(ind == allVals.length - 1) {
        that.remove();
      }
    });

    elem.delegate('button[name="prev"]', 'click', function(ev){
      var that = $(this),
        ind = that.attr('data-index');
      showVal(ind);
      that.attr('data-index', ind);
      // if we are back at zero - remove this button.
      if(ind == 0) {
        that.remove();
      }
      if (that.attr('data-index') != allVals.length - 1 && elem.find('.rdebug-actions button[name="next"]').length == 0) {
        addButton('next', ind);
      }
    });
  });

}(jQuery))
