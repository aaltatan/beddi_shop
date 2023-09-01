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
const userId = +document.body.getAttribute("data-user-id");
const shoppingCart = document.querySelector("#shopping-cart");
const shoppingCartBody = document.querySelector("#shopping-cart .body");
const shoppingCartTotal = document.querySelector(
  "#shopping-cart #shopping-cart-total"
);
const addToCartBtns = document.querySelectorAll(
  "button[data-role='add-to-cart']"
);
const shoppingCartCountSpan = document.getElementById("shopping-cart-count");
const elementsObserver = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add("opened");
    } else {
      entry.target.classList.remove("opened");
    }
  });
});

getCart();

addToCartBtns.forEach((addToCartBtn) => {
  addToCartBtn.addEventListener("click", () => {
    let itemId = addToCartBtn.getAttribute("data-item-id");
    addToCart(itemId);
  });
});

likeBtns.forEach((btn) => {
  btn.addEventListener("click", () => {
    let itemId = btn.parentElement.getAttribute("data-item-id");
    let isLiked = btn.parentElement.getAttribute("data-is-liked");
    if (isLiked === "1") {
      fetch(`api/likes.php?do=Unlike&userid=${userId}&itemid=${itemId}`).then(
        (data) => data
      );
      btn.parentElement.setAttribute("data-is-liked", "0");
    } else {
      fetch(`api/likes.php?do=Like&userid=${userId}&itemid=${itemId}`).then(
        (data) => data
      );
      btn.parentElement.setAttribute("data-is-liked", "1");
    }
  });
});

specialGoRight.addEventListener("click", () => {
  specialContainer.scrollBy({
    left: "500",
    behavior: "smooth",
  });
});
specialGoLeft.addEventListener("click", () => {
  specialContainer.scrollBy({
    left: "-500",
    behavior: "smooth",
  });
});

observedElements.forEach((observedElement) => {
  elementsObserver.observe(observedElement);
});

checker(current, images, lis);

nextBtn.addEventListener("click", () => {
  current++;
  if (current > imagesCount) current = 1;
  if (current < 1) current = imagesCount;
  checker(current, images, lis);
});
prevBtn.addEventListener("click", () => {
  current--;
  if (current > imagesCount) current = 1;
  if (current < 1) current = imagesCount;
  checker(current, images, lis);
});

setInterval(() => {
  nextBtn.click();
}, 5000);

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

async function getCart() {
  const res = await fetch(`./api/cart.php?do=Get&userid=${userId}`);
  const data = await res.json();
  shoppingCartCountSpan.innerHTML = data.length;

  shoppingCartBody.innerHTML = "";

  let total = 0;

  for (let itemChild of data) {
    let item = createEl("div", "", ["class", "item"]);

    let img = createEl(
      "img",
      "",
      ["src", itemChild["img"].slice(1)],
      ["alt", "dasd"]
    );
    let info = createEl("div", "", ["class", "info"]);

    let infoRowOne = createEl("div", "", ["class", "row-1"]);

    let titleSpan = createEl(
      "span",
      itemChild["item_name"] + ` (${itemChild["quantity"]})`
    );
    let deleteSpan = createEl("span", "");
    let deleteI = createEl(
      "i",
      "",
      ["class", "fa-solid fa-trash"],
      ["title", "Delete " + itemChild["item_name"]]
    );
    deleteI.addEventListener("click", () => {
      deleteFromCart(itemChild["item_id"]);
    });
    deleteSpan.appendChild(deleteI);

    infoRowOne.appendChild(titleSpan);
    infoRowOne.appendChild(deleteSpan);

    let infoRowTwo = createEl("div", "", ["class", "row-2"]);

    let controlDiv = createEl("div", "", ["class", "control"]);

    let plusSpan = createEl("span", "");
    let plusI = createEl("i", "", ["class", "fa-solid fa-plus-square"]);
    plusI.addEventListener("click", () => addQuantity(itemChild["item_id"]));
    plusSpan.appendChild(plusI);

    let minusSpan = createEl("span", "");
    let minusI = createEl("i", "", ["class", "fa-solid fa-minus-square"]);
    minusI.addEventListener("click", () =>
      subtractQuantity(itemChild["item_id"])
    );
    minusSpan.appendChild(minusI);

    controlDiv.appendChild(plusSpan);
    controlDiv.appendChild(minusSpan);

    let totalItem = itemChild["item_price"] * itemChild["quantity"];
    let totalSpan = createEl("span", totalItem.toLocaleString());

    infoRowTwo.appendChild(controlDiv);
    infoRowTwo.appendChild(totalSpan);

    info.appendChild(infoRowOne);
    info.appendChild(infoRowTwo);

    item.appendChild(img);
    item.appendChild(info);

    shoppingCartBody.appendChild(item);
    total += totalItem;
  }

  shoppingCartTotal.innerHTML = total.toLocaleString();

  return data;
}

async function addToCart(itemId) {
  const res = await fetch(
    `./api/cart.php?do=Add&userid=${userId}&itemid=${itemId}`
  );
  const data = res.status;
  getCart();
  return data;
}

async function addQuantity(itemId) {
  const res = await fetch(
    `./api/cart.php?do=Plus&userid=${userId}&itemid=${itemId}`
  );
  const data = res.status;
  getCart();
  return data;
}

async function subtractQuantity(itemId) {
  const res = await fetch(
    `./api/cart.php?do=Minus&userid=${userId}&itemid=${itemId}`
  );
  const data = res.status;
  getCart();
  return data;
}

async function deleteFromCart(itemId) {
  const res = await fetch(
    `./api/cart.php?do=Delete&userid=${userId}&itemid=${itemId}`
  );
  const data = res.status;
  getCart();
  return data;
}

/**
 * add a list of classes to an HTML element
 * @param {HTMLElement} el - the html element
 * @param  {...string} classes - the list of classes to add
 * @returns {HTMLElement}
 */
function addClasses(el, ...classes) {
  el.classList.add(...classes);
  return el;
}

/**
 * Create an Element Node with its attributes
 * @param {string} el - HTML Tag
 * @param {string} textNode - Text Node
 * @param  {...string} attributes - list of the attributes [[att,val],[att,val]]
 * @returns {HTMLElement}
 */
function createEl(el = "div", textNode = "", ...attributes) {
  let element = document.createElement(el);
  if (attributes.length) {
    for (let attribute of attributes) {
      element.setAttribute(attribute[0], attribute[1]);
    }
  }
  if (textNode !== "") {
    let tn = document.createTextNode(textNode);
    element.appendChild(tn);
  }
  return element;
}
