import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['container']

  connect() {
    document.addEventListener('notification:show', this.show.bind(this));
  }

  show(event) {
    const { message, type } = event.detail;

    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;

    this.containerTarget.appendChild(notification);

    // Remove notification after 3 seconds
    setTimeout(() => {
      notification.remove();
    }, 3000);
  }
}
