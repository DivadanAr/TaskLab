const Sidebar = (() => {
  const COLLAPSE_KEY = "tasklab-sidebar-collapsed";
  let activeItem = null;

  function isCollapsed() {
    return localStorage.getItem(COLLAPSE_KEY) === "1";
  }

  function applyState(collapsed) {
    const sidebar = document.getElementById("sidebar");
    const chevron = document.getElementById("sbChevron");
    if (!sidebar) return;

    sidebar.classList.toggle("collapsed", collapsed);
    if (chevron) chevron.style.transform = collapsed ? "rotate(180deg)" : "";
  }

  function toggle() {
    const next = !isCollapsed();
    localStorage.setItem(COLLAPSE_KEY, next ? "1" : "0");
    applyState(next);

    document.dispatchEvent(
      new CustomEvent("sidebar:toggle", { detail: { collapsed: next } }),
    );
  }

  function selectItem(btn, label) {
    if (activeItem) {
      activeItem.classList.remove("active");
      activeItem.removeAttribute("aria-current");
    }

    btn.classList.add("active");
    btn.setAttribute("aria-current", "page");
    activeItem = btn;

    if (window.App && App.setPageTitle) App.setPageTitle(label);

    document.dispatchEvent(
      new CustomEvent("sidebar:select", { detail: { label } }),
    );
  }

  function toggleGroup(btn) {
    const isOpen = btn.classList.toggle("open");
    btn.setAttribute("aria-expanded", isOpen);

    const body = btn.nextElementSibling;
    body.style.maxHeight = isOpen ? body.scrollHeight + "px" : "0";
  }

  function init() {
    // Sinkronkan ulang (state awal sudah dipasang oleh inline script
    // di sidebar.php supaya tidak "kilat" saat reload).
    applyState(isCollapsed());

    activeItem = document.querySelector(".sb-item.active");

    document.querySelectorAll(".sb-group-body").forEach((body) => {
      body.style.maxHeight = body.scrollHeight + "px";
    });
  }

  document.addEventListener("app:ready", init);

  return { toggle, selectItem, toggleGroup };
})();
