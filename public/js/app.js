

window.addEventListener('load', function () {
    'use strict';

    var access_token;

    // Get the forms we want to add validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function (event) {
            // Ignore default behavior
            event.preventDefault();
            event.stopPropagation();
            // Form validation
            if (form.checkValidity()) {
                form.classList.add('was-validated');
                if (form.attributes.id.value === 'login') {
                    const username = form[0].value;
                    const password = form[1].value;
                    const remember_me = form[2].checked;

                    const data = call_login_api(username, password);

                    form[3].children[0].removeAttribute('hidden'); // spinner visible
                    data.then(json => {
                        form[3].children[0].setAttribute('hidden', ''); // spinner hidden again

                        // Remove old alerts
                        var alerts = document.getElementsByClassName('alert');
                        if (alerts.length > 0) {
                            for (let alert of alerts) {
                                alert.remove();
                            }
                        }
                        // First check status message
                        if(json.message === 'failure')
                        {
                            // Create error notice on the fly
                            let error_node = document.createElement('div');
                            error_node.setAttribute('id', 'err_msg');
                            error_node.setAttribute('class', 'mt-3 alert alert-danger');
                            error_node.setAttribute('role', 'alert');
                            error_node.textContent = json.body.error_msg;

                            form.appendChild(error_node);
                            form.classList.remove('was-validated');

                            return;
                        }
                        // Store jwt token in memory
                        access_token = json.body.access_token;

                        // login was successful, go to dashboard
                        let success_node = document.createElement('div');
                        success_node.setAttribute('id', 'suc_msg');
                        success_node.setAttribute('class', 'mt-3 alert alert-success');
                        success_node.setAttribute('role', 'alert');
                        success_node.textContent = "Login was successful!";
                        form.appendChild(success_node);

                        
                    });
                    data.catch(error => console.log(error));
                }
            }
        });
    });

    console.log(access_token);
});

async function call_login_api(username, password) {
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