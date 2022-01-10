function renderCollapsedAreas(justifiableOptions) {
  const justifications = document.querySelectorAll(".justificationArea");

  justifications.forEach((justification) => {
    const itemId = filterId(justification.id);

    const selectedOption = parseInt(
      document.querySelector(`#select-${itemId}`).value
    );

    if (justifiableOptions.includes(selectedOption)) {
      $(`#collapseJustify-${itemId}`).collapse("show");
      $(`#inputJustify-${itemId}`).prop("disabled", false);
    }
  });
}
