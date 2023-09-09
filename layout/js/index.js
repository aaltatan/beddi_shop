let current = 1;
const images = document.querySelectorAll(".image-slider .images img");
const imagesCount = images.length;
const lis = document.querySelectorAll(".index li");
const prevBtn = document.getElementById("prev");
const nextBtn = document.getElementById("next");
const observedElements = document.querySelectorAll(".observe");
const specialContainer = document.querySelector(".specials-container");
const specialGoRight = document.getElementById("go-right");
const specialGoLeft = document.getElementById("go-left");
const likeBtns = document.querySelectorAll(".like-btn");
const loginReminder = document.querySelector(".login-reminder");
const loginReminderSpan = document.querySelector(".login-reminder span");
const elementsObserver = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add("opened");
    } else {
      entry.target.classList.remove("opened");
    }
  });
});

if (loginReminderSpan) {
  loginReminderSpan.addEventListener("click", () => {
    loginReminder.classList.add("closed");
  });
}

likeBtns.forEach((btn) => {
  btn.addEventListener("click", () => {
    let itemId = btn.parentElement.getAttribute("data-item-id");
    let isLiked = btn.parentElement.getAttribute("data-is-liked");
    if (isLiked === "1") {
      fetch(`./api/likes.php?do=Unlike&userid=${userId}&itemid=${itemId}`)
        .then((res) => res.text())
        .then((data) => data);
      btn.parentElement.setAttribute("data-is-liked", "0");
    } else {
      fetch(`./api/likes.php?do=Like&userid=${userId}&itemid=${itemId}`)
        .then((res) => res.text())
        .then((data) => data);
      btn.parentElement.setAttribute("data-is-liked", "1");
    }
  });
});

if (specialGoRight) {
  specialGoRight.addEventListener("click", () => {
    specialContainer.scrollBy({
      left: "500",
      behavior: "smooth",
    });
  });
}
if (specialGoLeft) {
  specialGoLeft.addEventListener("click", () => {
    specialContainer.scrollBy({
      left: "-500",
      behavior: "smooth",
    });
  });
}

observedElements.forEach((observedElement) => {
  elementsObserver.observe(observedElement);
});

checker(current, images, lis);

if (nextBtn) {
  nextBtn.addEventListener("click", () => {
    current++;
    if (current > imagesCount) current = 1;
    if (current < 1) current = imagesCount;
    checker(current, images, lis);
  });
  setInterval(() => {
    nextBtn.click();
  }, 5000);
}
if (prevBtn) {
  prevBtn.addEventListener("click", () => {
    current--;
    if (current > imagesCount) current = 1;
    if (current < 1) current = imagesCount;
    checker(current, images, lis);
  });
}

function checker(current, images, lis) {
  images.forEach((img, idx) => {
    idx + 1 === current ? (img.ariaCurrent = true) : (img.ariaCurrent = false);
  });
  lis.forEach((li, idx) => {
    idx + 1 === current ? (li.ariaCurrent = true) : (li.ariaCurrent = false);
    li.addEventListener("click", () => {
      li.ariaCurrent = true;
      current = idx + 1;
      checker(current, images, lis);
    });
  });
}
