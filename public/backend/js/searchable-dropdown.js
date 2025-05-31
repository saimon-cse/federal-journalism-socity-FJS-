/**
 * Searchable Dropdown - A custom dropdown with search functionality
 */
class SearchableDropdown {
    constructor(element, options = {}) {
      // Store the original select element
      this.selectElement = element;

      // Set default options
      this.options = Object.assign({
        placeholder: 'Select an option',
        noResultsText: 'No results found',
        searchPlaceholder: 'Search...',
        multiSelect: false,
        maxItems: null,
        disabled: false,
        onChange: null
      }, options);

      // Disable the original select element
      this.selectElement.classList.add('searchable-select-native');

      // Create the dropdown elements
      this.createDropdownElements();

      // Populate options from select element
      this.populateOptions();

      // Add event listeners
      this.addEventListeners();

      // Set initial state
      this.updateDisplay();

      // Disable if needed
      if (this.options.disabled || this.selectElement.disabled) {
        this.disable();
      }
    }

    /**
     * Create the HTML structure for the dropdown
     */
    createDropdownElements() {
      // Create container
      this.container = document.createElement('div');
      this.container.className = 'searchable-dropdown';

      // Create display input
      this.displayInput = document.createElement('div');
      this.displayInput.className = 'searchable-dropdown-input';
      this.displayInput.textContent = this.options.placeholder;

      // Create dropdown arrow
      this.arrow = document.createElement('div');
      this.arrow.className = 'searchable-dropdown-arrow';
      this.arrow.innerHTML = '<i class="fas fa-chevron-down"></i>';

      // Create dropdown content
      this.dropdownContent = document.createElement('div');
      this.dropdownContent.className = 'searchable-dropdown-content';

      // Create search container
      this.searchContainer = document.createElement('div');
      this.searchContainer.className = 'searchable-dropdown-search';
      this.searchContainer.innerHTML = '<i class="fas fa-search"></i>';

      // Create search input
      this.searchInput = document.createElement('input');
      this.searchInput.type = 'text';
      this.searchInput.placeholder = this.options.searchPlaceholder;
      this.searchContainer.appendChild(this.searchInput);

      // Create options container
      this.optionsContainer = document.createElement('div');
      this.optionsContainer.className = 'searchable-dropdown-options';

      // Assemble the components
      this.dropdownContent.appendChild(this.searchContainer);
      this.dropdownContent.appendChild(this.optionsContainer);

      this.container.appendChild(this.displayInput);
      this.container.appendChild(this.arrow);
      this.container.appendChild(this.dropdownContent);

      // If multi-select, create selected items container
      if (this.options.multiSelect) {
        this.selectedItemsContainer = document.createElement('div');
        this.selectedItemsContainer.className = 'searchable-dropdown-selected-items';
        this.container.appendChild(this.selectedItemsContainer);
      }

      // Insert the dropdown after the select element
      this.selectElement.parentNode.insertBefore(this.container, this.selectElement.nextSibling);
    }

    /**
     * Populate dropdown options from the original select element
     */
    populateOptions() {
      this.options.data = [];
      const optgroups = this.selectElement.querySelectorAll('optgroup');

      if (optgroups.length > 0) {
        // Handle optgroups
        optgroups.forEach(optgroup => {
          const groupOptions = [];

          Array.from(optgroup.querySelectorAll('option')).forEach(option => {
            groupOptions.push({
              value: option.value,
              text: option.textContent,
              selected: option.selected,
              disabled: option.disabled
            });
          });

          if (groupOptions.length > 0) {
            this.options.data.push({
              label: optgroup.label,
              options: groupOptions
            });
          }
        });

        // Also add any direct child options
        const directOptions = Array.from(this.selectElement.querySelectorAll(':scope > option')).map(option => ({
          value: option.value,
          text: option.textContent,
          selected: option.selected,
          disabled: option.disabled
        }));

        if (directOptions.length > 0) {
          this.options.data.unshift({
            options: directOptions
          });
        }
      } else {
        // No optgroups, just options
        const options = Array.from(this.selectElement.querySelectorAll('option')).map(option => ({
          value: option.value,
          text: option.textContent,
          selected: option.selected,
          disabled: option.disabled
        }));

        this.options.data = [{
          options: options
        }];
      }

      this.renderOptions();
    }

