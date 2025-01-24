class EventStatusManager {
  constructor() {
    this.initEventListeners();
  }

  initEventListeners() {
    document.addEventListener('click', e => {
      const btn = e.target.closest('.accept-event, .decline-event');
      if (btn) this.handleStatusChange(btn);
    });
  }

  handleStatusChange(btn) {
    const eventId = btn.dataset.eventId;
    const action = btn.classList.contains('accept-event') ? 'accept' : 'decline';
    
    btn.disabled = true;
    
    fetch(futureEventsData.ajaxUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        action: 'future_events_handle_action',
        nonce: futureEventsData.nonce,
        event_id: eventId,
        event_action: action
      })
    })
    .then(response => response.json())
    .then(data => this.handleResponse(data, btn))
    .catch(error => this.handleError(error, btn));
  }

  handleResponse(response, btn) {
    if (response.success) {
      const listItem = btn.closest('li');
      listItem.querySelectorAll('.accept-event, .decline-event').forEach(e => e.remove());
      
      const statusElem = document.createElement('span');
      statusElem.className = `status-text ${response.data.statusClass}`;
      statusElem.textContent = response.data.statusText;
      
      listItem.querySelector('a').insertAdjacentElement('afterend', statusElem);
    } else {
      alert(`Error: ${response.data}`);
      btn.disabled = false;
    }
  }

  handleError(error, btn) {
    console.error('AJAX Error:', error);
    alert('Request failed. Please check your connection.');
    btn.disabled = false;
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => new EventStatusManager());