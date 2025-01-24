// assets/js/modules/modal-manager.js
export function showDeclineModal() {
  return new Promise((resolve) => {
    // Create modal container
    const modal = document.createElement("div");
    modal.className = "future-events-modal";
    modal.innerHTML = `
            <div class="modal-backdrop fixed inset-0 bg-black bg-opacity-50"></div>
            <div class="modal-content fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white p-6 rounded-lg w-[95%] max-w-md">
                <h2 class="text-xl font-bold mb-4">Reason for Declining</h2>
                <textarea
                    id="decline-reason-input"
                    class="w-full p-2 border rounded mb-4 h-32 resize-none"
                    placeholder="Please specify your reason..."
                    required
                ></textarea>
                <div class="flex justify-end gap-2">
                    <button
                        id="modal-cancel"
                        class="px-4 py-2 border rounded hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        id="modal-confirm"
                        class="px-4 py-2 rounded bg-red-500 text-white hover:bg-red-600 disabled:bg-gray-300 disabled:cursor-not-allowed"
                        disabled
                    >
                        Really Decline
                    </button>
                </div>
            </div>
        `;

    // Add to DOM
    document.body.appendChild(modal);
    document.body.classList.add("overflow-hidden");

    // Get elements
    const textarea = modal.querySelector("#decline-reason-input");
    const confirmBtn = modal.querySelector("#modal-confirm");
    const cancelBtn = modal.querySelector("#modal-cancel");
    const backdrop = modal.querySelector(".modal-backdrop");

    // Handle input validation
    const handleInput = () => {
      confirmBtn.disabled = !textarea.value.trim();
    };

    // Handle confirmation
    const handleConfirm = () => {
      cleanup();
      resolve(textarea.value.trim());
    };

    // Handle cancellation
    const handleCancel = () => {
      cleanup();
      resolve(null);
    };

    // Cleanup function
    const cleanup = () => {
      document.body.removeChild(modal);
      document.body.classList.remove("overflow-hidden");
      textarea.removeEventListener("input", handleInput);
      confirmBtn.removeEventListener("click", handleConfirm);
      cancelBtn.removeEventListener("click", handleCancel);
      backdrop.removeEventListener("click", handleCancel);
      document.removeEventListener("keydown", handleKeydown);
    };

    // Keyboard handling
    const handleKeydown = (e) => {
      if (e.key === "Enter" && !confirmBtn.disabled) {
        handleConfirm();
      }
      if (e.key === "Escape") {
        handleCancel();
      }
    };

    // Add event listeners
    textarea.addEventListener("input", handleInput);
    confirmBtn.addEventListener("click", handleConfirm);
    cancelBtn.addEventListener("click", handleCancel);
    backdrop.addEventListener("click", handleCancel);
    document.addEventListener("keydown", handleKeydown);

    // Auto-focus textarea
    setTimeout(() => textarea.focus(), 50);
  });
}
