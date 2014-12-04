/*globals Container, $, Modal, TabPanelElement, Panel, Base*/
/**
 * @class Window
 * Handle window objects
 * @extend Container
 *
 * @constructor
 * Creates a new instance of the window's class
 * @param {Object} options
 */
var Window = function (options) {
    Container.call(this, options);
    /**
     * Defines the window's title
     * @type {String}
     */
    this.title = null;
    /**
     * Defines the window's modal property
     * @type {Boolean}
     */
    this.modal = null;
    /**
     * Defines the Modal Object to handle modal windows
     * @type {Modal}
     */
    this.modalObject = null;
    /**
     * Defines the window header HTML Element where are placed the title label HTML Element and the Close Button HTML Element
     * @type {HTMLElement}
     */
    this.windowHeader = null;
    /**
     * Defines the Close Button HTML Element
     * @type {HTMLElement}
     */
    this.closeButtonObject = null;
    /**
     * Defines the title label HTML Element
     * @type {HTMLElement}
     */
    this.titleLabelObject = null;
    /**
     * Records the loading state of the window
     * @type {Boolean}
     * @private
     */
    this.loaded = false;

    /**
     * Defines the DestroyOnHide property
     * @type {Boolean}
     */
    this.destroyOnHide = null;

    /**
     * Defines the modal handler HTML Element pointer
     * @type {HTMLElement}
     */
    this.modalHandler = null;

    /**
     * Defines the close button property
     * @type {Boolean}
     */
    this.closeButton = null;
    /**
     * Defines the window's panel objects
     * @type {Array<Panel>}
     */
    this.panels = [];
    /**
     * Defines the HTML Element to apply the modal mask
     * @type {HTMLElement}
     * @private
     */
    this.modalContainer = null;
    /**
     * Defines the HTML Element which contains the tabs
     * @type {HTMLElement}
     */
    this.tabsContainer = null;
    /**
     * Defines the current selected tab/panel
     * @type {[TabPanelElement]}
     */
    this.selectedTab = null;
    Window.prototype.initObject.call(this, options);
};

Window.prototype = new Container();

/**
 * Defines the object's type
 * @type {String}
 */
Window.prototype.type = "Window";

/**
 * Initialize the object with the default values
 */
Window.prototype.initObject = function (options) {
    var defaults = {
        title: 'No Title',
        modal: true,
        modalHandler: null,
        destroyOnHide: false,
        closeButton: true,
        panels: []
    };
    $.extend(true, defaults, options);
    this.setTitle(defaults.title)
        .setModalHandler(defaults.modalHandler)
        .setModal(defaults.modal)
        .setVisible(false)
        .setCloseButton(defaults.closeButton)
        .setDestroyOnHide(defaults.destroyOnHide)
        .setPanels(defaults.panels);

    this.modalContainer = $('body');
};

/**
 * Sets the window's title
 * @param {String} text
 */
Window.prototype.setTitle = function (text) {
    this.title = text;
    if (this.titleLabelObject) {
        this.titleLabelObject.innerHTML = text;
    }
    return this;
};

/**
 * Sets the Modal handler function
 * @param {Function} fn
 * @return {*}
 */
Window.prototype.setModalHandler = function (fn) {
    this.modalHandler = fn;
    return this;
};

/**
 * Sets the window's modal property
 * @param {Boolean} value
 */
Window.prototype.setModal = function (value) {
    if (value) {
        this.modalObject = new Modal({
            clickHandler: this.modalHandler
        });
    } else {
        this.modalObject = null;
    }
    this.modal = value;
    return this;
};

/**
 * Sets the destroy on hide property
 * @param {Boolean} value
 * @return {*}
 */
Window.prototype.setDestroyOnHide = function (value) {
    this.destroyOnHide = value;
    return this;
};

/**
 * Sets the close Button property
 * @param {Boolean} value
 * @return {*}
 */
Window.prototype.setCloseButton = function (value) {
    this.closeButton = value;
    return this;
};

Window.prototype.onTabSelectedHandler = function() {
    var that = this;
    return function() {
        var newContent;
        if(this !== that.selectedTab) {
            $(that.selectedTab.unselect().getContent().getHTML()).detach();
            that.selectedTab = this.select();
            newContent = that.selectedTab.getContent();
            newContent.setHeight($(that.body).innerHeight());
            that.body.appendChild(newContent.getHTML());
            if(typeof newContent.load === 'function') {
                newContent.load();
            }
        }
    };
};

