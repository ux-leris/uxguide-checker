const tabs = document.querySelectorAll(".tab");
const steps = document.querySelectorAll(".step");

let currentTab = 0;

showTab(currentTab);

function showTab(currentTab) {
  tabs[currentTab].style.display = "block";

  setStepIndicator(currentTab);
}

function next() {
  if (!validateForm()) return false;

  tabs[currentTab].style.display = "none";

  currentTab += 1;

  showTab(currentTab);
}

function previous() {
  tabs[currentTab].style.display = "none";

  currentTab -= 1;

  showTab(currentTab);
}

function validateForm() {
  let valid = true;

  const inputs = tabs[currentTab].querySelectorAll("input");

  for (let i = 0; i < inputs.length; i++) {
    if (inputs[i].value == "") {
      if (!inputs[i].classList.contains("is-invalid")) {
        inputs[i].className += " is-invalid";
      }

      valid = false;
    } else {
      if (inputs[i].classList.contains("is-invalid")) {
        inputs[i].className = inputs[i].className.replace(
          " is-invalid",
          " is-valid"
        );
      } else {
        if (!inputs[i].classList.contains("is-valid")) {
          inputs[i].className += " is-valid";
        }
      }
    }
  }

  return valid;
}

function submitForm() {
  const form = document.querySelector("#createChecklist");

  if (validateForm()) form.submit();
}

function setStepIndicator(currentTab) {
  for (let i = 0; i < steps.length; i++) {
    steps[i].className = steps[i].className.replace("step active", "step");
  }

  steps[currentTab].className = "step active";
}
