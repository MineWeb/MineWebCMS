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

        $.get($.Notification.defaultOptions.url.get+'/'+$.Notification.defaultOptions.notification_type, function(data) {

          for (var i = 0; i < data.length; i++) {
            if($.Notification.defaultOptions.limit > 0 && $.Notification.defaultOptions.limit == i) {
              break;
            }
            self.notifications[data[i]['id']] = data[i];
          }

          self.updateDOM()

        });

      },

      updateDOM: function() {

        var self = this;
        var notifications = self.notifications;
        var count = 0;
        for (var k in notification.notifications) {
          if(!notification.notifications[k].seen) {
            count++;
          }
        }

        if(count > 0) {
          $($.Notification.defaultOptions.indicator.element).each(function() {
            $(this).html($.Notification.defaultOptions.indicator.defaultContent+self.createIndicator(count).outerHTML);
          });
        } else {
          $($.Notification.defaultOptions.indicator.element).each(function() {
            $(this).html($.Notification.defaultOptions.indicator.defaultContent);
          });
        }

        this.generateNotificationsList()

      },

      generateNotificationsList: function() {

        // On créé le container
          if($.Notification.defaultOptions.list.container.type.length > 0) {
            var container = document.createElement($.Notification.defaultOptions.list.container.type);
            container.style.cssText = $.Notification.defaultOptions.list.container.style;
            container.className = $.Notification.defaultOptions.list.container.class;
          } else {
            var container = '';
          }

        // On parcours les notifications et on créé un élement par notif
          var self = this;
          Object.keys(this.notifications).sort().reverse().forEach(function(id) {

            id = parseInt(id);

            var el = document.createElement($.Notification.defaultOptions.list.notification.type);
            el.style.cssText = $.Notification.defaultOptions.list.notification.style;
            el.className = $.Notification.defaultOptions.list.notification.class;

            if(self.notifications[id].seen) {

              el.style.cssText += ' '+$.Notification.defaultOptions.list.notification.seen.element.style
              el.className += ' '+$.Notification.defaultOptions.list.notification.seen.element.class;

            }

            var content = $.Notification.defaultOptions.list.notification.content;
            content = content.replace('{ID}', id);
            content = content.replace('{ID}', id);
            content = content.replace('{CONTENT}', self.notifications[id].content);
            content = content.replace('{TIME}', self.notifications[id].time);
            content = content.replace('{MARK_AS_SEEN}', $.Notification.defaultOptions.messages.markAsSeen);

            el.innerHTML = content;

            if(self.notifications[id].seen) {
              var btn_seen = el.querySelector($.Notification.defaultOptions.list.notification.seen.btn.element);

              if(btn_seen != null && typeof btn_seen == "object" && Object.keys(btn_seen).length == 1) {

                btn_seen.style.cssText += ' '+$.Notification.defaultOptions.list.notification.seen.btn.style
                btn_seen.className += ' '+$.Notification.defaultOptions.list.notification.seen.btn.class;

                for (var i = 0; i < $.Notification.defaultOptions.list.notification.seen.btn.attr.length; i++) {

                  for (var attr in $.Notification.defaultOptions.list.notification.seen.btn.attr[i]) {

                    btn_seen.setAttribute(attr, $.Notification.defaultOptions.list.notification.seen.btn.attr[i][attr]);

                  }

                }

              }
            }

            if(self.notifications[id].from != null && $.Notification.defaultOptions.list.notification.from.type.length > 0) {

              var from_element = document.createElement($.Notification.defaultOptions.list.notification.from.type);
              from_element.style.cssText = $.Notification.defaultOptions.list.notification.from.style;
              from_element.className = $.Notification.defaultOptions.list.notification.from.class;

              var from_content = $.Notification.defaultOptions.list.notification.from.content;
              from_content = from_content.replace('{NOTIFIED_BY}', $.Notification.defaultOptions.messages.notifiedBy);
              from_content = from_content.replace('{FROM}', self.notifications[id].from);

              from_element.innerHTML = from_content;

              el.innerHTML = el.innerHTML.replace('{FROM}', from_element.outerHTML);

            } else {
              el.innerHTML = el.innerHTML.replace('{FROM}', '');
            }

            // On l'ajoute au container
              if($.Notification.defaultOptions.list.container.type.length > 0) {
                container.appendChild(el);
              } else {
                container += el.outerHTML;
              }

          });

        // On met dans l'HTML

          $($.Notification.defaultOptions.list.element).each(function() {

            $(this).html(container);

          });

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

      markAllAsSeen: function(time) {

        var self = this;

        if(time == undefined) {
          time = 0;
        }
        time = time*1000;

        setTimeout(function(){

          var url = $.Notification.defaultOptions.url.markAllAsSeen;

          $.get(url);

          for (var k in notification.notifications) {
            notification.notifications[k].seen = true;
          }

          self.updateDOM();

        }, time);

      }

    };

    $.Notification.defaultOptions = {
      'notification_type': 'user',
      'limit': 0,
      'indicator': {
        'element': '.notification-indicator',
        'style': {
          'position': 'absolute',
          'top': '-5px',
          'borderRadius': '30px'
        },
        'class': 'label label-danger',
        'defaultContent': ''
      },
      'messages': {
        'markAsSeen': '#',
        'notifiedBy': '#'
      },
      'urls': {
        'get': '#',
        'clear': '#',
        'clearAll': '#',
        'markAsSeen': '#',
        'markAllAsSeen': '#'
      },
      'list': {
        'element': '.notifications-list',
        'container': {
          'type': 'ul',
          'class': 'list-group',
          'style': 'margin-bottom:0;'
        },
        'notification': {
          'type': 'li',
          'class': 'list-group-item',
          'style': 'border-top-left-radius:0;border-top-right-radius:0;',
          'content': '<p>{CONTENT}<small class="pull-right"><em>{TIME}</em></small></p>{FROM}<div class="btn-group pull-right" style="margin-top:-5px;"><button type="button" class="btn btn-default btn-sm mark-as-seen" onClick="notification.markAsSeen({ID})" name="button"><abbr title="{MARK_AS_SEEN}"><i class="fa fa-check"></i></abbr></button><button type="button" class="btn btn-danger btn-sm" onClick="notification.clear({ID})" name="button"><i class="fa fa-times"></i></button></div><div class="clearfix"></div>',
          'from': {
            'type': 'small',
            'class': 'text-muted',
            'style': '',
            'content': '<u><em>{NOTIFIED_BY} {FROM}</em></u>'
          },
          'seen': {
            'element': {
              'style': 'opacity:0.6;',
              'class': ''
            },
            'btn': {
              'element': '.mark-as-seen',
              'style': '',
              'class': 'disabled active',
              'attr': [{'disabled': true}, {'onclick': ''}],
            }
          }
        }
      }
    };

}(jQuery));
