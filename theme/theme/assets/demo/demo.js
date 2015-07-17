

// Polyfills
//
(function() {
  // Array indexOf
  if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (searchElement, fromIndex) {
      if ( this === undefined || this === null ) {
        throw new TypeError( '"this" is null or not defined' );
      }
      var length = this.length >>> 0; // Hack to convert object.length to a UInt32
      fromIndex = +fromIndex || 0;
      if (Math.abs(fromIndex) === Infinity) {
        fromIndex = 0;
      }
      if (fromIndex < 0) {
        fromIndex += length;
        if (fromIndex < 0) {
          fromIndex = 0;
        }
      }
      for (;fromIndex < length; fromIndex++) {
        if (this[fromIndex] === searchElement) {
          return fromIndex;
        }
      }
      return -1;
    };
  }

  // Event listener
  if (!Event.prototype.preventDefault) {
    Event.prototype.preventDefault=function() {
      this.returnValue=false;
    };
  }
  if (!Event.prototype.stopPropagation) {
    Event.prototype.stopPropagation=function() {
      this.cancelBubble=true;
    };
  }
  if (!Element.prototype.addEventListener) {
    var eventListeners=[];
    
    var addEventListener=function(type,listener /*, useCapture (will be ignored) */) {
      var self=this;
      var wrapper=function(e) {
        e.target=e.srcElement;
        e.currentTarget=self;
        if (listener.handleEvent) {
          listener.handleEvent(e);
        } else {
          listener.call(self,e);
        }
      };
      if (type=="DOMContentLoaded") {
        var wrapper2=function(e) {
          if (document.readyState=="complete") {
            wrapper(e);
          }
        };
        document.attachEvent("onreadystatechange",wrapper2);
        eventListeners.push({object:this,type:type,listener:listener,wrapper:wrapper2});
        
        if (document.readyState=="complete") {
          var e=new Event();
          e.srcElement=window;
          wrapper2(e);
        }
      } else {
        this.attachEvent("on"+type,wrapper);
        eventListeners.push({object:this,type:type,listener:listener,wrapper:wrapper});
      }
    };
    var removeEventListener=function(type,listener /*, useCapture (will be ignored) */) {
      var counter=0;
      while (counter<eventListeners.length) {
        var eventListener=eventListeners[counter];
        if (eventListener.object==this && eventListener.type==type && eventListener.listener==listener) {
          if (type=="DOMContentLoaded") {
            this.detachEvent("onreadystatechange",eventListener.wrapper);
          } else {
            this.detachEvent("on"+type,eventListener.wrapper);
          }
          break;
        }
        ++counter;
      }
    };
    Element.prototype.addEventListener=addEventListener;
    Element.prototype.removeEventListener=removeEventListener;
    if (HTMLDocument) {
      HTMLDocument.prototype.addEventListener=addEventListener;
      HTMLDocument.prototype.removeEventListener=removeEventListener;
    }
    if (Window) {
      Window.prototype.addEventListener=addEventListener;
      Window.prototype.removeEventListener=removeEventListener;
    }
  }
})();


// Demo
//

