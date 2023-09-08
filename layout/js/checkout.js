const showBtn = document.getElementById("show-order-summary");
const orderSummary = document.querySelector(".order-summary-container");
const selectCountry = document.getElementById("checkout-country");

showBtn.addEventListener("click", () =>
  orderSummary.classList.toggle("opened")
);
document.getElementById("shopping-cart-btn").remove();

const url = "https://restcountries.com/v3.1/all?fields=name,flags";

fetch(url)
  .then((res) => res.json())
  .then((data) => {
    data.sort();
    data.forEach((country) => {
      let countryOption = createEl("option", country["name"]["official"], [
        "value",
        country["name"]["common"],
      ]);
      country["name"]["common"] === "Syria" &&
        countryOption.setAttribute("selected", "");
      selectCountry.appendChild(countryOption);
    });
  });

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
