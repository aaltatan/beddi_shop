export class Validate {
  constructor(formId, msgsId, submitId) {
    this.form = document.getElementById(formId);
    this.msgsTag = document.getElementById(msgsId);
    this.submitBtn = document.getElementById(submitId);
  }

  onOpen() {
    this.form.querySelectorAll("li").forEach((li) => {
      li.remove();
    });
  }

  createMsg(msg = "") {
    const li = document.createElement("li");
    li.appendChild(document.createTextNode(msg));
    this.msgsTag.appendChild(li);
  }

  validateInput(inputName) {
    const input = document.getElementsByName(inputName)[0];
    let re, msg;
    switch (inputName) {
      case "username":
        re = /^[a-z]+[a-z0-9\.]{4,20}$/;
        msg =
          inputName +
          " must be start with small letter, it must be at least 4 characters and less than or equal 20 characters, it can include digits and period symbol only";
        !re.test(input.value) && this.createMsg(msg);
        input.focus();
        break;
      case "name":
      case "country":
      case "description":
      case "fullname":
      case "status":
        re = /^[A-Za-z][A-Za-z0-9\s]{3,50}$/;
        msg = inputName + " must be between 4 and 50 characters";
        !re.test(input.value) && this.createMsg(msg);
        input.focus();
        break;
      case "order":
      case "price":
      case "rating":
        re = /^\d+$/;
        msg = inputName + " must be positive number and grater than zero";
        !re.test(input.value) && this.createMsg(msg);
        input.focus();
        break;
      case "email":
        re = /^[A-Za-z0-9\.\-_]+@[a-z0-9\-]+\.\w{2,4}$/;
        msg = inputName + " not valid";
        !re.test(input.value) && this.createMsg(msg);
        input.focus();
        break;
      case "password":
        re = /^.{8,}$/;
        msg = inputName + " must be at least 8 characters";
        !re.test(input.value) && this.createMsg(msg);
        input.focus();
        break;
    }
    return re.test(input.value);
  }

  submitForm() {
    this.form.addEventListener("submit", (e) => {
      e.preventDefault();
      this.msgsTag.classList.add("opened");
      this.onOpen();
      let criteria = true;
      this.form
        .querySelectorAll(
          "input:not([type='hidden'],[type='checkbox'],[type='file'])"
        )
        .forEach((input) => {
          let inputName = input.name;
          criteria &&= this.validateInput(inputName);
        });
      criteria && e.currentTarget.submit();
    });
  }
}
