const form = document.querySelector("form");

form.addEventListener("submit", (event) => {
  event.preventDefault();

  const passwordInput = document.querySelector("#password-input");
  const confirmInput = document.querySelector("#confirm-input");

  if (passwordInput.value !== confirmInput.value) {
    confirmInput.classList.add("is-invalid");

    const confirmMessage = document.querySelector("#confirm-message");
    confirmMessage.classList.remove("is-hidden");
  } else {
    form.submit();
  }
});
