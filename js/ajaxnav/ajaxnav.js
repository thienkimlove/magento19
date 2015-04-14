var ajaxnav = {
    clickableObservers: false,
    changeableObservers: false,
    initialize: function(clickableObservers, changeableObservers) {
        this.clickableObservers = clickableObservers;
        this.changeableObservers = changeableObservers;

        this.setUrls();
        this.bindEvents();
    },
    setUrls: function() {
        var self = this;
        /*$$(self.clickableObservers).each(function(e){
            var url = e.readAttribute('href');
            self.setUrl(url, e);
        });*/
        $$(self.changeableObservers + ' option').each(function(e){
            var value = e.readAttribute('value');
            self.setValue(value, e);
        });
    },
    /*setUrl: function(url, e) {
        if (url.search('ajax=true') == -1 && url.indexOf('?') == -1)
            e.writeAttribute('href', url + '?ajax=true');
        else if (url.search('ajax=true') == -1 && url.indexOf('?') !== -1)
            e.writeAttribute('href', url + '&ajax=true');
    },*/
    setValue: function(value, e) {
        if (value.search('ajax=true') == -1 && value.indexOf('?') == -1)
            e.writeAttribute('value', value + '?ajax=true');
        else if (value.search('ajax=true') == -1 && value.indexOf('?') !== -1)
            e.writeAttribute('value', value + '&ajax=true');
    },
    bindEvents: function () {
        var self = this;
        $$(self.clickableObservers).each(function(e){
            $(e).observe('click', function(event) {
                var url = $(e).readAttribute('href');
                self.ajaxcall(url);
                Event.stop(event);
            });
        });
    },
    ajaxcall: function(url) {
        var contentElement = $$('.category-products').first();
        var appendTitle = false;
        if (typeof(contentElement) == 'undefined') {
            contentElement = $$('.col-main').first();
            appendTitle = true;
        }
        var leftElement = $$('.block-layered-nav').first();

        var self = this;
        new Ajax.Request(url, {
            onCreate    : function() {
                contentElement.setOpacity(0.5);
                if (leftElement)
                    leftElement.setOpacity(0.5);
            },
            onSuccess   : function(response) {
                // Handle the response content...
                try{
                    var res = response.responseText.evalJSON();
                    if (res) {
                        if (!appendTitle) {
                            $(contentElement).replace(res.content);
                        } else {
                            var title = $(contentElement).down('.category-title');
                            $(contentElement).innerHTML = '';
                            $(contentElement).insert(title);
                            $(contentElement).insert(res.content);
                        }

                        
                        if (leftElement)
                            $(leftElement).replace(res.left);

                        self.initialize(self.clickableObservers, self.changeableObservers);
                    } else {
                        document.location.reload(true);
                    }
                } catch(e) {
                //window.location.href = url;
                //document.location.reload(true);
                }
            },
            onComplete  : function() {
                contentElement.setOpacity(1);
                leftElement.setOpacity(1);
            }
        });
    }
};

var oldSetLocation = setLocation;
var setLocation = (function() {
    return function(url){
        if( url.search('ajax=true') != -1 ) {
            ajaxnav.ajaxcall(url);
        } else {
            oldSetLocation(url);
        }
    };
})();

document.observe("dom:loaded", function() {
    ajaxnav.initialize('.block-layered-nav a, .toolbar a', '.toolbar select');
});