Window.prototype.clearPanels = function() {
    var i;
    for(i = 0; i < this.panels.length; i += 1) {
        $(this.panels[i].getTab()).remove();
        $(this.panels[i].getContent().getHTML()).remove();
    }
    this.panels = [];
    return this;
};

Window.prototype.setPanels = function(panels) {
    var i;
    if(!(panels.hasOwnProperty("length") && typeof panels.push === 'function')) {
        return this;
    }
    this.clearPanels();

    for(i = 0; i < panels.length; i += 1) {
        this.addPanel(panels[i]);
    }

    return this;
};

/**
 * Adds a panel to the container window
 * @param {Panel} p
 */
Window.prototype.addPanel = function (panel) {
    var p = panel.panel, tabPanelElement;

    if(panel instanceof TabPanelElement) {
        tabPanelElement = panel;
    } else if(panel instanceof Panel) {
        p = panel;
        tabPanelElement = new TabPanelElement({
            content: panel
        });
    } else if(p instanceof Panel) {
        tabPanelElement = new TabPanelElement({
            title: panel.title,
            content: p
        });
    } else {
        return this;
    }

    tabPanelElement.onClick = this.onTabSelectedHandler();
    tabPanelElement.setParent(this);
    this.panels.push(tabPanelElement);
    if(this.panels.length === 1) {
        this.selectedTab = tabPanelElement;
    }
    if (this.loaded) {
        this.tabsContainer.appendChild(tabPanelElement.getTab());
        if(this.panels.length === 1) {
            this.body.appendChild(p.getHTML());
        }
    }
    p.setParent(this);

    return this;
};

Window.prototype.getPanel = function(i) {
    return this.panels[i];
};

Window.prototype.getPanels = function() {
    return this.panels;
};

/**
 * Creates the HTML Element fot the object
 * @return {*}
 */
Window.prototype.createHTML = function () {
    var marginProps, closeBtn, titleLabel, windowHeader, tabsContainer, i;
    Container.prototype.createHTML.call(this);
    marginProps = '-' + parseInt(this.height / 2, 10) + 'px 0 0 -' + parseInt(this.width / 2, 10) + 'px';
    this.style.addClasses(['adam-window']);
    this.style.addProperties({
        'z-index': 1033,
        'left': '50%',
        'top': '50%',
        'margin': marginProps
    });

    this.height -= 16;
    this.html.style.height = this.height + "px";

    windowHeader = this.createHTMLElement('div');
    windowHeader.className = 'adam-window-header';

    titleLabel = this.createHTMLElement('label');
    titleLabel.className = 'adam-window-title';
    titleLabel.innerHTML = this.title || "&nbsp;";
    titleLabel.title = titleLabel.innerHTML;

    if (this.closeButton) {
        closeBtn = this.createHTMLElement('span');
        closeBtn.className = 'adam-window-close';
        windowHeader.appendChild(closeBtn);
        this.html.insertBefore(windowHeader, this.body);
        this.closeButtonObject = closeBtn;
    } else {
        this.html.insertBefore(windowHeader, this.body);
    }

    windowHeader.appendChild(titleLabel);

    tabsContainer = this.createHTMLElement("ul");
    tabsContainer.className = 'adam-tabs';
    this.html.insertBefore(tabsContainer, this.body);
    this.tabsContainer = tabsContainer;

    for(i = 0; i < this.panels.length; i += 1) {
        tabsContainer.appendChild(this.panels[i].getTab());
    }

    if(i <= 1) {
        tabsContainer.style.display = 'none';
    }

    this.windowHeader = windowHeader;
    this.titleLabelObject = titleLabel;
    if (this.body) {
        this.body.className = 'adam-window-body';
        this.body.style.height = (this.height - 22 - (i > 1 ? 22 : 0)) + 'px';
        //this.body.innerHTML = 'test';
    }
    return this.html;
};

/**
 * Shows the window
 */
