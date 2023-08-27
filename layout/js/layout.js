const burgerBtn = document.querySelector(".burger");
const navBarList = document.querySelector("header nav .links");
const navBarIcons = document.querySelector("header nav .icons");
const categoriesShow = document.getElementById("show-categories");
const itemsShow = document.getElementById("show-items");
const categoriesContainer = document.getElementById("categories");
const itemsContainer = document.getElementById("items");
const modeBtn = document.getElementById("mode-btn");
const shoppingCartBtn = document.getElementById("shopping-cart-btn");
const blurWall = document.getElementById("blur");
const shoppingCartBtnClose = document.querySelector(
  "#shopping-cart .heading span"
);
const shoppingCartContainer = document.getElementById("shopping-cart");

shoppingCartBtn.addEventListener("click", () => {
  shoppingCartContainer.classList.toggle("opened");
  blurWall.classList.toggle("opened");
});
shoppingCartBtnClose.addEventListener("click", () => {
  shoppingCartContainer.classList.remove("opened");
  blurWall.classList.remove("opened");
});
blurWall.addEventListener("click", () => {
  shoppingCartContainer.classList.remove("opened");
  blurWall.classList.remove("opened");
});

burgerBtn.addEventListener("click", () => {
  navBarList.classList.toggle("opened");
  burgerBtn.classList.toggle("opened");
  navBarIcons.classList.toggle("opened");
});

categoriesShow.addEventListener("click", () => {
  categoriesShow.classList.toggle("opened");
  categoriesContainer.classList.toggle("opened");
});

itemsShow.addEventListener("click", () => {
  itemsShow.classList.toggle("opened");
  itemsContainer.classList.toggle("opened");
});

modeBtn.addEventListener("click", () => {
  if (modeBtn.getAttribute("data-theme-dark") === "true") {
    modeBtn.setAttribute("data-theme-dark", "false");
    localStorage.setItem("theme", "dark");
    modeBtn.querySelector("i:first-child").style.display = "block";
    modeBtn.querySelector("i:last-child").style.display = "none";
  } else {
    modeBtn.setAttribute("data-theme-dark", "true");
    localStorage.setItem("theme", "light");
    modeBtn.querySelector("i:first-child").style.display = "none";
    modeBtn.querySelector("i:last-child").style.display = "block";
  }
  document.body.classList.toggle("light");
});

document.documentElement.setAttribute("hidden", "");
document.addEventListener("DOMContentLoaded", () => {
  if (localStorage.getItem("theme") === "dark") {
    modeBtn.setAttribute("data-theme-dark", "false");
    document.body.classList.remove("light");
    modeBtn.querySelector("i:first-child").style.display = "block";
    modeBtn.querySelector("i:last-child").style.display = "none";
  } else {
    modeBtn.setAttribute("data-theme-dark", "true");
    document.body.classList.add("light");
    modeBtn.querySelector("i:first-child").style.display = "none";
    modeBtn.querySelector("i:last-child").style.display = "block";
  }
  document.documentElement.removeAttribute("hidden");
});
