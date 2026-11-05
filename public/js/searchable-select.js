(function () {
    'use strict';

    var CHEVRON = '<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z" fill="currentColor"/></svg>';
    var SEARCH = '<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" fill="currentColor"/></svg>';
    var CHECK = '<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" fill="currentColor"/></svg>';

    var counter = 0;
    var openInstance = null;
    var instances = [];

    function normalize(value) {
        return String(value == null ? '' : value)
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    function SearchableSelect(select) {
        this.select = select;
        this.id = 'ss-' + (++counter);
        this.items = [];
        this.visible = [];
        this.activeIndex = -1;
        this.isOpen = false;
        this.placeholder = '';
        this.init();
        instances.push(this);
    }

    SearchableSelect.prototype.init = function () {
        var self = this;
        var select = this.select;
        var options = Array.prototype.slice.call(select.options);
        var placeholderOption = null;

        options.forEach(function (option) {
            if (placeholderOption === null && option.value === '') {
                placeholderOption = option;
                return;
            }

            self.items.push({
                value: option.value,
                label: option.text,
                search: normalize(option.text) + ' ' + normalize(option.value),
                element: null
            });
        });

        this.placeholder = placeholderOption
            ? placeholderOption.text
            : (select.getAttribute('data-placeholder') || 'Pilih...');

        select.classList.add('ss-native');
        select.setAttribute('tabindex', '-1');

        this.wrapper = document.createElement('div');
        this.wrapper.className = 'ss';

        this.control = document.createElement('button');
        this.control.type = 'button';
        this.control.className = 'ss-control';
        this.control.setAttribute('role', 'combobox');
        this.control.setAttribute('aria-haspopup', 'listbox');
        this.control.setAttribute('aria-expanded', 'false');
        this.control.setAttribute('aria-controls', this.id + '-list');

        this.valueEl = document.createElement('span');
        this.valueEl.className = 'ss-value';

        this.caretEl = document.createElement('span');
        this.caretEl.className = 'ss-caret';
        this.caretEl.innerHTML = CHEVRON;

        this.control.appendChild(this.valueEl);
        this.control.appendChild(this.caretEl);

        this.dropdown = document.createElement('div');
        this.dropdown.className = 'ss-dropdown';
        this.dropdown.hidden = true;

        this.searchWrap = document.createElement('div');
        this.searchWrap.className = 'ss-search';

        this.searchIcon = document.createElement('span');
        this.searchIcon.className = 'ss-search-icon';
        this.searchIcon.innerHTML = SEARCH;

        this.searchInput = document.createElement('input');
        this.searchInput.type = 'text';
        this.searchInput.className = 'ss-search-input';
        this.searchInput.autocomplete = 'off';
        this.searchInput.placeholder = select.getAttribute('data-search-placeholder') || 'Cari...';
        this.searchInput.setAttribute('aria-label', 'Cari pilihan');

        this.searchWrap.appendChild(this.searchIcon);
        this.searchWrap.appendChild(this.searchInput);

        this.list = document.createElement('ul');
        this.list.className = 'ss-list';
        this.list.id = this.id + '-list';
        this.list.setAttribute('role', 'listbox');

        this.empty = document.createElement('div');
        this.empty.className = 'ss-empty';
        this.empty.textContent = 'Tidak ada hasil';
        this.empty.hidden = true;

        this.dropdown.appendChild(this.searchWrap);
        this.dropdown.appendChild(this.list);
        this.dropdown.appendChild(this.empty);

        select.parentNode.insertBefore(this.wrapper, select);
        this.wrapper.appendChild(select);
        this.wrapper.appendChild(this.control);
        this.wrapper.appendChild(this.dropdown);

        this.control.addEventListener('click', function () {
            if (self.isOpen) {
                self.close();
            } else {
                self.open();
            }
        });

        this.control.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp' || event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                self.open();
            }
        });

        this.searchInput.addEventListener('input', function () {
            self.renderList(self.searchInput.value);
        });

        this.searchInput.addEventListener('keydown', function (event) {
            self.handleKeydown(event);
        });

        this.select.addEventListener('change', function () {
            self.syncValue();
        });

        this.select.addEventListener('invalid', function () {
            self.wrapper.classList.add('ss-invalid');
        });

        if (select.disabled) {
            this.control.disabled = true;
            this.wrapper.classList.add('is-disabled');
        }

        if (select.id) {
            document.querySelectorAll('label[for="' + select.id + '"]').forEach(function (label) {
                label.addEventListener('click', function (event) {
                    event.preventDefault();
                    self.control.focus();
                    self.open();
                });
            });
        }

        this.syncValue();
    };

    SearchableSelect.prototype.syncValue = function () {
        var current = null;

        for (var i = 0; i < this.items.length; i++) {
            if (this.items[i].value === this.select.value) {
                current = this.items[i];
                break;
            }
        }

        if (current) {
            this.valueEl.textContent = current.label;
            this.wrapper.classList.remove('is-placeholder');
            this.wrapper.classList.remove('ss-invalid');
        } else {
            this.valueEl.textContent = this.placeholder;
            this.wrapper.classList.add('is-placeholder');
        }
    };

    SearchableSelect.prototype.open = function () {
        if (this.isOpen || this.select.disabled) {
            return;
        }

        if (openInstance && openInstance !== this) {
            openInstance.close();
        }

        this.isOpen = true;
        openInstance = this;
        this.wrapper.classList.add('is-open');
        this.control.setAttribute('aria-expanded', 'true');
        this.dropdown.hidden = false;
        this.searchInput.value = '';
        this.renderList('');
        this.searchInput.focus();

        var selectedIndex = -1;

        for (var i = 0; i < this.visible.length; i++) {
            if (this.visible[i].value === this.select.value) {
                selectedIndex = i;
                break;
            }
        }

        if (selectedIndex > -1) {
            this.setActive(selectedIndex);
        }
    };

    SearchableSelect.prototype.close = function () {
        if (!this.isOpen) {
            return;
        }

        this.isOpen = false;

        if (openInstance === this) {
            openInstance = null;
        }

        this.wrapper.classList.remove('is-open');
        this.control.setAttribute('aria-expanded', 'false');
        this.searchInput.removeAttribute('aria-activedescendant');
        this.dropdown.hidden = true;
    };

    SearchableSelect.prototype.renderList = function (query) {
        var self = this;
        var needle = normalize(query);
        var selectedValue = this.select.value;

        this.list.innerHTML = '';
        this.visible = [];
        this.activeIndex = -1;

        this.items.forEach(function (item) {
            if (needle !== '' && item.search.indexOf(needle) === -1) {
                return;
            }

            var option = document.createElement('li');
            option.className = 'ss-option';
            option.id = self.id + '-opt-' + self.visible.length;
            option.setAttribute('role', 'option');
            option.setAttribute('aria-selected', item.value === selectedValue ? 'true' : 'false');
            option.dataset.value = item.value;

            if (item.value === selectedValue) {
                option.classList.add('is-selected');
            }

            var label = document.createElement('span');
            label.className = 'ss-option-label';
            label.textContent = item.label;
            option.appendChild(label);

            if (item.value === selectedValue) {
                var check = document.createElement('span');
                check.className = 'ss-check';
                check.innerHTML = CHECK;
                option.appendChild(check);
            }

            option.addEventListener('click', function () {
                self.selectItem(item);
            });

            option.addEventListener('mousemove', function () {
                var index = self.visible.indexOf(item);
                if (index !== -1 && index !== self.activeIndex) {
                    self.setActive(index);
                }
            });

            item.element = option;
            self.list.appendChild(option);
            self.visible.push(item);
        });

        this.list.hidden = this.visible.length === 0;
        this.empty.hidden = this.visible.length !== 0;

        if (this.visible.length > 0) {
            this.setActive(0);
        }
    };

    SearchableSelect.prototype.handleKeydown = function (event) {
        var total = this.visible.length;

        if (event.key === 'Escape') {
            event.preventDefault();
            this.close();
            this.control.focus();
            return;
        }

        if (event.key === 'Tab') {
            this.close();
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            this.setActive(total === 0 ? -1 : (this.activeIndex + 1) % total);
            return;
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault();
            this.setActive(total === 0 ? -1 : (this.activeIndex - 1 + total) % total);
            return;
        }

        if (event.key === 'Home') {
            event.preventDefault();
            this.setActive(total === 0 ? -1 : 0);
            return;
        }

        if (event.key === 'End') {
            event.preventDefault();
            this.setActive(total === 0 ? -1 : total - 1);
            return;
        }

        if (event.key === 'Enter') {
            event.preventDefault();

            if (this.activeIndex > -1 && this.visible[this.activeIndex]) {
                this.selectItem(this.visible[this.activeIndex]);
            }
        }
    };

    SearchableSelect.prototype.setActive = function (index) {
        if (index < 0 || index >= this.visible.length) {
            return;
        }

        this.activeIndex = index;

        for (var i = 0; i < this.visible.length; i++) {
            var item = this.visible[i];
            var isActive = i === index;

            if (item.element) {
                item.element.classList.toggle('is-active', isActive);
            }

            if (isActive) {
                this.searchInput.setAttribute('aria-activedescendant', item.element.id);
            }
        }

        var activeEl = this.visible[index].element;

        if (activeEl && activeEl.scrollIntoView) {
            activeEl.scrollIntoView({ block: 'nearest' });
        }
    };

    SearchableSelect.prototype.selectItem = function (item) {
        this.select.value = item.value;
        this.syncValue();
        this.select.dispatchEvent(new Event('change', { bubbles: true }));
        this.close();
        this.control.focus();
    };

    function initAll() {
        document.querySelectorAll('select[data-searchable-select]').forEach(function (select) {
            if (select.dataset.ssReady === '1') {
                return;
            }

            select.dataset.ssReady = '1';
            new SearchableSelect(select);
        });
    }

    document.addEventListener('click', function (event) {
        if (!openInstance) {
            return;
        }

        if (!openInstance.wrapper.contains(event.target)) {
            openInstance.close();
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    window.SearchableSelect = {
        init: initAll,
        instances: instances
    };
})();
