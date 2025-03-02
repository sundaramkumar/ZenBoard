let draggedCard = null;

function handleDragStart(event) {
  draggedCard = event.target;
  event.target.classList.add("dragging");
}

function handleDragEnd(event) {
  event.target.classList.remove("dragging");
  draggedCard = null;
  document.querySelectorAll(".column-drop-zone").forEach((zone) => {
    zone.classList.remove("drag-over");
  });
}

function handleDragOver(event) {
  event.preventDefault();
  event.currentTarget.classList.add("drag-over");
}

function handleDrop(event) {
  event.preventDefault();
  const targetColumn = event.currentTarget;
  const sourceColumn = draggedCard.parentElement;
  const columnId = targetColumn.dataset.columnId;
  const cardId = draggedCard.dataset.cardId;

  // Optimistically update UI
  targetColumn.appendChild(draggedCard);

  // Update card's column in the database
  $.ajax({
    url: "tasks.php",
    type: "POST",
    data: {
      action: "move",
      card_id: cardId,
      column_id: columnId,
    },
  })
    .done(function (data) {
      if (data.includes("success")) {
        showToast("Task moved Successfully");
      } else {
        console.log("Error Occurred");
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Error:", errorThrown);
      showToast("Failed to move task. Please try again.", "error");
    });

  targetColumn.classList.remove("drag-over");
}
