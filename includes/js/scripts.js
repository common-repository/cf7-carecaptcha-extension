(function(window) {
    function CareCaptchaInstance($obj, $id) {

        if (!$obj.hasAttribute("data-carecaptcha")) {
            return;
        }
        var _id = $id,
            _captcha = $obj,
            _chf,
            _checkBox,
            _maxSets = 5,
            _sets = [{
                id: 0,
                txt: script_vars._setText1
            }, {
                id: 1,
                txt: script_vars._setText2
            }, {
                id: 2,
                txt: script_vars._setText3
            }, {
                id: 3,
                txt: script_vars._setText4
            }, {
                id: 4,
                txt: script_vars._setText5
            }],
            _introText = script_vars._introText,
            _errorText = script_vars._errorText,
            _verifyText = script_vars._verifyText,
            _linkText = script_vars._linkText,
            _currentSet = 0,
            _sequence = [],
            _correct = [],
            _rNums = [],
            _htmlElements;

        function _getCleanClassName(element) {
            return element.className.replace(/[\n\t]/g, ' ');
        }

        function _hasClass(className, element) {
            return _getCleanClassName(element).indexOf(className) > -1;
        }

        function _addClass(className, element) {
            if (!_hasClass(className, element)) {
                element.className += ' ' + className;
            }
        }

        function _removeClass(className, element) {
            element.className = _getCleanClassName(element).replace(new RegExp('\\b' + className + '\\b'), '');
        }

        function _toggleClass(className, element) {
            if (_hasClass(className, element)) {
                _removeClass(className, element);
            } else {
                _addClass(className, element);
            }
        }

        function _shuffle(array) {
            let counter = array.length;
            while (counter > 0) {
                let index = Math.floor(Math.random() * counter);
                counter--;
                let temp = array[counter];
                array[counter] = array[index];
                array[index] = temp;
            }
            return array;
        }

        function _destroy() {
            _removeClass("show", _htmlElements.modal);
            setTimeout(function() {
                for (var i in _htmlElements) {
                    if (_htmlElements[i].length > 0) {
                        for (var foo = 0; foo < _htmlElements[i].length; foo++) {
                            _htmlElements[i][foo].parentNode.removeChild(_htmlElements[i][foo]);
                            _htmlElements[i][foo] = null;
                            delete _htmlElements[i][foo];

                        }
                        _htmlElements[i] = null;
                        delete _htmlElements[i];
                    } else {
                        _htmlElements[i].parentNode.removeChild(_htmlElements[i]);
                        _htmlElements[i] = null;
                        delete _htmlElements[i];
                    }
                }
                _htmlElements = null;
            }, 250);
        }

        function _closeOverlay() {
            _destroy();
        }

        function _createHMTLElement($parent, $type, $class) {
            var e = document.createElement($type);
            if ($class) {
                e.setAttribute("class", $class);
            }
            if ($parent) {
                $parent.appendChild(e);
            }
            return e;
        }

        function _toggleSelected($e) {
            _toggleClass("selected", $e.currentTarget);
            _verify();
        }

        function _verify() {
            var checked = 0;
            for (var foo = 0; foo < 9; foo++) {

                if (_hasClass("selected", _htmlElements.modalContentBodyImageListItems[foo])) {
                    checked += _correct[foo] ? 1 : -1000;
                }
            }
            if (checked === 6) {
                _htmlElements.modalContentBodyError.innerHTML = _sets[_currentSet].txt;
                return true;
            } else {
                _htmlElements.modalContentBodyError.innerHTML = "&nbsp;";
            }
            return false;
        }

        function _doVerify() {
            if (_verify()) {
                _closeOverlay();
                _captcha.onclick = null;
                _addClass("checked", _captcha);
                _chf.value = "true";
            } else {
                for (var foo = 0; foo < 9; foo++) {
                    _removeClass("selected", _htmlElements.modalContentBodyImageListItems[foo]);
                    _htmlElements.modalContentBodyImageListItems[foo].onclick = null;
                }
                _htmlElements.modalContentFooterReload.onclick = _htmlElements.modalContentFooterVerify.onclick = null;
                _htmlElements.modalContentBodyError.innerHTML = _errorText;
                setTimeout(_setImages, 1500);
            }
        }

        function _setImages() {
            _currentSet++;
            _currentSet %= _maxSets;
            if (_htmlElements.modalContentBodyError.innerHTML !== _errorText) {
                _htmlElements.modalContentBodyError.innerHTML = "&nbsp;";
            }
            _sequence = [1, 2, 3, 4, 5, 6, 7, 8, 9];
            _rNums = [10, 11, 12];
            _shuffle(_sequence);
            _shuffle(_rNums);
            _sequence.splice(_sequence.length - 3, 3);
            _sequence = _sequence.concat(_rNums);
            _shuffle(_sequence);

            _htmlElements.modalContentHeaderImg.style.backgroundPosition = "0% " + ((100 / (_maxSets - 1)) * _sets[_currentSet].id) + "%";
            for (var foo = 0; foo < 9; foo++) {
                _correct[foo] = _sequence[foo] < 10;
                _htmlElements.modalContentBodyImageListItems[foo].style.backgroundPosition = (_sequence[foo] * (100 / 12)) + "% " + ((100 / (_maxSets - 1)) * (_sequence[foo] > 9 ? ((Math.random() * 4) >> 0) : _sets[_currentSet].id)) + "%";
                _removeClass("selected", _htmlElements.modalContentBodyImageListItems[foo]);
                _htmlElements.modalContentBodyImageListItems[foo].onclick = _toggleSelected;
            }
            _htmlElements.modalContentFooterVerify.onclick = _doVerify;
            _htmlElements.modalContentFooterReload.onclick = _setImages;
        }

        function _openOverlay() {
            if (!_htmlElements) {
                _shuffle(_sets);

                _htmlElements = {};

                _htmlElements.modal = _createHMTLElement(document.body, "div", "cc-modal");
                setTimeout(function() {
                    _addClass("show", _htmlElements.modal);
                }, 100);
                _htmlElements.modalClose = _createHMTLElement(_htmlElements.modal, "div", "cc-modal-close");
                _htmlElements.modalClose.onclick = _closeOverlay;

                _htmlElements.modalContent = _createHMTLElement(_htmlElements.modal, "div", "cc-modal-content");

                _htmlElements.modalContentHeader = _createHMTLElement(_htmlElements.modalContent, "div", "cc-modal-header");

                _htmlElements.modalContentHeaderTitle = _createHMTLElement(_htmlElements.modalContentHeader, "div", "cc-modal-title");
                _htmlElements.modalContentHeaderTitle.innerHTML = _introText;

                _htmlElements.modalContentHeaderImg = _createHMTLElement(_htmlElements.modalContentHeader, "div", "cc-modal-image");

                _htmlElements.modalContentBody = _createHMTLElement(_htmlElements.modalContent, "div", "cc-modal-body");

                _htmlElements.modalContentBodyImageList = _createHMTLElement(_htmlElements.modalContentBody, "ul", "cc-modal-images");

                _htmlElements.modalContentBodyImageListItems = [];

                for (var foo = 0; foo < 9; foo++) {
                    _htmlElements.modalContentBodyImageListItems[foo] = _createHMTLElement(_htmlElements.modalContentBodyImageList, "li", "cc-modal-image");
                    _htmlElements.modalContentBodyImageListItems[foo].setAttribute("data-img", foo);
                    _htmlElements.modalContentBodyImageListItems[foo].onclick = _toggleSelected;
                }
                _htmlElements.modalContentBodyError = _createHMTLElement(_htmlElements.modalContentBody, "span", "cc-modal-message");
                _htmlElements.modalContentBodyError.innerHTML = "&nbsp;";

                _htmlElements.modalContentFooter = _createHMTLElement(_htmlElements.modalContent, "div", "cc-modal-footer");

                _htmlElements.modalContentFooterColLeft = _createHMTLElement(_htmlElements.modalContentFooter, "div", "cc-modal-footer-col");
                _htmlElements.modalContentFooterColMiddle = _createHMTLElement(_htmlElements.modalContentFooter, "div", "cc-modal-footer-col");
                _htmlElements.modalContentFooterColRight = _createHMTLElement(_htmlElements.modalContentFooter, "div", "cc-modal-footer-col");

                _htmlElements.modalContentFooterReload = _createHMTLElement(_htmlElements.modalContentFooterColLeft, "div", "cc-modal-footer-reload");
                _htmlElements.modalContentFooterReload.innerHTML = "↺";

                _htmlElements.modalContentFooterLogo = _createHMTLElement(_htmlElements.modalContentFooterColMiddle, "div", "cc-modal-footer-logo");
                _htmlElements.modalContentFooterLogo.innerHTML = "<a href='https://www.noah.de/' target='_blank'></a>";

                _htmlElements.modalContentFooterLink = _createHMTLElement(_htmlElements.modalContentFooterColMiddle, "div", "cc-modal-footer-link");
                _htmlElements.modalContentFooterLink.innerHTML = "<a href='https://wordpress.org/plugins/cf7-carecaptcha-extension/' target='_blank'>"+ _linkText +"</a>";

                _htmlElements.modalContentFooterVerify = _createHMTLElement(_htmlElements.modalContentFooterColRight, "div", "cc-modal-footer-verify");
                _htmlElements.modalContentFooterVerify.innerHTML = _verifyText;

                _setImages();
            }
        }

        function _init() {
            _captcha.onclick = _openOverlay;

            _chf = _captcha.querySelector(".cc-toggle-input");
            _checkBox = _captcha.querySelector(".cc-toggle-checkbox");
        }
        _init();

    }

    if (!window.CareCaptchaInstance) {
        window.CareCaptchaInstance = CareCaptchaInstance;
    }
}(window));

(function(window) {
    function CareCaptcha() {
        function _init() {
            var careCaptchas = document.querySelectorAll('span[data-carecaptcha]');
            for (var foo = 0; foo < careCaptchas.length; foo++) {
                new CareCaptchaInstance(careCaptchas[foo], 'xxxxxxxx'.replace(/[xy]/g, function(c) {
                    var r = Math.random() * 16 | 0,
                        v = c == 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                }));
            }
        }
        document.addEventListener("DOMContentLoaded", _init);
    }

    if (!window.CareCaptcha) {
        window.CareCaptcha = new CareCaptcha;
    }
}(window));