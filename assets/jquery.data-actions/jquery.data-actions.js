/*!
 * Script name: data-actions
 * Script URI: https://github.com/nikolays93/jquery.data-actions/
 * Author: NikolayS93
 * Author URI: //vk.com/nikolays_93
 * Description: jQuery actions.
 * Version: 1.6b
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

jQuery(function($){
	//
	// Core функции data-действий
	//
  function doAction($obj, target, action){
    if( target === 'this' ){
      var evalTarget = '$(this)';
    } else {
      var evalTarget = "$('"+target+"')";

      var exp = {
        "> " : "$obj.children('",
        "< " : "$obj.parent('",
        ">> " : "$obj.find('",
        "<< " : "$obj.closest('",
        "<> " : "$obj.parent().children('",
        "<<>> " : "$obj.parent().find('"
      };

      $.each(exp, function(index, value) {
        var regExp = new RegExp('^('+index+')');
        if ( target.match(regExp) )
          evalTarget = value + target.replace(regExp, '') + "')";
      }); 
    }

    eval( evalTarget + '.' + action + '(' + $obj.data('props') + ');' );
  }

  function setAction($obj, target, trigger, action ){
    $obj.on(trigger, function(event) {
      if( $(this).data('wrapper') && event.target !== this )
        return;

      if( ! $(this).data('allow-click') && trigger == 'click' )
        event.preventDefault();

      if( ! action ) action = $obj.data('action');
      doAction( $obj, target, action);
    });
  }

  // [data-action="toggle"]
  // [data-target="#selector"] указывает на объект над которым нужно производить действия
  // если не указан - ищет target у родителя. Если цель не будет найдена сработает на самом объекте
  $('[data-action]').each(function(index, el) {
    var target = $(this).data('target') ? $(this).data('target') : $(this).closest('[data-target]').data('target');
    if( ! target ) target = 'this';

    var trigger = $(this).data('trigger');
    if( trigger == 'load' ){
      doAction( $(this), target, $(this).data('action') );
      return;
    }
    else if( ! trigger ){
      trigger = 'click';
    }

    setAction( $(this), target, trigger );
  });

  //
  // Простые действия (target пишется в аттрибут действия)[data-fade-In="#target"]
  // если data-toggle !== 'false' повторный клик совершит обратное действие
  // на checkbox'ах и input'ах действует коровья суперсила ;D
  // 
  var easyActions = ['hide', 'show', 'fade-out', 'fade-in', 'slide-up', 'slide-down', 'toggle', 'fade-toggle', 'slide-toggle'];
  easyActions.forEach(function(item, i, arr) {
    $('[data-' + item + ']').each(function(index, el) {

      var spAct = item.split('-');
      action = spAct[0] + spAct[1].charAt(0).toUpperCase() + spAct[1].substr(1).toLowerCase();
      if( $(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio' ){
        
        if($(this).data('toggle') !== 'false')
          action = (spAct[0] == 'hide' || spAct[0] == 'show') ? 'toggle' : spAct[0] + 'Toggle';

        var cAction = item.replace('-', '');
        if( !$(this).is(':checked')){
          if( ['show', 'fadeIn', 'slideDown'].includes(cAction) ){
            cAction = cAction.replace('show', 'hide').replace('In', 'Out').replace('Down', 'Up');
          }
          else{
            cAction = cAction.replace('hide', 'show').replace('Out', 'In').replace('Up', 'Down');
          }
        }

        doAction( $(this), $(this).data(item), cAction );
        var trigger = $(this).data('trigger');
        if( ! trigger ) trigger = 'change';
      } else {
        var trigger = $(this).data('trigger');
        if( ! trigger ) trigger = 'click';
      }

      setAction( $(this), $(this).data(item), trigger, action );
    });
  });

  //
  // [data-text-replace="Текст"] Полностью изменить текст объекта на указанный в атрибуте
  // [data-text-replace-to="Тест"] Найти и изменить текст [data-text-replace] на новый
  // При повторном действии обращает текст обратно.
  //
  function replaceTextOnly($obj, from, to){
    var node = $obj.get(0);
    var childs = node.childNodes;
    for(var inc = 0; inc < childs.length; inc++) {
      if(childs[inc].nodeType == 3){ 
        if(childs[inc].textContent) {
          childs[inc].textContent = childs[inc].textContent.replace(from, to);
        } 
        else {
         childs[inc].nodeValue = childs[inc].nodeValue.replace(from, to);
        }
      }
    }
  }

  function textRepalce( $obj ){
    $wasObj = $obj;
    var textReplace = $obj.attr('data-text-replace');
    var textReplaceTo = $obj.attr('data-text-replace-to');
    var target  = $obj.data('target');

    if( target )
      $obj = $( target )

    if( textReplace && textReplaceTo ){
      if( ! $obj.attr('data-text-replaced') ){
        replaceTextOnly($obj, textReplace, textReplaceTo);
        $obj.attr('data-text-replaced', 'true');
      }
      else {
        replaceTextOnly($obj, textReplaceTo, textReplace);
        $obj.removeAttr('data-text-replaced');
      }
    }
    else {
      var text = $obj.text();
      $wasObj.attr('data-text-replace', text);
      $obj.text( textReplace );
    }
  }
  
  $('[data-text-replace]').each(function(index, el) {
    var trigger = $(this).data('trigger');
    if( ! trigger ) trigger = 'click';
    
    if( trigger == 'load' ){
      textRepalce( $(this) );
    }
    else {
      $(this).on(trigger, function(){
        textRepalce( $(this) );
      });
    }
  });
});