// name///
function validateName(input) {
    const nameError = document.getElementById('nameError');
    
    const Pattern = /^[A-Za-z\s]*$/;
    
    if (!Pattern.test(input.value)) {
        nameError.style.display = 'block';
       
        input.value = input.value.replace(/[^A-Za-z\s]/g, '');
    } else {
        nameError.style.display = 'none';
    }
}

//others category in partner _dashbord form //
      function toggleDescriptions() {
        const category = document.getElementById("category").value;
        const descriptionBox = document.getElementById("other-description");
        descriptionBox.style.display = category === "others" ? "block" : "none";
      }

      // Run on page load in case "others" is pre-selected
      window.onload = toggleDescription;

//others category in partner _dashbord form //

//others category in admin _dashbord form //
function toggleDescription() {
  const category = document.getElementById("category").value;
  const descriptionBox = document.getElementById("other-description");
  descriptionBox.style.display = category === "others" ? "block" : "none";
}

// Run on page load in case "others" is pre-selected
window.onload = toggleDescription;

// image valitation//
function validateFiles(input) {
    const maxSize = 2 * 1024 * 1024; // 2MB
    const fileError = document.getElementById('file-error');
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg', 'application/pdf'];
    const invalidFiles = [];

    input.classList.remove('is-invalid');
    fileError.style.display = 'none';
    fileError.textContent = '';

    for (let i = 0; i < input.files.length; i++) {
        const file = input.files[i];

        if (!allowedTypes.includes(file.type)) {
            invalidFiles.push(`${file.name} - Invalid file type`);
            continue;
        }

        if (file.size > maxSize) {
            invalidFiles.push(`${file.name} - File too large (max 2MB)`);
        }
    }

    if (invalidFiles.length > 0) {
        input.classList.add('is-invalid');
        fileError.textContent = 'Invalid files:\n' + invalidFiles.join('\n');
        fileError.style.display = 'block';
        input.value = ''; // Reset input
    }
}

