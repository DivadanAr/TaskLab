const App = (() => {
  const state = {
    navOpen: true,
    activePage: "dashboard",
    activeNav: "Semua Tugas",
    activeRail: "Beranda",
  };

  function init() {
    document.dispatchEvent(new CustomEvent("app:ready", { detail: state }));
  }

  function toggleNav() {
    state.navOpen = !state.navOpen;
    document.dispatchEvent(
      new CustomEvent("nav:toggle", { detail: { open: state.navOpen } }),
    );
  }

  function setPageTitle(title) {
    const el = document.getElementById("pageTitle");
    if (el) el.textContent = title;
  }

  return { init, toggleNav, setPageTitle, state };
})();

document.addEventListener("DOMContentLoaded", () => App.init());