(function() {

var storageSupported = (typeof(window.Storage)!=="undefined");

// Functions
//

var reloadPage = function () {
  location.reload();
}

var testTheme = function (name) {
  for (var j=0; j<demo_themes.length; j++) {
    if (demo_themes[j].name === name) {
      return demo_themes[j].name;
    }
  }
  return 'default';
}

var loadDemoSettings = function () {
  var result = {
    fixed_navbar: false,
    fixed_menu:   false,
    rtl:          false,
    menu_right:   false,
    theme:        'default'
  };

  if (storageSupported) {
    try {
      result.fixed_navbar = (window.localStorage.demo_fixed_navbar && window.localStorage.demo_fixed_navbar === '1');
      result.fixed_menu   = (window.localStorage.demo_fixed_menu && window.localStorage.demo_fixed_menu === '1');
      result.rtl          = (window.localStorage.demo_rtl && window.localStorage.demo_rtl === '1');
      result.menu_right   = (window.localStorage.demo_menu_right && window.localStorage.demo_menu_right === '1');
      result.theme        = testTheme((window.localStorage.demo_theme) ? window.localStorage.demo_theme : '');
      return result;
    } catch (e) {}
  } 

  var key, val, pos, demo_cookies = document.cookie.split(';');
  for (var i=0, l=demo_cookies.length; i < l; i++) {

    pos = demo_cookies[i].indexOf('=');
    key = demo_cookies[i].substr(0,  pos).replace(/^\s+|\s+$/g, '');
    val = demo_cookies[i].substr(pos + 1).replace(/^\s+|\s+$/g, '');

    if (key === 'demo_fixed_navbar') {
      result.fixed_navbar = (val === '1') ? true : false;
    
    } else if (key === 'demo_fixed_menu') {
      result.fixed_menu = (val === '1') ? true : false;
    
    } else if (key === 'demo_rtl') {
      result.rtl = (val === '1') ? true : false;
    
    } else if (key === 'demo_menu_right') {
      result.menu_right = (val === '1') ? true : false;
    
    } else if (key === 'demo_theme') {
      result.theme = testTheme(val);
    }
  }

  return result;
}

var saveDemoSettings = function () {
  if (storageSupported) {
    try {
      window.localStorage.demo_fixed_navbar = escape((demo_settings.fixed_navbar) ? '1' : '0');
      window.localStorage.demo_fixed_menu   = escape((demo_settings.fixed_menu) ? '1' : '0');
      window.localStorage.demo_rtl          = escape((demo_settings.rtl) ? '1' : '0');
      window.localStorage.demo_menu_right   = escape((demo_settings.menu_right) ? '1' : '0');
      window.localStorage.demo_theme        = escape(demo_settings.theme);
      return;
    } catch (e) {}
  }

  document.cookie = 'demo_fixed_navbar=' + escape((demo_settings.fixed_navbar) ? '1' : '0');
  document.cookie = 'demo_fixed_menu='   + escape((demo_settings.fixed_menu) ? '1' : '0');
  document.cookie = 'demo_rtl='          + escape((demo_settings.rtl) ? '1' : '0');
  document.cookie = 'demo_menu_right='   + escape((demo_settings.menu_right) ? '1' : '0');
  document.cookie = 'demo_theme='        + escape(demo_settings.theme);
}

var getThemesTemplate = function () {
  result = '';
  for (var i=0, l=demo_themes.length-1; i <= l; i++) {
    if (i % 2 == 0) {
      result = result + '<div class="demo-themes-row">';
      result = result + '<a href="#" class="demo-theme" data-theme="' + demo_themes[i].name + '"><div class="theme-preview"><img src="' + demo_themes[i].img + '" alt=""></div><div class="overlay"></div><span>' + demo_themes[i].title + '</span></a>';
    } else {
      result = result + '<a href="#" class="demo-theme" data-theme="' + demo_themes[i].name + '"><div class="theme-preview"><img src="' + demo_themes[i].img + '" alt=""></div><div class="overlay"></div><span>' + demo_themes[i].title + '</span></a>';
      result = result + '</div>';
    }
    if (i == l && i % 2 == 0) {
      result = result + '<div class="demo-theme"></div></div>';
    }
  }
  return result;
}

var activateTheme = function (btns) {
  document.body.className = document.body.className.replace(/theme\-[a-z0-9\-\_]+/ig, 'theme-' + demo_settings.theme);
  
  if (! btns) return;
  btns.removeClass('dark');
  if (demo_settings.theme != 'clean' && demo_settings.theme != 'white') {
    btns.addClass('dark');
  }
}


// Load and apply settings
//

var panel_width = 260;

var demo_themes = [
  { name: 'default', title: 'Default', img: 'assets/demo/themes/default.png' },
  { name: 'asphalt', title: 'Asphalt', img: 'assets/demo/themes/asphalt.png' },
  { name: 'purple-hills', title: 'Purple Hills', img: 'assets/demo/themes/purple-hills.png' },
  { name: 'adminflare',  title: 'Adminflare', img: 'assets/demo/themes/adminflare.png' },
  { name: 'dust',  title: 'Dust', img: 'assets/demo/themes/dust.png' },
  { name: 'frost',  title: 'Frost', img: 'assets/demo/themes/frost.png' },
  { name: 'fresh',  title: 'Fresh', img: 'assets/demo/themes/fresh.png' },
  { name: 'silver',  title: 'Silver', img: 'assets/demo/themes/silver.png' },
  { name: 'clean',  title: 'Clean', img: 'assets/demo/themes/clean.png' },
  { name: 'white',  title: 'White', img: 'assets/demo/themes/white.png' }
];

var demo_settings = loadDemoSettings();

if (demo_settings.fixed_navbar) {
  document.body.className = document.body.className + ' main-navbar-fixed';
}

if (demo_settings.fixed_menu) {
  document.body.className = document.body.className + ' main-menu-fixed';
}

if (demo_settings.rtl) {
  document.body.className = document.body.className + ' right-to-left';
}

if (demo_settings.menu_right) {
  document.body.className = document.body.className + ' main-menu-right';
}

activateTheme();


// Templates
//

var demo_styles = [
  '#demo-themes {',
  ' display: table;',
  ' table-layout: fixed;',
  ' width: 100%;',
  ' border-bottom-left-radius: 5px;',
  ' overflow: hidden;',
  '}',
  '#demo-themes .demo-themes-row {',
  ' display: block;',
  '}',
  '#demo-themes .demo-theme {',
  ' display: block;',
  ' float: left;',
  ' text-align: center;',
  ' width: 50%;',
  ' padding: 25px 0;',
  ' color: #888;',
  ' position: relative;',
  ' overflow: hidden;',
  '}',
  '#demo-themes .demo-theme:hover {',
  ' color: #fff;',
  '}',
  '#demo-themes .theme-preview {',
  ' width: 100%;',
  ' position: absolute;',
  ' top: 0;',
  ' left: 0;',
  ' bottom: 0;',
  ' overflow: hidden !important;',
  '}',
  '#demo-themes .theme-preview img {',
  ' width: 100%;',
  ' position: absolute;',
  ' display: block;',
  ' top: 0;',
  ' left: 0;',
  '}',
  '#demo-themes .demo-theme .overlay {',
  ' background: #1d1d1d;',
  ' background: rgba(0,0,0,.8);',
  ' position: absolute;',
  ' top: 0;',
  ' bottom: 0;',
  ' left: -100%;',
  ' width: 100%;',
  '}',
  '#demo-themes .demo-theme span {',
  ' position: relative;',
  ' color: #fff;',
  ' color: rgba(255,255,255,0);',
  '}',
  '#demo-themes .demo-theme span,',
  '#demo-themes .demo-theme .overlay {',
  ' -webkit-transition: all .3s;',
  ' -moz-transition: all .3s;',
  ' -ms-transition: all .3s;',
  ' -o-transition: all .3s;',
  ' transition: all .3s;',
  '}',
  '#demo-themes .demo-theme.active span,',
  '#demo-themes .demo-theme:hover span {',
  ' color: #fff;',
  ' color: rgba(255,255,255,1);',
  '}',
  '#demo-themes .demo-theme.active .overlay,',
  '#demo-themes .demo-theme:hover .overlay {',
  ' left: 0;',
  '}',
  '#demo-settings {',
  ' position: fixed;',
  ' right: -' + (panel_width + 10) + 'px;',
  ' width: ' + (panel_width + 10) + 'px;',
  ' top: 70px;',
  ' padding-right: 10px;  ',
  ' background: #333;',
  ' border-radius: 5px;',
  ' -webkit-transition: all .3s;',
  ' -moz-transition: all .3s;',
  ' -ms-transition: all .3s;',
  ' -o-transition: all .3s;',
  ' transition: all .3s;',
  ' -webkit-touch-callout: none;',
  ' -webkit-user-select: none;',
  ' -khtml-user-select: none;',
  ' -moz-user-select: none;',
  ' -ms-user-select: none;',
  ' user-select: none;',
  ' z-index: 999998;',
  '}',
  '#demo-settings.open {',
  ' right: -10px;',
  '}',
  '#demo-settings > .header {',
  ' position: relative;',
  ' z-index: 100000;',
  ' line-height: 20px;',
  ' background: #444;',
  ' color: #fff;',
  ' font-size:  11px;',
  ' font-weight: 600;',
  ' padding: 10px 20px;',
  ' margin: 0;',
  '}',
  '#demo-settings > div {',
  ' position: relative;',
  ' z-index: 100000;',
  ' background: #282828;',
  ' border-bottom-right-radius: 5px;',
  ' border-bottom-left-radius: 5px;',
  '}',
  '#demo-settings-toggler {',
  ' font-size: 21px;',
  ' width: 50px;',
  ' height: 40px;',
  ' padding-right: 10px;',
  ' position: absolute;',
  ' left: -40px;',
  ' top: 0;',
  ' background: #444;',
  ' text-align: center;',
  ' line-height: 40px;',
  ' color: #fff;',
  ' border-radius: 5px;',
  ' z-index: 99999;',
  ' text-decoration: none !important;',
  ' -webkit-transition: color .3s;',
  ' -moz-transition: color .3s;',
  ' -ms-transition: color .3s;',
  ' -o-transition: color .3s;',
  ' transition: color .3s;',
  '}',
  '#demo-settings.open #demo-settings-toggler {',
  ' font-size: 16px;',
  ' color: #888;',
  '}',
  '#demo-settings.open #demo-settings-toggler:hover {',
  ' color: #fff;',
  '}',
  '#demo-settings.open #demo-settings-toggler:before {',
  ' content: "\\f00d";',
  '}',
  '#demo-settings-list {',
  ' padding: 0;',
  ' margin: 0;',
  '}',
  '#demo-settings-list li {',
  ' padding: 0;',
  ' margin: 0;',
  ' list-style: none;',
  ' position: relative;',
  '}',
  '#demo-settings-list li > span {',
  ' line-height: 20px;',
  ' color: #fff;',
  ' display: block;',
  ' padding: 12px 20px;',
  ' cursor: pointer;',
  '}',
  '#demo-settings-list li + li {',
  ' border-top: 1px solid #333;',
  '}',
  '#demo-settings .demo-checkbox {',
  ' position: absolute;',
  ' right: 20px;',
  ' top: 12px;',
  '}',
  '.right-to-left #demo-settings {',
  ' left: -270px;',
  ' right: auto;',
  ' padding-right: 0;',
  ' padding-left: 10px;',
  '}',
  '.right-to-left #demo-settings.open {',
  ' left: -10px;',
  ' right: auto;',
  '}',
  '.right-to-left #demo-settings-toggler {',
  ' padding-left: 10px;',
  ' padding-right: 0;',
  ' left: auto;',
  ' right: -40px;',
  '}',
  '.right-to-left #demo-settings .demo-checkbox {',
  ' right: auto;',
  ' left: 20px;',
  '}'
];

var demo_template = [
  '<div id="demo-settings">',
  ' <a href="#" id="demo-settings-toggler" class="fa fa-cogs"></a>',
  ' <h5 class="header">SETTINGS</h5>',
  ' <div>',
  '   <ul id="demo-settings-list">',
  '     <li class="clearfix">',
  '       <span>Fixed navbar</span>',
  '       <div class="demo-checkbox"><input type="checkbox" id="demo-fixed-navbar" class="demo-settings-switcher" data-class="switcher-sm"' + ((demo_settings.fixed_navbar) ? ' checked="checked"' : '' ) + '></div>',
  '     </li>',
  '     <li class="clearfix">',
  '       <span>Fixed main menu</span>',
  '       <div class="demo-checkbox"><input type="checkbox" id="demo-fixed-menu" class="demo-settings-switcher" data-class="switcher-sm"' + ((demo_settings.fixed_menu) ? ' checked="checked"' : '' ) + '></div>',
  '     </li>',
  '     <li class="clearfix">',
  '       <span>Right-to-left direction</span>',
  '       <div class="demo-checkbox"><input type="checkbox" id="demo-rtl" class="demo-settings-switcher" data-class="switcher-sm"' + ((demo_settings.rtl) ? ' checked="checked"' : '' ) + '></div>',
  '     </li>',
  '     <li class="clearfix">',
  '       <span>Main menu on the right</span>',
  '       <div class="demo-checkbox"><input type="checkbox" id="demo-menu-rigth" class="demo-settings-switcher" data-class="switcher-sm"' + ((demo_settings.menu_right) ? ' checked="checked"' : '' ) + '></div>',
  '     </li>',
  '     <li class="clearfix">',
  '       <span>Hide main menu</span>',
  '       <div class="demo-checkbox"><input type="checkbox" id="demo-no-menu" class="demo-settings-switcher" data-class="switcher-sm"></div>',
  '     </li>',
  '   </ul>',
  ' </div>',
  ' <h5 class="header">THEMES</h5>',
  ' <div id="demo-themes">',
      getThemesTemplate(),
  ' </div>',
  '</div>'
];

// Initialize
//

window.addEventListener("load", function () {
  activateTheme($('#main-menu .btn-outline'));
  $('head').append($('<style>\n' + demo_styles.join('\n') + '\n</style>'));
  $('body').append($(demo_template.join('\n')));

  // Activate theme
  $('#demo-themes .demo-theme[data-theme="' + demo_settings.theme + '"]').addClass('active');


  // Add callbacks
  //

  // Initialize switchers
  $('.demo-settings-switcher').switcher({
    theme: 'square',
    on_state_content: '<span class="fa fa-check" style="font-size:11px;"></span>',
    off_state_content: '<span class="fa fa-times" style="font-size:11px;"></span>'
  });

  // Demo panel toggle
  $('#demo-settings-toggler').click(function () {
    $('#demo-settings').toggleClass('open');
    return false;
  });

  // Toggle switchers on label click
  $('#demo-settings-list li > span').click(function () {
    $(this).parents('li').find('.switcher').click();
  });

  // Fix/unfix main navbar
  $('#demo-fixed-navbar').on($('html').hasClass('ie8') ? "propertychange" : "change", function () {
    var uncheck_menu_chk = (! $(this).is(':checked') && $('#demo-fixed-menu').is(':checked')) ? true : false;
    demo_settings.fixed_navbar = $(this).is(':checked');
    if (uncheck_menu_chk) {
      $('#demo-fixed-menu').switcher('off');
    } else {
      saveDemoSettings();
      reloadPage();
    }
  });

  // Fix/unfix main menu
  $('#demo-fixed-menu').on($('html').hasClass('ie8') ? "propertychange" : "change", function () {
    var check_navbar_chk = ($(this).is(':checked') && ! $('#demo-fixed-navbar').is(':checked')) ? true : false;
    demo_settings.fixed_menu = $(this).is(':checked');
    if (check_navbar_chk) {
      $('#demo-fixed-navbar').switcher('on');
    } else {
      saveDemoSettings();
      reloadPage();
    }
  });

  // RTL
  $('#demo-rtl').on($('html').hasClass('ie8') ? "propertychange" : "change", function () {
    demo_settings.rtl = $(this).is(':checked');
    saveDemoSettings();
    reloadPage();
  });

  // Fix/unfix main menu
  $('#demo-menu-rigth').on($('html').hasClass('ie8') ? "propertychange" : "change", function () {
    demo_settings.menu_right = $(this).is(':checked');
    saveDemoSettings();
    reloadPage();
  });
  
  // Hide/show main menu
  $('#demo-no-menu').on($('html').hasClass('ie8') ? "propertychange" : "change", function () {
    if ($(this).is(':checked')) {
      $('body').addClass('no-main-menu');
    } else {
      $('body').removeClass('no-main-menu');
    }
  });

  // Change theme
  $('#demo-themes .demo-theme').on('click', function () {
    if ($(this).hasClass('active')) return;
    $('#demo-themes .active').removeClass('active');
    $(this).addClass('active');
    demo_settings.theme = $(this).attr('data-theme');
    saveDemoSettings();
    activateTheme($('#main-menu .btn-outline'));
    return false;
  });


  // Custom menu content demo
  //

  $('#menu-content-demo .close').click(function () {
    var $p = $(this).parents('.menu-content');
    $p.addClass('fadeOut');
    setTimeout(function () {
      $p.css({ height: $p.outerHeight(), overflow: 'hidden' }).animate({'padding-top': 0, height: $('#main-navbar').outerHeight()}, 500, function () {
        $p.remove();
      });
    }, 300);
    return false;
  });
});


})();
