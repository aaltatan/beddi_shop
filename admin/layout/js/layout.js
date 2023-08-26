const modeBtn = document.querySelector("#mode-btn + label");
const burgerBtn = document.querySelector("#burger");
const mainAside = document.querySelector(".main-aside");
const mainSearchInput = document.querySelector("input[name=main-search]");
const userList = document.querySelector("header nav .user");
const searchContainer = document.querySelector(".search");
const searchInput = document.querySelector(".search input[name=main-search]");
const searchList = document.querySelector(".search ul.list");

searchInput.addEventListener("keyup", () => {
  if (searchInput.value === "") {
    searchList.classList.remove("opened");
  } else {
    searchList.classList.add("opened");
    let re = new RegExp(searchInput.value, "i");
    searchList.querySelectorAll("li a p").forEach((text) => {
      text.parentElement.parentElement.classList.remove("opened");
      re.test(text.innerHTML) &&
        text.parentElement.parentElement.classList.add("opened");
    });
  }
});
searchInput.addEventListener("blur", () => {
  if (searchInput.value === "") {
    searchList.classList.remove("opened");
  }
});

userList.querySelector(".icon h2").addEventListener("click", () => {
  userList.querySelector(".list").classList.toggle("opened");
});

document.addEventListener("keydown", (e) => {
  if (e.key.toLowerCase() === "s" && e.altKey) {
    mainSearchInput.focus();
  }
  if (e.key.toLowerCase() === "س" && e.altKey) {
    mainSearchInput.focus();
  }
  if (e.key.toLowerCase() === "1" && e.altKey) {
    window.location.href = "dashboard.php";
  }
  if (e.key.toLowerCase() === "2" && e.altKey) {
    window.location.href = "categories.php";
  }
  if (e.key.toLowerCase() === "3" && e.altKey) {
    window.location.href = "items.php";
  }
  if (e.key.toLowerCase() === "4" && e.altKey) {
    window.location.href = "members.php";
  }
  if (e.code === "Escape") {
    mainSearchInput.blur();
    closeAside();
  }
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

function openAside() {
  burgerBtn.classList.toggle("opened");
  mainAside.classList.toggle("opened");
}
function closeAside() {
  burgerBtn.classList.remove("opened");
  mainAside.classList.remove("opened");
}