Window.prototype.show = function () {
    var panel;
    if (!this.loaded) {
        this.load();
    }
    if (this.modal) {
        this.modalObject.show();
    }
    if (this.selectedTab) {
        this.selectedTab.select();
        panel = this.selectedTab.getContent();
        panel.setHeight($(this.body).innerHeight());
        this.body.appendChild(panel.getHTML());
        panel.load();
    }
    document.body.appendChild(this.html);

    //here do visible the window
    this.setVisible(true);
};

/**
 * Opens/Creates the windows object
 * @private
 */
Window.prototype.load = function () {
    if (!this.html) {
        this.createHTML();
        this.attachListeners();
        this.loaded = true;
    }
};


/**
 * Close the window and destroy the object
 */
Window.prototype.close = function () {
    if (this.visible) {
        this.hide();
    }
    if (this.dispose) {
        this.dispose();
    }
};

/**
 * Hides the window
 * @param {Boolean} [destroy]
 */
Window.prototype.hide = function (destroy) {
    if (this.modal) {
        this.modalObject.hide();
    }
    document.body.removeChild(this.html);
    this.setVisible(false);
    if (destroy || this.destroyOnHide) {
        this.close();
    }
};

/**
 * Sets the window listeners
 */
Window.prototype.attachListeners = function () {
    var self = this;
    $(this.html).draggable({
        cursor: "move",
        scroll: false,
        containment: "document"
    }).on('keydown keyup keypress', function(e) {
        e.stopPropagation();
    });
    if (this.closeButton && this.closeButtonObject) {
        $(this.closeButtonObject).click(function (e) {
            e.stopPropagation();
            self.hide();
        });
    }
};

//TabPanelElement
    var TabPanelElement = function(settings) {
        Base.call(this, settings);
        this.title = null;
        this.tab = null;
        this.content = null;
        this.parent = null;
        this.onClick = null;
        this.selected = null;
        TabPanelElement.prototype.initObject.call(this, settings);
    };

    TabPanelElement.prototype.initObject = function(settings) {
        var defaults = {
            title: null,
            content: null,
            parent: null,
            onClick: null
        };

        $.extend(true, defaults, settings );

        this.onClick = defaults.onClick;

        this.setTitle(defaults.title)
            .setContent(defaults.content)
            .setParent(defaults.parent);
    };

    TabPanelElement.prototype.setTitle = function(title) {
        this.title = title;
        return this;
    };

    TabPanelElement.prototype.getTitle = function() {
        return this.title;
    };

    TabPanelElement.prototype.setContent = function(content) {
        if(content instanceof Panel) {
            this.content = content;
        }

        return this;
    };

    TabPanelElement.prototype.getContent = function() {
        return this.content;
    };

    TabPanelElement.prototype.setParent = function(parent) {
        this.parent = parent;
        return this;
    };

    TabPanelElement.prototype.getParent = function() {
        return this.parent;
    };

    TabPanelElement.prototype.attachListeners = function() {
        var that = this;
        $(this.tab).on("click", "a", function(e) {
            e.preventDefault();
        }).on("click", function() {
            if(typeof that.onClick === 'function') {
                that.onClick.call(that);
            }
        }).on("keydown", function(e) {
            e.stopPropagation();
        });

        return this;
    };

    TabPanelElement.prototype.createTab = function() {
        var tab, link;

        if(this.tab) {
            return this.tab;
        }

        tab = document.createElement('li');
        tab.id = this.id;
        tab.className = 'adam-tab';
        link = document.createElement("a");
        link.href= '#';
        if(this.title) {
        link.appendChild(document.createTextNode(this.title));
        } else {
            link.innerHTML = "&nbsp;";
        }

        tab.appendChild(link);
        this.tab = tab;

        this.attachListeners();

        return this.tab;
    };

    TabPanelElement.prototype.getTab = function() {
        if(!this.tab) {
            this.createTab();
            if(this.selected) {
                this.select();    
            } else {
                this.unselect();
            }
            
        }
        return this.tab;
    };

    TabPanelElement.prototype.isSelected =function() {
        return this.selected;
    };

    TabPanelElement.prototype.select = function() {
        this.selected = true;
        if(this.tab) {
            $(this.tab).addClass("active");
        }
        return this;
    };

    TabPanelElement.prototype.unselect = function() {
        this.selected = false;
        if(this.tab) {
            $(this.tab).removeClass("active");
        }
        return this;
    };
