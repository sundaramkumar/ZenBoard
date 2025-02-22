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
  fetch("update_card.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `card_id=${cardId}&column_id=${columnId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        // If update fails, revert the UI change
        sourceColumn.appendChild(draggedCard);
        alert("Failed to move card. Please try again.");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      // Revert UI on error
      sourceColumn.appendChild(draggedCard);
      alert("Failed to move card. Please try again.");
    });

  targetColumn.classList.remove("drag-over");
}
