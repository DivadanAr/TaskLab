<div class="modal-overlay" id="todoModal">
    <div class="modal modal-lg">
        <div class="modal-header">
            <div>
                <h2>New Task</h2>
                <p>Add a new task to your board.</p>
            </div>
            <button class="modal-close" onclick="closeTodoModal()">
                <i class="fa-solid fa-x" style="font-size: 15px;"></i>
            </button>
        </div>
        <form action="/actions/task/task_post.php" method="POST">
            <input type="hidden" id="board_id" name="board_id">
            <div class=" modal-body">
                <div class="form-group">
                    <label>Task Name</label>
                    <input type="text" name="task_name" placeholder="Contoh: Website Redesign" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Due Date</label>
                        <input type="date" name="due_date">
                    </div>
                    <div class="form-group">
                        <label>Priority</label>
                        <select name="priority">
                            <option value="">No Priority</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeTodoModal()">
                    Cancel
                </button>
                <button type="submit" class="btn-save">
                    Create Task
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openTodoModal(board_id) {

        document
            .getElementById("board_id").value = board_id;
        document
            .getElementById("todoModal")
            .classList.add("show");

    }

    function closeTodoModal() {

        document
            .getElementById("todoModal")
            .classList.remove("show");

    }

    document
        .getElementById("todoModal")
        .addEventListener("click", function(e) {

            if (e.target === this) {

                closeTodoModal();

            }

        });
</script>