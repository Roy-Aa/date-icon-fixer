document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('input[type="date"]').forEach(input => {
    const newInput = document.createElement('input');
    newInput.type = 'text';
    newInput.setAttribute("placeholder", "DD-MM-YYYY");

    [...input.attributes].forEach(attr => {
      if (attr.name !== 'type') {
        newInput.setAttribute(attr.name, attr.value);
      }
    });

    if (input.value) newInput.value = input.value;

    input.replaceWith(newInput);

    const options = {
      dateFormat: DateIconFixerSettings.dateFormat || "d-m-Y",
      disableMobile: true
    };

    if (DateIconFixerSettings.minDate) {
      options.minDate = DateIconFixerSettings.minDate;
    }

    if (DateIconFixerSettings.maxDate) {
      options.maxDate = DateIconFixerSettings.maxDate;
    }

    if (DateIconFixerSettings.disableWeekend) {
      options.disable = [
        function(date) {
          return (date.getDay() === 0 || date.getDay() === 6);
        }
      ];
    }

    flatpickr(newInput, options);
  });
});
