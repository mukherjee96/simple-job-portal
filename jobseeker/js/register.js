const pass = document.getElementById("password");
const cpass = document.getElementById("cpassword");
const form = document.getElementById("form");
const alert = document.getElementById("alert");

cpass.addEventListener("keyup", function(e) {
  if (pass.value !== cpass.value) {
    cpass.classList.add("is-invalid");
    pass.classList.add("is-invalid");
  } else {
    cpass.classList.remove("is-invalid");
    pass.classList.remove("is-invalid");
  }
});
form.addEventListener("submit", function(e) {
  e.preventDefault();
  if (pass.value !== cpass.value) {
    error.style.display = "block";
    return false;
  } else {
    form.submit();
  }
});
