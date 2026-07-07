/**
 * TaskToggle — klik lingkaran di kiri task buat toggle status TODO <-> DONE.
 * UI langsung berubah duluan (optimistic), baru sinkron ke server;
 * kalau request gagal, tampilannya dikembalikan seperti semula.
 */
const TaskToggle = (() => {
  function toggle(checkEl) {
    const card = checkEl.closest(".task-card");
    const taskId = checkEl.dataset.taskId;
    if (!taskId) return;

    const willBeDone = !checkEl.classList.contains("done");
    setVisualState(checkEl, card, willBeDone);

    const formData = new FormData();
    formData.append("task_id", taskId);

    fetch("../actions/task/task_status_toggle.php", {
      method: "POST",
      body: formData,
    })
      .then((r) => r.json())
      .then((data) => {
        if (!data.success) {
          // Gagal -> balikin tampilan ke kondisi semula
          setVisualState(checkEl, card, !willBeDone);
          App.toast(data.message || "Gagal memperbarui status tugas.", "error");
        }
      })
      .catch(() => {
        setVisualState(checkEl, card, !willBeDone);
        App.toast("Terjadi kesalahan, coba lagi.", "error");
      });
  }

  function setVisualState(checkEl, card, isDone) {
    checkEl.classList.toggle("done", isDone);
    if (card) card.classList.toggle("done", isDone);
  }

  return { toggle };
})();
