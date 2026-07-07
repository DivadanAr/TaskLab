/**
 * BoardInfoPanel — panel floating berisi nama board (editable inline),
 * deskripsi (rich text editor), dan tombol hapus board.
 *
 * Catatan: fungsi simpan-ke-database (update nama, update deskripsi,
 * hapus board) belum dihubungkan ke backend — tinggal isi di bagian
 * yang ditandai TODO.
 */
const BoardInfoPanel = (() => {
  let panelEl;
  let boardId;
  let boardTitle;
  let boardDesc;
  
  function init() {
    panelEl = document.getElementById("boardInfoPanel");
    boardId = document.getElementById("boardId").value;
    boardTitle = document.getElementById("boardTitle");
    boardDesc = document.getElementById("boardDesc");
  }

  function toggle() {
    if (!panelEl) return;
    panelEl.classList.contains("open") ? close() : open();
  }

  function open() {
    panelEl.classList.add("open");
  }

  function close() {
    panelEl.classList.remove("open");
  }

async function onNameBlur(input) {
  const newName = input.value.trim();

  if (!newName) {
    input.value = input.dataset.original || "";
    return;
  }

  if (newName === input.dataset.original) return;

  try {
    const formData = new FormData();
    formData.append("board_id", boardId);
    formData.append("field", "board_name");
    formData.append("value", newName);

    const res = await fetch("../actions/board/board_update.php", {
      method: "POST",
      body: formData,
    });

    const data = await res.json();

    if (data.success) {
      boardTitle.textContent = newName
      input.value = newName;
      showSaveHint();
    } else {
      alert(data.message);
      input.value = input.value;
    }
  } catch (err) {
    console.error(err);
    input.value = input.value;
  }
}

  function enterDescEditMode() {
    const viewer = document.getElementById("boardDescViewer");
    const viewerContent = document.getElementById("boardDescViewerContent");
    const editWrap = document.getElementById("boardDescEditWrap");
    const editor = document.getElementById("boardDescEditor");
    if (!viewer || !viewerContent || !editWrap || !editor) return;

    editor.innerHTML = viewerContent.innerHTML;

    viewer.style.display = "none";
    editWrap.style.display = "block";
    editor.focus();
  }

async function onDescBlur(editor) {
  const html = editor.innerHTML.trim();

  try {
    const formData = new FormData();
    formData.append("board_id", boardId);
    formData.append("field", "description");
    formData.append("value", html);
    
    const res = await fetch("../actions/board/board_update.php", {
      method: "POST",
      body: formData,
    });

    const data = await res.json();

    if (!data.success) {
      alert(data.message);
      return;
    } else {
      boardDesc.textContent = html
    }

    const viewer = document.getElementById("boardDescViewer");
    const viewerContent = document.getElementById("boardDescViewerContent");
    const editWrap = document.getElementById("boardDescEditWrap");

    viewerContent.innerHTML = html;
    editWrap.style.display = "none";
    viewer.style.display = "block";

    showSaveHint();

  } catch (err) {
    console.error(err);
  }
}
  function showSaveHint() {
    const hint = document.getElementById("boardInfoSaveHint");
    if (!hint) return;
    hint.classList.add("show");
    clearTimeout(hint._timeout);
    hint._timeout = setTimeout(() => hint.classList.remove("show"), 1600);
  }

  function deleteBoard(boardId) {
    const boardName = document.getElementById("boardInfoNameInput")?.value || "board ini";
    const modal = document.getElementById("confirmModal");
    const title = document.getElementById("confirmTitle");
    const message = document.getElementById("confirmMessage");
    const confirmBtn = document.getElementById("confirmActionBtn");
    if (!modal) return;

    if (title) title.textContent = `Hapus "${boardName}"?`;
    if (message) message.textContent = "Semua tugas di dalam board ini akan ikut terhapus. Tindakan ini tidak bisa dibatalkan.";

    modal.classList.add("show");

    if (confirmBtn) {
        confirmBtn.onclick = async () => {
      try {
        confirmBtn.disabled = true;
        confirmBtn.textContent = "Menghapus...";

        const formData = new FormData();
        formData.append("board_id", boardId);

        const res = await fetch("../actions/board/board_delete.php", {
          method: "POST",
            body: formData,
          });

          const data = await res.json();

          if (!data.success) {
            alert(data.message || "Gagal menghapus board.");
            return;
          }

          modal.classList.remove("show");

          // Redirect ke halaman daftar board
          window.location.href = "../dashboard.php";

          // atau reload jika tetap di halaman yang sama
          // location.reload();

        } catch (err) {
          console.error(err);
          alert("Terjadi kesalahan.");
        } finally {
          confirmBtn.disabled = false;
          confirmBtn.textContent = "Hapus";
        }

        }
      }
  }
  return { init, toggle, open, close, onNameBlur, enterDescEditMode, onDescBlur, deleteBoard };
})();

/**
 * RichEditor — toolbar sederhana untuk contenteditable ala Word,
 * memakai document.execCommand (cukup untuk bold/italic/list/align dasar).
 */
const RichEditor = (() => {
  function exec(command) {
    document.execCommand(command, false, null);
    document.getElementById("boardDescEditor")?.focus();
    syncToolbarState();
  }

  function syncToolbarState() {
    document.querySelectorAll(".richtext-toolbar button[data-cmd]").forEach((btn) => {
      const isActive = document.queryCommandState(btn.dataset.cmd);
      btn.classList.toggle("active", !!isActive);
    });
  }

  function init() {
    const editor = document.getElementById("boardDescEditor");
    if (!editor) return;
    editor.addEventListener("keyup", syncToolbarState);
    editor.addEventListener("mouseup", syncToolbarState);
  }

  return { exec, init };
})();

document.addEventListener("DOMContentLoaded", () => {
  BoardInfoPanel.init();
  RichEditor.init();
});

// Dipakai tombol "Batal" pada #confirmModal (sudah ada di markup board-detail.php)
function closeConfirmModal() {
  document.getElementById("confirmModal")?.classList.remove("show");
}
