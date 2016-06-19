(function ($) {

    $.Notification = function (config) {

      for (var key in config) {
        $.Notification.defaultOptions[key] = config[key];
      }

      this.getNotifications();

    };

    $.Notification.prototype = {

      createIndicator: function(count) {
        var indicator = document.createElement('span');

        indicator.className = $.Notification.defaultOptions.indicator.class;

        for (var property in $.Notification.defaultOptions.indicator.style) {
          indicator.style[property] = $.Notification.defaultOptions.indicator.style[property];
        }

        indicator.innerText = count;

        return indicator;
      },

      getNotifications: function() {

        var self = this;
        self.notifications = {};

        $.get($.Notification.defaultOptions.url.get, function(data) {

          for (var i = 0; i < data.length; i++) {
            self.notifications[data[i]['id']] = data[i];
          }

          self.updateDOM()

        });

      },

      updateDOM: function() {

        var notifications = this.notifications;
        var count = 0;
        for (var k in notification.notifications) {
          if(!notification.notifications[k].seen) {
            count++;
          }
        }

        if(count > 0) {
          $($.Notification.defaultOptions.indicator.element).html(this.createIndicator(count));
        } else {
          $($.Notification.defaultOptions.indicator.element).html('');
        }

      },

      clear: function(id) {
        var url = $.Notification.defaultOptions.url.clear;
        url = url.replace('NOTIF_ID', id);

        $.get(url);

        delete this.notifications[id];

        this.updateDOM();
      },
      clearAll: function() {
        var url = $.Notification.defaultOptions.url.clearAll;

        $.get(url);

        this.notifications = {};

        this.updateDOM();
      },

      markAsSeen: function(id) {
        var url = $.Notification.defaultOptions.url.markAsSeen;
        url = url.replace('NOTIF_ID', id);

        $.get(url);

        this.notifications[id].seen = true;

        this.updateDOM();
      },

      markAllAsSeen: function(id) {
        var url = $.Notification.defaultOptions.url.markAllAsSeen;

        $.get(url);

        for (var k in notification.notifications) {
          notification.notifications[k].seen = true;
        }

        this.updateDOM();
      }

    };

    $.Notification.defaultOptions = {
      'indicator': {
        'element': '#notification-indicator',
        'style': {
          'position': 'absolute',
          'top': '-5px',
          'borderRadius': '30px'
        },
        'class': 'label label-danger'
      },
      'urls': {
        'get': '#',
        'clear': '#',
        'clearAll': '#',
        'markAsSeen': '#',
        'markAllAsSeen': '#'
      }
    };

}(jQuery));
