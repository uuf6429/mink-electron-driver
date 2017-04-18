(function () {
    var remote = require('electron').remote,
        setExecutionError = remote.getGlobal('setExecutionError'),
        setWindowUnloading = remote.getGlobal('setWindowUnloading'),
        setWindowIdName = remote.getGlobal('setWindowIdName'),
        setFileFromScript = remote.getGlobal('setFileFromScript'),
        DELAY_SCRIPT_RESPONSE = remote.getGlobal('DELAY_SCRIPT_RESPONSE'),
        electronWindow = remote.getCurrentWindow();

    window.onerror = function (error) {
        setExecutionError(error);
        return true;
    };

    var oldOnUnload = window.onbeforeunload;
    window.onbeforeunload = function (error) {
        setWindowUnloading(true);
        if (oldOnUnload) oldOnUnload();
        setWindowIdName(electronWindow.id, null, location.href);
    };

    var oldWndName = window.name || remote.getGlobal('newWindowName');
    window.__defineSetter__("name", function (name) {
        oldWndName = name;
        setWindowIdName(electronWindow.id, name, location.href);
    });
    window.__defineGetter__("name", function () {
        return oldWndName;
    });
    setWindowIdName(electronWindow.id, oldWndName, location.href);

    window.Electron = {
        'syn': require('syn'),

        'setFileFromScript': function (xpath, value) {
            setFileFromScript(electronWindow.id, xpath, value);
            return DELAY_SCRIPT_RESPONSE;
        },

        // Thanks to Jason Farrell from Use All Five
        'isVisible': function isVisible(el, t, r, b, l, w, h) {
            var p = el.parentNode,
                VISIBLE_PADDING = 2;

            if (!this._elementInDocument(el)) {
                return false;
            }

            if (9 === p.nodeType) {
                return true;
            }

            if (
                '0' === this._getStyle(el, 'opacity') ||
                'none' === this._getStyle(el, 'display') ||
                'hidden' === this._getStyle(el, 'visibility')
            ) {
                return false;
            }

            if (
                'undefined' === typeof(t) ||
                'undefined' === typeof(r) ||
                'undefined' === typeof(b) ||
                'undefined' === typeof(l) ||
                'undefined' === typeof(w) ||
                'undefined' === typeof(h)
            ) {
                t = el.offsetTop;
                l = el.offsetLeft;
                b = t + el.offsetHeight;
                r = l + el.offsetWidth;
                w = el.offsetWidth;
                h = el.offsetHeight;
            }
            if (p) {
                if (('hidden' === this._getStyle(p, 'overflow') || 'scroll' === this._getStyle(p, 'overflow'))) {
                    if (
                        l + VISIBLE_PADDING > p.offsetWidth + p.scrollLeft ||
                        l + w - VISIBLE_PADDING < p.scrollLeft ||
                        t + VISIBLE_PADDING > p.offsetHeight + p.scrollTop ||
                        t + h - VISIBLE_PADDING < p.scrollTop
                    ) {
                        return false;
                    }
                }
                if (el.offsetParent === p) {
                    l += p.offsetLeft;
                    t += p.offsetTop;
                }

                return this.isVisible(p, t, r, b, l, w, h);
            }
            return true;
        },

        '_getStyle': function (el, property) {
            if (window.getComputedStyle) {
                return document.defaultView.getComputedStyle(el, null)[property];
            }
            if (el.currentStyle) {
                return el.currentStyle[property];
            }
        },

        /**
         * @param {Element|Node} element
         * @returns {boolean}
         * @private
         */
        '_elementInDocument': function (element) {
            while (element = element.parentNode) {
                if (element === document) {
                    return true;
                }
            }
            return false;
        },

        /**
         * @param {Element|HTMLInputElement|HTMLSelectElement} element
         * @returns {*}
         */
        'getValue': function (element) {
            var i;

            switch (true) {
                case element.tagName === 'SELECT' && element.multiple:
                    var selected = [];
                    for (i = 0; i < element.options.length; i++) {
                        if (element.options[i].selected) {
                            selected.push(element.options[i].value);
                        }
                    }
                    return selected;

                case element.tagName === 'INPUT' && element.type === 'checkbox':
                    return element.checked ? element.value : null;

                case element.tagName === 'INPUT' && element.type === 'radio':
                    var name = element.getAttribute('name');
                    if (name) {
                        var radioButtons = window.document.getElementsByName(name);
                        for (i = 0; i < radioButtons.length; i++) {
                            var radioButton = radioButtons.item(i);
                            if (radioButton.form === element.form && radioButton.checked) {
                                return radioButton.value;
                            }
                        }
                    }
                    return null;

                default:
                    return element.value;
            }
        },

        /**
         * @param {string} xpath
         * @param {HTMLInputElement} element
         * @param {*} value
         * @returns {*}
         * @todo See also: https://github.com/segmentio/nightmare/blob/5ee597175861023cd23ccc5421f4fe3e00e54159/lib/runner.js#L369
         */
        'setValue': function(xpath, element, value){
            switch (true) {
                case element.tagName === 'SELECT':
                    if (value && value.constructor.name === 'Array') {
                        this.deselectAllOptions(element);

                        for (var n = 0; n < value.length; n++) {
                            this.selectOptionOnElement(element, value[n], true);
                        }
                    } else {
                        this.selectOptionOnElement(element, value, false);
                    }
                    break;

                case element.tagName === 'INPUT' && element.type === 'checkbox':
                    if (element.checked === !value) element.click();
                    break;

                case element.tagName === 'INPUT' && element.type === 'radio':
                    this.selectRadioByValue(element, value);
                    break;

                case element.tagName === 'INPUT' && element.type === 'file':
                    this.setFileFromScript(xpath, value);
                    break;

                default:
                    // FIXME here we need to trigger actual key strokes, otherwise keyboard events fail
                    element.value = value;
                    this.syn.trigger(element, 'change', {});
                    break;
            }
        },

        /**
         * @param {HTMLInputElement|HTMLSelectElement} element
         */
        'deselectAllOptions': function(element) {
            if (!element || element.tagName !== 'SELECT')
                throw new Error('Element is not a valid select element.');

            for (var i = 0; i < element.options.length; i++) {
                element.options[i].selected = false;
            }
        },

        /**
         * @param {HTMLInputElement} element
         * @returns {boolean}
         */
        'isSelected': function(element){
            if (!element || element.tagName !== 'OPTION')
                throw new Error('Element is not a valid option element.');

            var select;
            if (element.parentNode.tagName === 'SELECT') { // select -> option
                select = element.parentNode;
            } else if(element.parentNode.parentNode.tagName === 'SELECT') { // select -> optgroup -> option
                select = element.parentNode.parentNode;
            } else {
                throw new Error('Could not find a containing select element.');
            }

            return select.value === element.value;
        },

        /**
         * @param {HTMLInputElement} element
         * @returns {boolean}
         */
        'isChecked': function(element){
            if (!element || !((element.type === 'checkbox') || (element.type === 'radio')))
                throw new Error('Element is not a valid checkbox or radio button.');

            return element.checked;
        },

        /**
         * @param {HTMLInputElement} element
         * @param {boolean} checked
         * @returns {boolean}
         */
        'setChecked': function(element, checked){
            if (!element || !((element.type === 'checkbox') || (element.type === 'radio')))
                throw new Error('Element is not a valid checkbox or radio button.');

            if (element.checked !== checked) element.click();
        },

        /**
         * @param {HTMLInputElement|HTMLSelectElement} element
         * @param {*} value
         * @param {boolean} multiple
         */
        'selectOptionOnElement': function(element, value, multiple){
            var option = null;

            for (var i = 0; i < element.options.length; i++) {
                if (element.options[i].value === value) {
                    option = element.options[i];
                    break;
                }
            }

            if (!option) {
                throw new Error('Select box "' + (element.name || element.id) + '" does not have an option "' + value + '".');
            }

            if (multiple || !element.multiple){
                if (!option.selected) {
                    option.selected = true; // FIXME Should have been "option.click();" but it doesn't work... are we losing events now?
                }
            } else {
                this.deselectAllOptions(element);
                option.selected = true; // FIXME Should have been "option.click();" but it doesn't work... are we losing events now?
            }

            this.syn.trigger(element, 'change', {});
        },

        /**
         * @param {HTMLInputElement} element
         * @param {*} value
         * @param {boolean} multiple
         */
        'selectOption': function(element, value, multiple){
            if (element.tagName === 'INPUT' && element.type === 'radio') {
                this.selectRadioByValue(element, value);
                return;
            }

            if (element.tagName === 'SELECT') {
                this.selectOptionOnElement(element, value, multiple);
                return;
            }

            throw new Error('Element is not a valid select or radio input');
        },

        /**
         * @param {HTMLInputElement} element
         * @param {*} value
         */
        'selectRadioByValue': function(element, value){
            var name = element.name,
                form = element.form,
                input = null;

            if (element.value === value) {
                element.click();
                return;
            }

            if (!name) {
                throw new Error('The radio button does not have the value "' + value + '".');
            }

            if (form) {
                var group = form[name];
                for (var i = 0; i < group.length; i++) {
                    if (group[i].value === value) {
                        input = group[i];
                    }
                }
            } else {
                throw new Error('The radio group "' + name + '" is not in a form.');
            }

            if (!input) {
                throw new Error('The radio group "' + name + '" does not have an option "' + value + '".');
            }

            input.click();
        },

        'getElementByXPath': function(xpath){
            return document.evaluate(xpath, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
        }
    }
})();