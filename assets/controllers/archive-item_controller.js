import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static values = {
    url: String,
    confirmMessage: { type: String, default: 'Are you sure you want to delete this item?' },
    successMessage: { type: String, default: 'Item deleted successfully' },
    errorMessage: { type: String, default: 'Failed to delete item' },
    parentSelector: { type: String, default: 'tr' }
  }

  async archive(event) {
    if (!confirm(this.confirmMessageValue)) {
      return;
    }

    try {
      const response = await fetch(this.urlValue, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      if (!response.ok) {
        throw new Error('Network response was not ok');
      }

      // Remove the element
      const parentElement = this.element.closest(this.parentSelectorValue);
      parentElement.remove();

      // Dispatch success event that can be caught by a notification controller
      const event = new CustomEvent('notification:show', {
        bubbles: true,
        detail: {
          message: this.successMessageValue,
          type: 'success'
        }
      });
      this.element.dispatchEvent(event);

    } catch (error) {
      console.error('Error:', error);
      // Dispatch error event
      const event = new CustomEvent('notification:show', {
        bubbles: true,
        detail: {
          message: this.errorMessageValue,
          type: 'error'
        }
      });
      this.element.dispatchEvent(event);
    }
  }
}
