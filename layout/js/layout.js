const modeBtn = document.querySelector("#mode-btn + label");
const burgerBtn = document.querySelector("#burger");
const mainAside = document.querySelector(".main-aside");
const mainSearchInput = document.getElementById("main-search");
const userList = document.querySelector("header nav .user");

userList.querySelector(".icon h2").addEventListener("click", () => {
  userList.querySelector(".list").classList.toggle("opened");
});

document.addEventListener("click", (e) => {
  if (
    e.target.id !== "burger" &&
    !e.target.closest(".main-aside") &&
    e.target.id !== "user-name"
  ) {
    burgerBtn.classList.remove("opened");
    mainAside.classList.remove("opened");
    userList.querySelector(".list").classList.remove("opened");
  }
});

modeBtn.addEventListener("click", () => {
  if (modeBtn.getAttribute("data-theme-dark") === "true") {
    modeBtn.setAttribute("data-theme-dark", "false");
    localStorage.setItem("theme", "dark");
  } else {
    modeBtn.setAttribute("data-theme-dark", "true");
    localStorage.setItem("theme", "light");
  }
  document.body.classList.toggle("light");
});

document.documentElement.setAttribute("hidden", "");
document.addEventListener("DOMContentLoaded", () => {
  if (localStorage.getItem("theme") === "dark") {
    document.querySelector("#mode-btn").removeAttribute("checked");
    modeBtn.setAttribute("data-theme-dark", "false");
    document.body.classList.remove("light");
  } else {
    document.querySelector("#mode-btn").setAttribute("checked", "true");
    modeBtn.setAttribute("data-theme-dark", "true");
    document.body.classList.add("light");
  }
  document.querySelectorAll(".main-aside li a").forEach((anchor) => {
    anchor.ariaCurrent = "false";
    const pathName = location.pathname.split("/").at(-1);
    if (pathName === anchor.getAttribute("href")) {
      anchor.ariaCurrent = "true";
    }
  });
  document.documentElement.removeAttribute("hidden");
});

burgerBtn.addEventListener("click", () => {
  openAside();
});

document.addEventListener("keydown", (e) => {
  if (e.key.toLowerCase() === "s" && e.altKey) {
    mainSearchInput.focus();
  }
  if (e.key.toLowerCase() === "س" && e.altKey) {
    mainSearchInput.focus();
  }
  if (e.code === "Escape") {
    mainSearchInput.blur();
  }
});

function openAside() {
  burgerBtn.classList.toggle("opened");
  mainAside.classList.toggle("opened");
}
