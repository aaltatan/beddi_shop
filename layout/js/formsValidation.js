export default class Validate {
  constructor(formId, msgsId, submitId) {
    this.form = document.getElementById(formId);
    this.msgsTag = document.getElementById(msgsId);
    this.submitBtn = document.getElementById(submitId);
    this.onOpen = function () {
      this.form.querySelectorAll("li").forEach((li) => li.remove());
    };
  }
  createMsg(msg = "") {
    const li = document.createElement("li");
    li.appendChild(document.createTextNode(msg));
    this.form.appendChild(li);
  }
  validate(inputId, type = "", pass = null) {
    const input = document.getElementById(inputId);
    let re, msg;
    switch (type) {
      case "username":
        re = /^[a-z]{3,}$/;
        msg = "the username must be at least 3 lower case characters";
        !re.test(input.value) && this.createMsg(msg);
        break;
      case "fullname":
        re = /^[A-Za-z]+\s[A-Za-z]+\s.*$/;
        msg = "the fullname must be two capitalized words ";
        !re.test(input.value) && this.createMsg(msg);
        break;
      case "email":
        re = /^[A-Za-z0-9\.\-_]+@[a-z0-9\-]+\.\w{2,4}$/;
        msg = "Email not valid";
        !re.test(input.value) && this.createMsg(msg);
        break;
      case "password":
        re = /\.{8,}/;
        msg = "Password must be at least 8 characters";
        !re.test(input.value) && this.createMsg(msg);
        break;
      case "repassword":
        re = /\.{8,}/;
        msg = "Password must be at least 8 characters";
        !re.test(input.value) &&
          input.value === pass.value &&
          this.createMsg(msg);
        break;
    }
    return re.test(input.value);
  }
  submit(e) {
    let criteria = false;
    e.preventDefault();
    this.onOpen();
    this.form.querySelectorAll("input").forEach((input) => {
      let inputName = input.getAttribute("name");
      let inputId = input.getAttribute("id");
      criteria &&=
        inputName === "repassword"
          ? this.validate(
              inputId,
              inputName,
              form.getElementsByName("password")
            )
          : this.validate(inputId, inputName);
    });
    criteria && e.currentTarget.submit();
  }
}
