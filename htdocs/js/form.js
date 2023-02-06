(function () {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')

    Array.from(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                } else {
                    event.preventDefault()
                    formPost(form)
                }
            }, false)
        })
})()

async function formPost(form) {
    const formData = new FormData(form)
    const response = await fetch(form.action, {
        method: 'POST',
        body: formData
    });

    const data = await response.json()
    const successAlert = document.querySelector(".alert-success")
    const errorAlert = document.querySelector(".alert-danger")
    if (data.success) {
        toggleValid(form, [])
        successAlert.classList.remove("d-none")
        errorAlert.classList.add("d-none")
        form.classList.add('was-validated')
    } else if (data.errors) {
        toggleValid(form, data.errors)
    } else {
        errorAlert.classList.remove("d-none")
        successAlert.classList.add("d-none")
    }
}

function toggleValid(form, errors) {
    Array.from(form.getElementsByClassName('form-control'))
        .forEach(function (field) {
            const fieldName = field.getAttribute('name')
            if (errors.includes(fieldName)) {
                field.classList.add('is-invalid')
                field.classList.remove('is-valid')
            } else {
                field.classList.add('is-valid')
                field.classList.remove('is-invalid')
            }
        })
}
