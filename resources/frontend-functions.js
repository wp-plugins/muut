/**
 * Contains the functionality that will be used for Muut on the frontend
 * Version 1.0
 * Requires jQuery
 *
 * Copyright (c) 2014 Moot, Inc.
 * Licensed under MIT
 * http://www.opensource.org/licenses/mit-license.php
 */
var NEPER = 2.718;

var muutRpc = {};

muut(function(app, rpc) {
  muutRpc = rpc;

  muutRpc.on('receive', function(object) {
    if ( typeof object.params != 'undefined' && object.params[0] == 'reply' ) {
      muutRpc.emit('reply', object.params[1], object.params[2]);
    } else if ( typeof object.params != 'undefined' && ( object.params[0] == 'post' || object.params[0] == 'moot' ) ) {
      muutRpc.emit('post', object.params[1], object.params[2]);
    }
  });
});

var __muut_frontend_strings = muut_frontend_functions_localized;

jQuery(document).ready( function($) {

  // Adds the comments navigation link to the forum navigation.
  var body = $('body');
  if ( body.hasClass('muut-forum-home') && !body.hasClass('muut-custom-nav') && typeof muut_show_comments_in_nav != 'undefined' && muut_show_comments_in_nav ) {
    // Make sure the title of the comments page is "Comments".
    muutObj().on( 'load', function(page) {
      var comments_link_class = "unlisted ";
      if (typeof( muut_comments_base_domain ) == 'string' && page.relativePath == '/' + muut_comments_base_domain) {
        page.title = "Comments";
        var comments_link_class = "m-selected";
      }
      if ($('#muut_site_comments_nav').length == 0) {
        $(".m-forums").append('<p><a id="muut_site_comments_nav" href="#!/' + muut_comments_base_domain + '" title="' + __muut_frontend_strings.comments + '" data-channel="' + __muut_frontend_strings.comments + '"  class="' + comments_link_class + '">' + __muut_frontend_strings.comments + '</a></p>');
      }
    });

    // Make sure links to trending posts work even from the forum page.
    if ( typeof muut_current_page_permalink == 'string' ) {
      $('a[href^="' + muut_current_page_permalink + '#!"]').one('click', function(e) {
       var el = $(this);
        var page = el.attr('href').slice(muut_current_page_permalink.length + 2);
        muutObj().load(page);
      });
    }
  }

  $.fn.extend({
    // The function that is used to initialize all m-facelink anchors below the jQuery element collection calling the function.
    facelinkinit: function( force_refresh ) {
      var online_usernames = Array();
      muutObj().online.forEach(function(user) {
        online_usernames.push(user.username);
      });
      if ($(this).hasClass('m-facelink')) {
        var facelinks = $(this);
      } else {
        var facelinks = $(this).find('.m-facelink');
      }
      $.each(facelinks, function() {
        var current_user_name = $(this).data('href').substr(4);

        if (force_refresh || !$(this).hasClass('m-facelink-inited') ) {
          // Add the username tooltip. This needs to be done on every call as it can disappear otherwise based on other Muut client behavior.
          $(this).tooltip2({prefix: 'm-', delayIn: 0, delayOut: 0}).appendTo($(this));
          if($(this).hasClass('m-is-admin')) {
            $(this).find(".m-tooltip").append("<em> (" + __muut_frontend_strings.admin + ")</em>");
          }
        }
        // If the facelinks are not marked as already having been initialized...
        if (!$(this).hasClass('m-facelink-inited') ) {
          // Load the user page if the portrait is clicked.
          $(this).on('click', function(e) {
            var el = $(this);
            var page = el.data('href').substr(2);
            muutObj().load(page);
          });
          // This class is required for tooltips to work--something on the Muut end.
          $(this).addClass('m-online');
          if($.inArray(current_user_name, online_usernames) >= 0) {
            $(this).addClass('m-user-online_' + current_user_name);
          } else {
            // This hides the "online" circle, which has been added by the required m-online.
            // Ugly, I know.
            $(this).addClass('m-wp-hideafter');
          }
          $(this).addClass('m-facelink-inited');
        } else {
          if ( !$(this).hasClass('m-online') ) {
            $(this).addClass('m-online');
          }
          if($.inArray(current_user_name, online_usernames) == -1) {
            // This hides the "online" circle, which has been added by the required m-online.
            // Ugly, I know.
            $(this).addClass('m-wp-hideafter');
          } else {
            // Show it again on log-in.
            $(this).removeClass('m-wp-hideafter');
          }
        }
      });
    },

    // Increase the count of a given element.
    increasecount: function(selector) {
      $(this).each( function() {
        var count_element = $(this).find(selector);
        count_element.text(parseInt(count_element.text()) + 1);
      });
    },

    decreasecount: function(selector) {
      $(this).each( function() {
        var count_element = $(this).find(selector);
        if (parseInt(count_element.text()) > 0 ) {
          count_element.text(parseInt(count_element.text()) - 1);
        }
      });
    }
  });
});

// Function that contains the template for avatars.
var get_user_avatar_html = function(user) {
  var is_admin_class = '';
  if(user.is_admin) {
    is_admin_class = 'm-is-admin ';
  }

  // Construct the actual username without the '@'.
  if(user.path.substr(0,1) == '@') {
    var username = user.path.substr(1);
  }

  var username_for_class = username.replace(' ', '_');
  var online_user_href_markup = '';
  if ( typeof muut_forum_page_permalink == 'string' ) {
    online_user_href_markup = 'href="' + muut_forum_page_permalink + '#!/' + user.path + '"';
  }
  // Return the HTML for the face.
  var html = '<a class="m-facelink ' + is_admin_class + 'm-online m-user-online_' + username_for_class +'" title="' + user.displayname + '" ' + online_user_href_markup + ' data-href="#!/' + user.path + '"><img class="m-face" src="' + user.img + '"></a>';
  return html;
};

// Function that tidies the usernames of specific bad (but important) characters.
var tidy_muut_username = function(username) {
  if (typeof(username) == 'string'){
    username = (username.replace(':', '\\:')).replace(' ', '_');
  }
  return username;
};

// Generate Muut-style shorthand string for timestamp (1s, 4d, etc.)
var muut_time_format = function(timestamp) {
  var time_since = Math.round(timestamp / 1000);
  var list_time = '';
  if ( time_since < 60 ) {
    list_time = __muut_frontend_strings.just_now;
  } else if ( time_since < ( 60 * 60 ) ) {
    list_time = String(Math.floor( time_since / 60 )) + __muut_frontend_strings.minutes_abbreviation;
  } else if ( time_since < ( 60 * 60 * 24 ) ) {
    list_time = String(Math.floor( time_since / ( 60 * 60 ) )) + __muut_frontend_strings.hours_abbreviation;
  } else if ( time_since < ( 60 * 60 * 24 * 7 ) ) {
    list_time = String(Math.floor( time_since / ( 60 * 60 * 24 ) )) + __muut_frontend_strings.days_abbreviation;
  } else {
    list_time = String(Math.floor( time_since / ( 60 * 60 * 24 * 7 ) )) + __muut_frontend_strings.weeks_abbreviation;
  }
  return list_time;
};