    /**
     * Render dropdown options
     */
    renderOptions(searchTerm = '') {
      // Clear current options
      this.optionsContainer.innerHTML = '';

      let hasResults = false;

      // Loop through option groups
      this.options.data.forEach(group => {
        const filteredOptions = group.options.filter(option =>
          option.text.toLowerCase().includes(searchTerm.toLowerCase())
        );

        if (filteredOptions.length === 0) return;

        hasResults = true;

        // Create option group
        const optionGroup = document.createElement('div');
        optionGroup.className = 'searchable-dropdown-group';

        // Add group label if exists
        if (group.label) {
          const groupHeader = document.createElement('div');
          groupHeader.className = 'searchable-dropdown-group-header';
          groupHeader.textContent = group.label;
          optionGroup.appendChild(groupHeader);
        }

        // Add options to group
        filteredOptions.forEach(option => {
          const optionElement = document.createElement('div');
          optionElement.className = 'searchable-dropdown-item';
          optionElement.dataset.value = option.value;
          optionElement.textContent = option.text;

          if (option.selected) {
            optionElement.classList.add('selected');
          }

          if (option.disabled) {
            optionElement.classList.add('disabled');
          }

          optionElement.addEventListener('click', () => {
            if (option.disabled) return;
            this.selectOption(option.value);
          });

          optionGroup.appendChild(optionElement);
        });

        this.optionsContainer.appendChild(optionGroup);
      });

      // Show "no results" message if needed
      if (!hasResults) {
        const noResults = document.createElement('div');
        noResults.className = 'searchable-dropdown-empty';
        noResults.textContent = this.options.noResultsText;
        this.optionsContainer.appendChild(noResults);
      }
    }

    /**
     * Handle option selection
     */
    selectOption(value) {
      const isAlreadySelected = this.isOptionSelected(value);

      if (this.options.multiSelect) {
        // For multi-select
        if (isAlreadySelected) {
          // Deselect the option
          this.deselectOption(value);
        } else {
          // Check if we've reached the maximum items (if set)
          if (this.options.maxItems !== null) {
            const selectedCount = this.getSelectedValues().length;
            if (selectedCount >= this.options.maxItems) {
              return;
            }
          }

          // Select the option in the original select
          Array.from(this.selectElement.options).forEach(option => {
            if (option.value === value) {
              option.selected = true;
            }
          });

          // Add selected badge
          this.addSelectedBadge(value);
        }
      } else {
        // For single select
        // Deselect all options first
        Array.from(this.selectElement.options).forEach(option => {
          option.selected = option.value === value;
        });

        // Update display
        this.updateDisplay();

        // Close dropdown
        this.closeDropdown();
      }

      // Trigger change event
      this.triggerChangeEvent();
    }

    /**
     * Check if an option is selected
     */
    isOptionSelected(value) {
      return Array.from(this.selectElement.options).some(option =>
        option.value === value && option.selected
      );
    }

    /**
     * Deselect an option
     */
    deselectOption(value) {
      // Deselect in the original select
      Array.from(this.selectElement.options).forEach(option => {
        if (option.value === value) {
          option.selected = false;
        }
      });

      // Remove the badge
      if (this.options.multiSelect) {
        const badge = this.selectedItemsContainer.querySelector(`[data-value="${value}"]`);
        if (badge) {
          badge.remove();
        }
      }

      // Trigger change event
      this.triggerChangeEvent();
    }

    /**
     * Add a badge for selected option (multi-select)
     */
    addSelectedBadge(value) {
      // Find the option text
      let optionText = '';

      this.options.data.forEach(group => {
        group.options.forEach(option => {
          if (option.value === value) {
            optionText = option.text;
          }
        });
      });

      // Create and add the badge
      const badge = document.createElement('div');
      badge.className = 'searchable-dropdown-selected-badge';
      badge.dataset.value = value;
      badge.innerHTML = `
        <span>${optionText}</span>
        <span class="remove-badge"><i class="fas fa-times"></i></span>
      `;

      // Add click event to remove
      badge.querySelector('.remove-badge').addEventListener('click', (e) => {
        e.stopPropagation();
        this.deselectOption(value);
      });

      this.selectedItemsContainer.appendChild(badge);
    }

    /**
     * Get all selected values
     */
    getSelectedValues() {
      return Array.from(this.selectElement.selectedOptions).map(option => option.value);
    }

    /**
     * Update the display input based on selection
     */
    updateDisplay() {
      const selectedOptions = Array.from(this.selectElement.selectedOptions);

      if (selectedOptions.length === 0) {
        this.displayInput.textContent = this.options.placeholder;
        this.displayInput.classList.add('placeholder');
      } else if (this.options.multiSelect) {
        // For multi-select, we show badges instead
        this.displayInput.textContent = '';
        this.displayInput.classList.remove('placeholder');

        // Update badges
        this.selectedItemsContainer.innerHTML = '';
        selectedOptions.forEach(option => {
          this.addSelectedBadge(option.value);
        });
      } else {
        // For single select
        this.displayInput.textContent = selectedOptions[0].textContent;
        this.displayInput.classList.remove('placeholder');
      }

      // Update selected items in dropdown
      const dropdownItems = this.optionsContainer.querySelectorAll('.searchable-dropdown-item');
      dropdownItems.forEach(item => {
        const isSelected = this.isOptionSelected(item.dataset.value);
        if (isSelected) {
          item.classList.add('selected');
        } else {
          item.classList.remove('selected');
        }
      });
    }

