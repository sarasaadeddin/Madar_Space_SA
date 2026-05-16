document.addEventListener("DOMContentLoaded", function () {
   const API_BASE = "/Madar_Space_SA/php";

    const notesContainer = document.getElementById("notesContainer");
    const addNoteBtn = document.getElementById("addNoteBtn");
    const addNoteModal = document.getElementById("addNoteModal");
    const closeModalBtn = document.getElementById("closeModalBtn");
    const noteForm = document.getElementById("noteForm");
    const searchInput = document.getElementById("searchInput");
    const filterSelect = document.getElementById("filterSelect");
    const emptyState = document.getElementById("emptyState");
    const confirmModal = document.getElementById("confirmModal");
    const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

    let notes = [];
    let noteToDeleteId = null;

    loadNotes();

    addNoteBtn.addEventListener("click", openAddNoteModal);
    closeModalBtn.addEventListener("click", closeAddNoteModal);
    noteForm.addEventListener("submit", handleNoteSubmit);
    searchInput.addEventListener("input", filterNotes);
    filterSelect.addEventListener("change", filterNotes);
    cancelDeleteBtn.addEventListener("click", closeConfirmModal);
    confirmDeleteBtn.addEventListener("click", confirmDeleteNote);

    function loadNotes() {
        fetch(`${API_BASE}/get_journals.php`)
            .then(response => response.json())
            .then(data => {
                console.log("JOURNALS FROM DATABASE:", data);

                notes = data;
                renderNotes(notes);
                updateEmptyState(notes);
            })
            .catch(error => {
                console.error("Error loading journals:", error);
            });
    }

    function renderNotes(notesToRender = notes) {
        notesContainer.innerHTML = "";

        notesToRender.forEach((note) => {
            const noteElement = document.createElement("div");
            noteElement.className = "note-card fade-in";

            noteElement.innerHTML = `
                <div class="note-content">
                    <div class="note-header">
                        <h3 class="note-title">${note.title}</h3>

                        <div class="note-actions">
                            <button class="delete-btn" data-id="${note.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <p class="note-text">${note.content}</p>

                    <div class="note-footer">
                        <span class="note-tag ${getTagClass(note.tag)}">
                            ${getTagIcon(note.tag)} ${getTagName(note.tag)}
                        </span>

                        <span class="note-date">
                            ${formatDate(note.created_at)}
                        </span>
                    </div>
                </div>
            `;

            notesContainer.appendChild(noteElement);
        });

        document.querySelectorAll(".delete-btn").forEach((btn) => {
            btn.addEventListener("click", function () {
                noteToDeleteId = this.getAttribute("data-id");
                openConfirmModal();
            });
        });
    }

    function handleNoteSubmit(e) {
        e.preventDefault();

        const title = document.getElementById("journalTitle").value.trim();
        const content = document.getElementById("journalContent").value.trim();
        const tag = document.querySelector('input[name="noteTag"]:checked').value;

        if (title === "" || content === "") {
            alert("Please fill in all fields");
            return;
        }

        const formData = new FormData();
        formData.append("title", title);
        formData.append("content", content);
        formData.append("tag", tag);

        fetch(`${API_BASE}/add_journal.php`, {
            method: "POST",
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeAddNoteModal();
                    loadNotes();
                } else {
                    alert(data.message || "Failed to save journal");
                }
            })
            .catch(error => {
                console.error("Error saving journal:", error);
            });
    }

    function confirmDeleteNote() {
        if (noteToDeleteId === null) return;

        const formData = new FormData();
        formData.append("id", noteToDeleteId);

        fetch(`${API_BASE}/delete_journal.php`, {
            method: "POST",
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeConfirmModal();
                    loadNotes();
                } else {
                    alert(data.message || "Failed to delete journal");
                }
            })
            .catch(error => {
                console.error("Error deleting journal:", error);
            });
    }

    function filterNotes() {
        const searchTerm = searchInput.value.toLowerCase();
        const filterValue = filterSelect.value;

        let filteredNotes = notes;

        if (searchTerm) {
            filteredNotes = filteredNotes.filter(
                (note) =>
                    note.title.toLowerCase().includes(searchTerm) ||
                    note.content.toLowerCase().includes(searchTerm)
            );
        }

        if (filterValue !== "all") {
            filteredNotes = filteredNotes.filter(
                (note) => note.tag === filterValue
            );
        }

        renderNotes(filteredNotes);
        updateEmptyState(filteredNotes);
    }

    function updateEmptyState(notesToCheck = notes) {
        if (notesToCheck.length === 0) {
            emptyState.style.display = "block";
        } else {
            emptyState.style.display = "none";
        }
    }

    function openAddNoteModal() {
        addNoteModal.classList.add("active");
        document.body.style.overflow = "hidden";
    }

    function closeAddNoteModal() {
        addNoteModal.classList.remove("active");
        document.body.style.overflow = "auto";
        noteForm.reset();
    }

    function openConfirmModal() {
        confirmModal.classList.add("active");
        document.body.style.overflow = "hidden";
    }

    function closeConfirmModal() {
        confirmModal.classList.remove("active");
        document.body.style.overflow = "auto";
        noteToDeleteId = null;
    }

    function getTagClass(tag) {
        const classes = {
            work: "tag-work",
            personal: "tag-personal",
            ideas: "tag-ideas",
            reminders: "tag-reminders",
        };

        return classes[tag] || "";
    }

    function getTagIcon(tag) {
        const icons = {
            work: '<i class="fas fa-briefcase"></i>',
            personal: '<i class="fas fa-user"></i>',
            ideas: '<i class="fas fa-lightbulb"></i>',
            reminders: '<i class="fas fa-bell"></i>',
        };

        return icons[tag] || "";
    }

    function getTagName(tag) {
        const names = {
            work: "Work",
            personal: "Personal",
            ideas: "Ideas",
            reminders: "Reminders",
        };

        return names[tag] || tag;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);

        return date.toLocaleDateString("en-US", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    }
});