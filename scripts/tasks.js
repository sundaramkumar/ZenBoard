function addTask(event, columnId) {
  event.preventDefault();
  const form = event.target;
  const title = form.title.value;
  const userId = form.user_id.value;
  const description = form.description.value;
  const dropZone = document.querySelector(`[data-column-id="${columnId}"]`);

  if (!title.trim()) return;

  // Create card element with loading state
  const tempCard = document.createElement("div");
  tempCard.className =
    "card bg-white border rounded-lg p-3 mb-2 shadow cursor-move opacity-50";
  tempCard.innerHTML = `
      <div class="flex justify-between items-start">
          <p>${escapeHtml(title)}</p>
          <span class="text-gray-400">Adding...</span>
      </div>
  `;
  dropZone.appendChild(tempCard);

  fetch("./tasks/add_task.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `title=${encodeURIComponent(
      title
    )}&description=${description}&column_id=${columnId}&user_id=${userId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Find the assigned user's name
        const assignedUser = users.find((user) => user.id == userId);

        // Replace temporary card with actual card
        const newCard = document.createElement("div");
        newCard.className =
          "card bg-white border rounded-lg p-3 mb-2 shadow cursor-move";
        newCard.draggable = true;
        newCard.dataset.cardId = data.card_id;
        newCard.innerHTML = `
              <div class="flex justify-between items-start">
                  <p>${escapeHtml(title)}</p>
                  <button onclick="deleteTask(${data.card_id})"
                          class="text-gray-400 hover:text-gray-600">x</button>
              </div>
              <div class="text-sm text-gray-500 mt-2">
               <p>${escapeHtml(description)}</p>
                  Assigned To: ${escapeHtml(
                    assignedUser ? assignedUser.username : "Unassigned"
                  )}
              </div>
          `;
        newCard.addEventListener("dragstart", handleDragStart);
        newCard.addEventListener("dragend", handleDragEnd);
        dropZone.replaceChild(newCard, tempCard);
      } else {
        dropZone.removeChild(tempCard);
        alert("Failed to add task. Please try again.");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      dropZone.removeChild(tempCard);
      alert("Failed to add task. Please try again.");
    });

  form.reset();
}
function editTask(cardId) {
  fetch("./tasks/get_task.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `card_id=${cardId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const card = data.card;
        document.getElementById("editCardId").value = card.id;
        document.getElementById("editTitle").value = card.title;
        document.getElementById("editDescription").value = card.description;
        document.getElementById("editUserId").value = card.user_id;
        document.getElementById("editTags").value = data.tags.join(", ");
        document.getElementById("editDueDate").value = card.due_date;

        openSidebar();
      } else {
        alert("Failed to fetch task details. Please try again.");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Failed to fetch task details. Please try again.");
    });
}

function saveTask(event) {
  event.preventDefault();
  const form = event.target;
  const cardId = form.card_id.value;
  const title = form.title.value;
  const description = form.description.value;
  const userId = form.user_id.value;
  const tags = form.tags.value;
  const dueDate = form.due_date.value;

  fetch("./tasks/update_task.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `card_id=${cardId}&title=${encodeURIComponent(
      title
    )}&description=${encodeURIComponent(
      description
    )}&user_id=${userId}&tags=${encodeURIComponent(tags)}&due_date=${dueDate}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const card = document.querySelector(`[data-card-id="${cardId}"]`);
        card.querySelector("p").innerText = title;
        card.querySelector(".text-sm").innerText = description;
        card.dataset.userId = userId;

        // Update tags display
        const tagsContainer = card.querySelector(".tags");
        tagsContainer.innerHTML = "";
        tags.split(",").forEach((tag) => {
          const tagElement = document.createElement("span");
          tagElement.className = "tag";
          tagElement.innerText = tag.trim();
          tagsContainer.appendChild(tagElement);
        });

        // Update due date display
        const dueDateContainer = card.querySelector(".due-date");
        if (dueDateContainer) {
          dueDateContainer.innerText = `Due Date: ${dueDate}`;
        } else {
          const newDueDateContainer = document.createElement("div");
          newDueDateContainer.className = "due-date text-sm text-gray-500 mt-2";
          newDueDateContainer.innerText = `Due Date: ${dueDate}`;
          card.appendChild(newDueDateContainer);
        }

        showToast("Task saved successfully!");
        closeSidebar();
      } else {
        alert("Failed to save task. Please try again.");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Failed to save task. Please try again.");
    });
}

function deleteTask(cardId) {
  if (!confirm("Are you sure you want to delete this task?")) return;

  const card = document.querySelector(`[data-card-id="${cardId}"]`);
  // Add loading state
  card.classList.add("opacity-50");

  fetch("./tasks/delete_task.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `card_id=${cardId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        card.remove();
      } else {
        card.classList.remove("opacity-50");
        alert("Failed to delete task. Please try again.");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      card.classList.remove("opacity-50");
      alert("Failed to delete task. Please try again.");
    });
}
