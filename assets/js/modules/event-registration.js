import { showDeclineModal } from "./modal-manager";

document.addEventListener("DOMContentLoaded", () => {
  document
    .querySelectorAll(".accept-event, .decline-event")
    .forEach((button) => {
      button.addEventListener("click", async (e) => {
        e.preventDefault();
        const eventId = button.dataset.eventId;
        const isDecline = button.classList.contains("decline-event");
        let customReason = "";

        // Handle decline modal flow
        if (isDecline) {
          try {
            customReason = await showDeclineModal();
            if (!customReason) return; // User cancelled
          } catch (error) {
            console.error("Modal error:", error);
            return;
          }
        }

        const action = isDecline ? "decline" : "accept";

        console.log(`Processing ${action} for event:`, eventId);

        try {
          const response = await fetch(ajax_object.ajax_url, {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
              action: "handle_event_registration",
              event_id: eventId,
              status: action,
              custom_reason: customReason, // Added custom reason
              security: ajax_object.nonce,
            }),
          });

          const data = await response.json();
          console.log("AJAX Response:", data);

          if (data.success) {
            // Update UI
            button.disabled = true;
            button.textContent =
              action === "accept"
                ? "Accepted"
                : "Declined (Reason: " + customReason.substring(0, 15) + "...)";

            // Remove any existing status messages
            const existingStatus = button
              .closest(".event-item")
              .querySelector(".status-message");
            if (existingStatus) existingStatus.remove();

            // Add new status message
            const statusElement = document.createElement("div");
            statusElement.className = "status-message text-sm mt-1";
            statusElement.textContent = `Status updated: ${action}`;
            button.insertAdjacentElement("afterend", statusElement);
          }
        } catch (error) {
          console.error("AJAX Error:", error);
          alert("Error processing your request. Please try again.");
        }
      });
    });
});

// Initialize status messages on existing buttons
document
  .querySelectorAll("[disabled].accept-event, [disabled].decline-event")
  .forEach((button) => {
    if (
      button.textContent.includes("Accepted") ||
      button.textContent.includes("Declined")
    ) {
      button.disabled = true;
    }
  });
