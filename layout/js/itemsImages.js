const imageContainer = document.getElementById("images-show-container");
const showBtns = document.querySelectorAll(".show-images");

showBtns.forEach((showBtn) => {
  showBtn.addEventListener("click", () => {
    let srcs = showBtn.getAttribute("data-images-src").split("\n").slice(1);
    imageContainer.innerHTML = "";
    srcs.forEach((src) => {
      let img = document.createElement("img");
      img.setAttribute("src", src);
      imageContainer.appendChild(img);
    });
    imageContainer.classList.add("opened");
    let xBtn = document.createElement("span");
    xBtn.appendChild(document.createTextNode("×"));
    xBtn.addEventListener("click", () => {
      imageContainer.classList.remove("opened");
    });
    imageContainer.appendChild(xBtn);
  });
});
