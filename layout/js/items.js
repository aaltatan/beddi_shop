var userId = document.body.getAttribute("data-user-id");
const primaryImage = document.querySelector(
  ".items-item-container .images > img"
);
const secondaryImages = document.querySelectorAll(
  ".items-item-container .images .sub-images img"
);
const likeBtns = document.querySelectorAll(".like-btn");
const likesCount = document.getElementById("likes-count");
const commentBox = document.getElementById("comment-box");
const addCommentBtn = document.querySelector("button[role='add-comment']");
const itemId = document
  .getElementById("item-heading-id")
  .getAttribute("data-item-id");
const commentsContainer = document.querySelector(
  ".comment-container .comments"
);

getComments(itemId);

if (addCommentBtn) {
  addCommentBtn.addEventListener("click", () => {
    let commentValue = commentBox.value;
    setTimeout(addComment, 500, commentValue, itemId);
    setTimeout(getComments, 500, itemId);
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
      getLikers(itemId);
    } else {
      fetch(`./api/likes.php?do=Like&userid=${userId}&itemid=${itemId}`)
        .then((res) => res.text())
        .then((data) => data);
      btn.parentElement.setAttribute("data-is-liked", "1");
      setTimeout(getLikers, 500, itemId);
    }
  });
});

secondaryImages.forEach((img) => {
  img.addEventListener("click", () => {
    let imgSrc = img.getAttribute("src");
    primaryImage.setAttribute("src", imgSrc);
  });
});

function addComment(comment, itemId) {
  return fetch(`./api/comments.php?do=Add&comment=${comment}&itemid=${itemId}`)
    .then((res) => res.status)
    .then((data) => {
      commentBox.value = "";
    });
}

function deleteComment(itemId, commentId) {
  return fetch(
    `./api/comments.php?do=Delete&comment=${commentId}&itemid=${itemId}`
  )
    .then((res) => res.text())
    .then((data) => data);
}

function getComments(itemId) {
  return fetch(`./api/comments.php?do=Get&itemid=${itemId}`)
    .then((res) => res.json())
    .then((data) => {
      commentsContainer.innerHTML = "";

      for (let row of data) {
        const { added_date, comment, comment_id, full_name, user_id } = row;

        const commentWrapper = createEl("div", "", [
          "class",
          "comment-wrapper",
        ]);

        const commentHeading = createEl("div", "", ["class", "heading"]);
        const commentP = createEl("p", comment);

        const commentImage = createEl("img", "", [
          "src",
          "./admin/layout/images/user-128x128.png",
        ]);
        const commentName = createEl("div", "", ["class", "name"]);
        const commentDots = createEl("div", "", ["class", "dots"]);

        const commentNameSpan = createEl("span", full_name);
        const commentDateSpan = createEl("span", added_date);
        commentName.appendChild(commentNameSpan);
        commentName.appendChild(commentDateSpan);

        const commentDotsList = createEl("div", "", ["class", "list"]);
        const commentDotsListDeleteBtn = createEl(
          "button",
          "",
          ["title", "Delete Comment"],
          ["class", "btn btn-primary"],
          ["role", "delete-comment"],
          ["data-comment-id", comment_id]
        );
        commentDotsListDeleteBtn.addEventListener("click", () => {
          setTimeout(deleteComment, 500, itemId, comment_id);
          setTimeout(getComments, 500, itemId);
        });
        const commentDotsListDeleteBtnI = createEl("i", "", [
          "class",
          "fa-solid fa-trash",
        ]);
        commentDotsListDeleteBtn.appendChild(commentDotsListDeleteBtnI);
        commentDotsList.appendChild(commentDotsListDeleteBtn);
        commentDots.appendChild(commentDotsList);

        commentHeading.appendChild(commentImage);
        commentHeading.appendChild(commentName);
        userId == user_id && commentHeading.appendChild(commentDots);

        commentWrapper.appendChild(commentHeading);
        commentWrapper.appendChild(commentP);

        commentsContainer.appendChild(commentWrapper);
      }
    });
}

function getLikers(itemId) {
  return fetch("./api/likes.php?do=Get&itemid=" + itemId)
    .then((res) => res.json())
    .then((data) => {
      likesCount.innerHTML = `(${data.length})`;
      likesCount.parentElement.setAttribute("title", data.join("\n"));
      likesCount.setAttribute("title", data.join("\n"));
    });
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
