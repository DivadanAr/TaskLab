/**
 * Inbox — logic accept/decline undangan sudah dipindah ke PHP
 * (form POST ke actions/collaboration/invitation_respond.php, lihat inbox.php).
 * JS di sini cuma buat urusan tampilan: pindah tab Undangan <-> Notifikasi.
 */
const Inbox = (() => {
  function switchTab(tabBtn, tabName) {
    document.querySelectorAll(".inbox-tab").forEach((t) => t.classList.remove("active"));
    tabBtn.classList.add("active");
    document.querySelectorAll("[data-inbox-panel]").forEach((panel) => {
      panel.style.display = panel.dataset.inboxPanel === tabName ? "flex" : "none";
    });
  }

  return { switchTab };
})();