    /**
     * Add all necessary event listeners
     */
    addEventListeners() {
      // Toggle dropdown on click
      this.displayInput.addEventListener('click', () => {
        if (this.container.classList.contains('disabled')) return;
        this.toggleDropdown();
      });

      // Search functionality
      this.searchInput.addEventListener('input', () => {
        this.renderOptions(this.searchInput.value);
      });

      // Close when clicking outside
      document.addEventListener('click', (e) => {
        if (!this.container.contains(e.target)) {
          this.closeDropdown();
        }
      });

      // Handle keyboard navigation
      this.container.addEventListener('keydown', (e) => {
        if (this.container.classList.contains('disabled')) return;

        switch (e.key) {
          case 'Enter':
            if (this.container.classList.contains('open')) {
              // Find first visible, non-disabled, selected option
              const option = this.optionsContainer.querySelector('.searchable-dropdown-item.selected:not(.disabled)');
              if (option) {
                this.selectOption(option.dataset.value);
              }
            } else {
              this.openDropdown();
            }
            e.preventDefault();
            break;

          case 'Escape':
            this.closeDropdown();
            e.preventDefault();
            break;

          case 'ArrowDown':
            if (this.container.classList.contains('open')) {
              this.navigateOptions('down');
            } else {
              this.openDropdown();
            }
            e.preventDefault();
            break;

          case 'ArrowUp':
            if (this.container.classList.contains('open')) {
              this.navigateOptions('up');
            }
            e.preventDefault();
            break;
        }
      });

      // Watch for changes to the original select
      const observer = new MutationObserver(() => {
        this.populateOptions();
        this.updateDisplay();
      });

      observer.observe(this.selectElement, { attributes: true, childList: true, subtree: true });
    }

    /**
     * Navigate options using keyboard
     */
    navigateOptions(direction) {
      const options = Array.from(this.optionsContainer.querySelectorAll('.searchable-dropdown-item:not(.disabled)'));
      if (options.length === 0) return;

      // Find currently focused option
      const focusedOption = this.optionsContainer.querySelector('.searchable-dropdown-item.focused');
      let index = focusedOption ? options.indexOf(focusedOption) : -1;

      // Remove focus from current option
      if (focusedOption) {
        focusedOption.classList.remove('focused');
      }

      // Determine next index
      if (direction === 'down') {
        index = index + 1 >= options.length ? 0 : index + 1;
      } else {
        index = index - 1 < 0 ? options.length - 1 : index - 1;
      }

      // Focus new option
      const nextOption = options[index];
      nextOption.classList.add('focused');
      nextOption.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /**
     * Open the dropdown
     */
    openDropdown() {
      this.container.classList.add('open');
      this.searchInput.focus();
      this.searchInput.value = '';
      this.renderOptions();
    }

    /**
     * Close the dropdown
     */
    closeDropdown() {
      this.container.classList.remove('open');
      this.searchInput.value = '';
    }

    /**
     * Toggle dropdown state
     */
    toggleDropdown() {
      if (this.container.classList.contains('open')) {
        this.closeDropdown();
      } else {
        this.openDropdown();
      }
    }

    /**
     * Disable the dropdown
     */
    disable() {
      this.container.classList.add('disabled');
    }

    /**
     * Enable the dropdown
     */
    enable() {
      this.container.classList.remove('disabled');
    }

    /**
     * Trigger change event
     */
    triggerChangeEvent() {
      // Update the original select element
      const event = new Event('change', { bubbles: true });
      this.selectElement.dispatchEvent(event);

      // Call custom onChange callback if provided
      if (typeof this.options.onChange === 'function') {
        this.options.onChange(this.getSelectedValues(), this.selectElement);
      }

      // Update display
      this.updateDisplay();
    }

    /**
     * Refresh the dropdown
     */
    refresh() {
      this.populateOptions();
      this.updateDisplay();
    }

    /**
     * Destroy the dropdown and restore original select
     */
    destroy() {
      this.selectElement.classList.remove('searchable-select-native');
      this.container.remove();
    }
  }

  // Initialize all searchable dropdowns on page load
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize on elements with the data attribute
    document.querySelectorAll('select[data-searchable="true"]').forEach(select => {
      new SearchableDropdown(select, {
        multiSelect: select.multiple,
        placeholder: select.getAttribute('data-placeholder') || 'Select an option',
        noResultsText: select.getAttribute('data-no-results-text') || 'No results found',
        searchPlaceholder: select.getAttribute('data-search-placeholder') || 'Search...',
        maxItems: select.getAttribute('data-max-items') ? parseInt(select.getAttribute('data-max-items')) : null
      });
    });
  });

  // Export for module use
  if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
    module.exports = SearchableDropdown;
  }
