const TaskDetailModal = (() => {

    const API_BASE = "../actions/task";

    let state = {
        taskId: null,
        title: "",
        description: "",
        boardMembers: [],
        assignees: [],
        subtasks: [],
        comments: [],
        pickerOpenKey: null,
    };

    async function postJSON(path, body) {
        const res = await fetch(`${API_BASE}/${path}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(body),
        });
        return res.json();
    }

    function findMemberInBoard(id) {
        return state.boardMembers.find(m => String(m.id) === String(id));
    }

    function initials(name) {
        if (!name) return "?";
        return name.trim().split(/\s+/).slice(0, 2).map(w => w[0].toUpperCase()).join("");
    }

    function avatarColorClass(id) {
        const classes = ["a1", "a2", "a3"];
        return classes[Math.abs(String(id).split("").reduce((a, c) => a + c.charCodeAt(0), 0)) % classes.length];
    }

    function avatarHTML(member) {
        if (!member) {
            return `<div class="avatar-mini empty"><i class="fa-solid fa-user" style="font-size:9px;"></i></div>`;
        }
        if (member.avatar) {
            return `<img src="${member.avatar}" class="avatar-mini" style="object-fit:cover;" title="${escapeHTML(member.name)}" />`;
        }
        return `<div class="avatar-mini ${avatarColorClass(member.id)}" title="${escapeHTML(member.name)}">${initials(member.name)}</div>`;
    }

    function showSaveHint() {
        const hint = document.getElementById("taskDetailSaveHint");
        if (!hint) return;
        hint.classList.add("show");
        setTimeout(() => hint.classList.remove("show"), 1400);
    }

    async function open(taskId) {
        const modal = document.getElementById("taskDetailModal");
        if (!modal) return;

        const res = await fetch(`${API_BASE}/task_detail_get.php?id=${taskId}`);
        const data = await res.json();

        if (!data.success) {
            alert(data.message || "Gagal memuat tugas.");
            return;
        }

        state = {
            taskId: data.task.id,
            title: data.task.task_name || "",
            description: data.task.description || "",
            boardMembers: data.board_members || [],
            assignees: data.assignees || [],
            subtasks: data.subtasks || [],
            comments: data.comments || [],
            pickerOpenKey: null,
        };

        document.getElementById("taskDetailTitleInput").value = state.title;
        document.getElementById("taskDetailDescInput").value = state.description;

        renderAssignees();
        renderSubtasks();
        renderComments();

        modal.classList.add("show");
    }

    function close() {
        const modal = document.getElementById("taskDetailModal");
        if (modal) modal.classList.remove("show");
        closeAllPickers();
    }

    async function onTitleBlur(el) {
        const value = el.value.trim();
        if (value === state.title) return;
        state.title = value;
        const res = await postJSON("task_update.php", {
            id: state.taskId,
            task_name: state.title,
            description: state.description,
        });
        if (res.success) showSaveHint();
    }

    async function onDescBlur(el) {
        const value = el.value.trim();
        if (value === state.description) return;
        state.description = value;
        const res = await postJSON("task_update.php", {
            id: state.taskId,
            task_name: state.title,
            description: state.description,
        });
        if (res.success) showSaveHint();
    }

    function togglePicker(key) {
        if (state.pickerOpenKey === key) {
            closeAllPickers();
            return;
        }
        console.log('asd')
        closeAllPickers();
        state.pickerOpenKey = key;
        const el = document.getElementById(`assigneePicker-${key}`);
        if (el) {
            el.classList.add("open");
            renderPickerList(key);
        }
        document.addEventListener("click", onDocClickClosePicker, true);
    }

    function onDocClickClosePicker(e) {
        const openEl = document.querySelector(".assignee-picker.open");
        if (openEl && !openEl.contains(e.target) && !e.target.closest(".btn-add-assignee, .subtask-assignee")) {
            closeAllPickers();
        }
    }

    function closeAllPickers() {
        document.querySelectorAll(".assignee-picker.open").forEach(el => el.classList.remove("open"));
        state.pickerOpenKey = null;
        document.removeEventListener("click", onDocClickClosePicker, true);
    }

    function getSubtaskFromKey(key) {
        const id = parseInt(key.replace("subtask-", ""), 10);
        return state.subtasks.find(s => s.id === id);
    }

    function renderPickerList(key, filter = "") {
        const listEl = document.getElementById(`assigneePickerList-${key}`);
        if (!listEl) return;
        
        const currentSubtaskAssigneeId = key === "task" ? null : getSubtaskFromKey(key)?.assignee?.id;
        
        listEl.innerHTML = state.boardMembers
            .filter(m => m.name.toLowerCase().includes(filter.toLowerCase()))
            .map(m => {
                const selected = key === "task" ?
                    state.assignees.some(a => String(a.id) === String(m.id)) :
                    String(currentSubtaskAssigneeId) === String(m.id);
                return `
            <div class="assignee-picker-item ${selected ? "selected" : ""}"
                onclick="TaskDetailModal.pickMember('${key}', '${m.id}')">
                ${avatarHTML(m)}
                <span>${escapeHTML(m.name)}</span>
            </div>
        `;
            }).join("") || `<div class="assignee-picker-item" style="color:var(--text-lo);cursor:default;">Tidak ada anggota</div>`;
    }

    function filterPicker(key, value) {
        renderPickerList(key, value);
    }

    async function pickMember(key, memberId) {
        if (key === "task") {
            const res = await postJSON("task_assign_toggle.php", {
                task_id: state.taskId,
                user_id: memberId,
            });
            if (!res.success) return;
            if (res.assigned) {
                const member = findMemberInBoard(memberId);
                if (member) state.assignees.push(member);
            } else {
                state.assignees = state.assignees.filter(a => String(a.id) !== String(memberId));
            }
            renderAssignees();
            renderPickerList("task");
        } else {
            const sub = getSubtaskFromKey(key);
            if (!sub) return;

            const res = await postJSON("subtask_assign_toggle.php", {
                subtask_id: sub.id,
                user_id: memberId,
            });
            if (!res.success) return;

            sub.assignee = res.assignee_id ? findMemberInBoard(res.assignee_id) : null;
            renderSubtasks();
            closeAllPickers();
        }
    }

    async function removeAssignee(memberId) {
        const res = await postJSON("task_assign_toggle.php", {
            task_id: state.taskId,
            user_id: memberId,
        });
        if (!res.success) return;
        state.assignees = state.assignees.filter(a => String(a.id) !== String(memberId));
        renderAssignees();
    }

    async function removeAssigneeBoard(memberId, boardId) {
        const res = await postJSON("board_assign_toggle.php", {
            board_id: boardId,
            user_id: memberId,
        });
        if (!res.success) return;
        state.assignees = state.assignees.filter(a => String(a.id) !== String(memberId));
        renderAssignees();
    }


    function renderAssignees() {
        const row = document.getElementById("taskAssigneeRow");
        if (!row) return;

        const chips = state.assignees.map(m => `
    <div class="assignee-chip">
        ${avatarHTML(m)}
        <span>${escapeHTML(m.name)}</span>
        <button class="assignee-remove" onclick="TaskDetailModal.removeAssignee('${m.id}')">
            <i class="fa-solid fa-x"></i>
        </button>
    </div>
`).join("");

        const addBtn = `
    <button class="btn-add-assignee" id="btnAddAssignee" onclick="TaskDetailModal.togglePicker('task')">
        <i class="fa-solid fa-plus"></i> Assign
    </button>
    <div class="assignee-picker" id="assigneePicker-task">
        <div class="assignee-picker-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Cari anggota..." oninput="TaskDetailModal.filterPicker('task', this.value)">
        </div>
        <div class="assignee-picker-list" id="assigneePickerList-task"></div>
    </div>
`;

        row.innerHTML = chips + addBtn;
    }

    async function onSubtaskInputKeydown(e) {
        if (e.key !== "Enter") return;
        e.preventDefault();
        const input = e.target;
        const name = input.value.trim();
        if (!name) return;

        input.disabled = true;
        const res = await postJSON("subtask_post.php", {
            task_id: state.taskId,
            subtask_name: name
        });
        input.disabled = false;

        if (!res.success) return;

        state.subtasks.push({
            id: res.id,
            name,
            done: false,
            assignee: null
        });
        input.value = "";
        renderSubtasks();
    }

    async function toggleSubtask(id) {
        const sub = state.subtasks.find(s => s.id === id);
        if (!sub) return;

        const res = await postJSON("subtask_status_toggle.php", {
            subtask_id: id
        });
        if (!res.success) return;

        sub.done = res.status === "DONE";
        renderSubtasks();
    }

    async function deleteSubtask(id) {
        const res = await postJSON("subtask_delete.php", {
            subtask_id: id
        });
        if (!res.success) return;

        state.subtasks = state.subtasks.filter(s => s.id !== id);
        renderSubtasks();
    }

    function renderSubtasks() {
        const list = document.getElementById("subtaskList");
        const progressLabel = document.getElementById("subtaskProgress");
        const progressFill = document.getElementById("subtaskProgressFill");
        if (!list) return;

        if (state.subtasks.length === 0) {
            list.innerHTML = `<div style="font-size:12px;color:var(--text-lo);padding:6px 2px;">Belum ada subtask.</div>`;
        } else {
            list.innerHTML = state.subtasks.map(sub => {
                const key = `subtask-${sub.id}`;
                return `
            <div class="subtask-item ${sub.done ? "done" : ""}" data-subtask-id="${sub.id}">
                <div class="subtask-check ${sub.done ? "done" : ""}" onclick="TaskDetailModal.toggleSubtask(${sub.id})"></div>
                <span class="subtask-name">${escapeHTML(sub.name)}</span>
                <div class="subtask-assignee" onclick="TaskDetailModal.togglePicker('${key}')">
                    ${avatarHTML(sub.assignee)}
                    <div class="assignee-picker" id="assigneePicker-${key}" style="left:auto;right:0;">
                        <div class="assignee-picker-search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" placeholder="Cari anggota..." oninput="TaskDetailModal.filterPicker('${key}', this.value)">
                        </div>
                        <div class="assignee-picker-list" id="assigneePickerList-${key}"></div>
                    </div>
                </div>
                <button class="subtask-delete" onclick="TaskDetailModal.deleteSubtask(${sub.id})">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `;
            }).join("");
        }

        const total = state.subtasks.length;
        const doneCount = state.subtasks.filter(s => s.done).length;
        if (progressLabel) progressLabel.textContent = `${doneCount}/${total}`;
        if (progressFill) progressFill.style.width = total ? `${(doneCount / total) * 100}%` : "0%";
    }


    function onCommentInputKeydown(e) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            addComment();
        }
    }

    async function addComment() {
        const input = document.getElementById("commentInput");
        const text = input.value.trim();
        if (!text) return;

        input.disabled = true;
        const res = await postJSON("comment_post.php", {
            task_id: state.taskId,
            comment: text
        });
        input.disabled = false;

        if (!res.success) {
            alert(res.message || "Gagal mengirim komentar.");
            return;
        }

        state.comments.push(res.comment);
        input.value = "";
        renderComments();
    }

    function renderComments() {
        const list = document.getElementById("commentsList");
        if (!list) return;

        if (state.comments.length === 0) {
            list.innerHTML = `<div style="font-size:12px;color:var(--text-lo);">Belum ada komentar.</div>`;
        } else {
            list.innerHTML = state.comments.map(c => `
        <div class="comment-item">
            ${avatarHTML({ id: c.user_id, name: c.name, avatar: c.avatar })}
            <div class="comment-bubble">
                <div class="comment-meta">
                    <span class="comment-author">${escapeHTML(c.name)}</span>
                    <span class="comment-time">${formatTime(c.created_at)}</span>
                </div>
                <div class="comment-text">${escapeHTML(c.comment)}</div>
            </div>
        </div>
    `).join("");
        }

        list.scrollTop = list.scrollHeight;
    }

    function formatTime(datetimeStr) {
        if (!datetimeStr) return "";
        const d = new Date(datetimeStr.replace(" ", "T"));
        if (isNaN(d)) return datetimeStr;
        return d.toLocaleTimeString("id-ID", {
            hour: "2-digit",
            minute: "2-digit"
        });
    }

    function confirmDeletSubtask() {
        document.getElementById('confirmModal').classList.add('show');
    }

    async function deleteTask() {
        
        const res = await postJSON("task_delete.php", {
            id: state.taskId
        });
        if (!res.success) {
            alert(res.message || "Gagal menghapus tugas.");
            return;
        }
        document.getElementById('confirmModal').classList.remove('show');
        close();
        location.reload();
    }

    function escapeHTML(str) {
        const div = document.createElement("div");
        div.textContent = str ?? "";
        return div.innerHTML;
    }

    return {
        open,
        close,
        onTitleBlur,
        onDescBlur,
        togglePicker,
        filterPicker,
        pickMember,
        removeAssignee,
        removeAssigneeBoard,
        onSubtaskInputKeydown,
        toggleSubtask,
        deleteSubtask,
        onCommentInputKeydown,
        addComment,
        deleteTask,
        confirmDeletSubtask
    };

})();