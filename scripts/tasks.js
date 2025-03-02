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
            <span class="text-blue-400">Adding...</span>
        </div>
    `;
  dropZone.appendChild(tempCard);

  $.ajax({
    url: "tasks.php",
    type: "POST",
    data: {
      action: "add",
      title: title,
      description: description,
      column_id: columnId,
      user_id: userId,
    },
  })
    .done(function (response) {
      if (response.includes("success")) {
        var data = JSON.parse(response);
        // Find the assigned user's name
        const assignedUser = users.find((user) => user.id == userId);

        // Replace temporary card with actual card
        const newCard = document.createElement("div");
        newCard.className =
          "card bg-white border rounded-lg p-3 mb-2 shadow cursor-move";
        newCard.draggable = true;
        newCard.dataset.cardId = data.card_id;

        newCard.innerHTML = `
                  <div class="flex justify-between">
                      <p class="items-start mr-2">${escapeHtml(title)}</p>
                      <p class="items-end whitespace-nowrap">
                      <button onclick="editTask(${data.card_id})"
                              class="text-gray-400 hover:text-gray-600"><i class="fa fa-edit fa-xs"></i></button>
                      <button onclick="deleteTask(${data.card_id})"
                              class="text-gray-400 hover:text-gray-600"><i class="fa fa-times fa-xs"></i></button>
                      </p>
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
        showToast("Task added successfully");
      } else {
        dropZone.removeChild(tempCard);
        showToast("Failed to add task. Please try again.", "error");
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Error:", errorThrown);
      dropZone.removeChild(tempCard);
      showToast("Failed to add task. Please try again.", "error");
    });
  form.reset();
}

function deleteTask(cardId) {
  if (!confirm("Are you sure you want to delete this task?")) return;

  const card = document.querySelector(`[data-card-id="${cardId}"]`);
  // Add loading state
  card.classList.add("opacity-50");

  $.ajax({
    url: "tasks.php",
    type: "POST",
    data: {
      action: "delete",
      card_id: cardId,
    },
  })
    .done(function (response) {
      if (response.includes("success")) {
        card.remove();
        showToast("Task deleted successfully");
        closeSidebar();
      } else {
        card.classList.remove("opacity-50");
        showToast("Failed to delete task. Please try again", "error");
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Error:", errorThrown);
      card.classList.remove("opacity-50");
      showToast("Failed to delete task. Please try again.", "error");
    });
}

function editTask(cardId) {
  $.ajax({
    url: "tasks.php",
    type: "POST",
    data: {
      action: "get",
      card_id: cardId,
    },
  })
    .done(function (response) {
      if (response.includes("success")) {
        var data = JSON.parse(response);
        const card = data.card;
        const tags =
          data.tags && data.tags.length > 0 ? data.tags.join(", ") : "";
        console.log(data);
        $("#editCardId").val(card.id);
        $("#editTitle").val(card.title);
        $("#editDescription").val(card.description);
        $("#editUserId").val(card.user_id);
        $("#editTags").val(tags);
        $("#editDueDate").val(card.due_date);

        openSidebar();
      } else {
        console.log("Error Occurred");
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Error:", errorThrown);
      showToast("Failed to fetch task details. Please try again.", "error");
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

  $.ajax({
    url: "tasks.php",
    type: "POST",
    data: {
      action: "update",
      card_id: cardId,
      title: title,
      description: description,
      user_id: userId,
      tags: tags,
      due_date: dueDate,
    },
  })
    .done(function (response) {
      console.log(response);
      if (response.includes("success")) {
        var data = JSON.parse(response);
        console.log(data);
        closeSidebar();
        showToast("Task updated successfully", "success");
      } else {
        showToast("Failed to save task. Please try again.", "error");
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Error:", errorThrown);
      showToast("Failed to save task. Please try again.", "error");
    });
}
