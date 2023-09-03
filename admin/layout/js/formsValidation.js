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
    console.log(inputName);
    switch (inputName) {
      case "username":
        re = /^[a-z]+[a-z0-9\.]{4,20}$/;
        msg =
          inputName +
          " must start with small letter, it must be at least 4 characters and less than or equal 20 characters, it can include digits and period symbol only";
        !re.test(input.value) && this.createMsg(msg);
        input.focus();
        break;
      case "title":
      case "description":
        re = /^.{4,50}$/;
        msg = inputName + " must be between 4 and 50 characters";
        !re.test(input.value) && this.createMsg(msg);
        input.focus();
        break;
      case "country": //!
      case "name": //!
        re = /^[A-Z][a-z]{3,19}$/;
        msg =
          inputName +
          " must be one Capitalized Word between 4 and 20 characters";
        !re.test(input.value) && this.createMsg(msg);
        input.focus();
        break;
      case "fullname": //!
        re = /^[A-Z][A-Za-z\s]{2,48}[a-z]$/;
        msg =
          inputName +
          " must start with letter and can it has spaces and ends with letter";
        !re.test(input.value) && this.createMsg(msg);
        input.focus();
        break;
      case "order":
      case "offerprice":
      case "price":
        re = /^\d+$/;
        msg = inputName + " must be positive number";
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
