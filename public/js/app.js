
window.addEventListener('load', function () {
    'use strict';

    // Get the forms we want to add validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function (event) {
            // Ignore default behavior
            event.preventDefault();
            event.stopPropagation();
            if (form.checkValidity()) {
                form.classList.add('was-validated');
                if (form.attributes.id.value === 'login') {
                    const username = form[0].value;
                    const password = form[1].value;
                    const remember_me = form[2].checked;

                    const data = login(username, password);

                    form[3].children[0].removeAttribute('hidden'); // spinner visible
                    data.then(json => {
                        // First check status message
                        if(json.message === 'failure')
                        {
                            // Create error notice on the fly
                            let error_node = document.createElement('div')
                            error_node.setAttribute('class', 'mt-3 alert alert-danger');
                            error_node.setAttribute('role', 'alert');
                            error_node.textContent = json.body.error_msg;

                            form.appendChild(error_node);
                            form.classList.remove('was-validated');

                            console.log(form);
                            console.log(json.body.error_msg);
                        }
                        form[3].children[0].setAttribute('hidden', ''); // spinner hidden again
                    });
                    data.catch(error => console.log(error));
                }
            }
        });
    });
});

async function login(username, password) {
    'use strict';

    let user = {
        username: username,
        password: password
    };

    let response = await fetch('/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=utf-8'
        },
        body: JSON.stringify(user)
    });

    return await response.json();
}