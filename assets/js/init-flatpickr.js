document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('input[type="date"]').forEach((input) => {
    const newInput = document.createElement("input");
    newInput.type = "text";
    newInput.setAttribute("placeholder", "DD-MM-YYYY");

    [...input.attributes].forEach((attr) => {
      if (attr.name !== "type") {
        newInput.setAttribute(attr.name, attr.value);
      }
    });

    if (input.value) newInput.value = input.value;
    input.replaceWith(newInput);

    const options = {
      dateFormat: DateIconFixerSettings.dateFormat || "d-m-Y",
      disableMobile: true,
    };

    if (DateIconFixerSettings.minDate) {
      options.minDate = DateIconFixerSettings.minDate;
    }

    if (DateIconFixerSettings.maxDate) {
      options.maxDate = DateIconFixerSettings.maxDate;
    }

    if (DateIconFixerSettings.disableWeekend) {
      options.disable = [
        function (date) {
          return date.getDay() === 0 || date.getDay() === 6;
        },
      ];
    }

    if (DateIconFixerSettings.disableDates) {
      const disabledDates = DateIconFixerSettings.disableDates
        .split(",")
        .map((date) => date.trim());
      options.disable = (options.disable || []).concat(disabledDates);
    }

    if (DateIconFixerSettings.disableRanges) {
      const [start, end] = DateIconFixerSettings.disableRanges
        .split("to")
        .map((s) => s.trim());
      options.disable = (options.disable || []).concat([
        { from: start, to: end },
      ]);
    }

    if (DateIconFixerSettings.enableOnly) {
      const enabledDates = DateIconFixerSettings.enableOnly
        .split(",")
        .map((date) => date.trim());
      options.enable = enabledDates;
    }

    if (DateIconFixerSettings.multipleDates) {
      options.mode = "multiple";
    }

    if (DateIconFixerSettings.weeksOnly) {
      options.weekNumbers = true;
      options.defaultDate = new Date();
      options.onChange = function (selectedDates, dateStr, instance) {
        if (selectedDates.length > 0) {
          const date = selectedDates[0];
          const week = flatpickr.formatDate(date, "W");
          instance.input.value = "Week " + week;
        }
      };
    }

    if (DateIconFixerSettings.weekNumbers) {
      options.weekNumbers = true;
    }

    flatpickr(newInput, options);
  });
});
