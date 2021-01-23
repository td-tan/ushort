

window.addEventListener('load', function () {
    'use strict';

    var access_token;
    var refresh_token;

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
                        // Store jwt token & refresh_token in memory
                        access_token = json.body.access_token;
                        refresh_token = json.body.refresh_token; // As cookie?


                        // login was successful, go to dashboard
                        let success_node = document.createElement('div');
                        success_node.setAttribute('id', 'suc_msg');
                        success_node.setAttribute('class', 'mt-3 alert alert-success');
                        success_node.setAttribute('role', 'alert');
                        success_node.textContent = "Login was successful!";
                        form.appendChild(success_node);


                        call_refresh_api(access_token);

                        // Create dashboard for user
                        const user_info = call_user_api(access_token);
                        const dashboard = document.getElementById('dashboard');
                        const tbody = document.getElementById('ltable');
                        const spinner = document.getElementById('data-loading');
                        spinner.removeAttribute('hidden');

                        user_info.then( json => {
                            json.body.links.forEach(element => {
                                const tr = document.createElement('tr');
                                const td0 = document.createElement('td');
                                const td1 = document.createElement('td');
                                const td2 = document.createElement('td');
                                td0.textContent = element.link;
                                td1.textContent = element.redirect;
                                td2.textContent = element.created;

                                tr.appendChild(td0);
                                tr.appendChild(td1);
                                tr.appendChild(td2);
                                tbody.appendChild(tr);
                            });
                            spinner.setAttribute('hidden', '');
                        });
                        dashboard.removeAttribute('hidden');
                        // Simple filter function w3school
                        $("#search").on("keyup", function() {
                            var value = $(this).val().toLowerCase();
                            $("#ltable tr").filter(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                            });
                        });

                        document.getElementById('create').addEventListener('click', function create() {
                            // TODO Create new short link
                            const tr = document.createElement('tr');
                            const td0 = document.createElement('td');
                            const td1 = document.createElement('td');
                            const td2 = document.createElement('td');

                            td0.setAttribute('contenteditable', '');
                            td1.setAttribute('contenteditable', '');

                            tr.appendChild(td0);
                            tr.appendChild(td1);
                            tr.appendChild(td2);
    
                            tbody.appendChild(tr);
                        });
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

async function call_refresh_api(access_token) {
    'use strict';


    let response = await fetch('/api/refresh', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${access_token}`,
            'Content-Type': 'application/json;charset=utf-8'
        }
    });

    return await response.json();
}

async function call_user_api(access_token) {
    'use strict';


    let response = await fetch('/api/user', {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${access_token}`,
            'Content-Type': 'application/json;charset=utf-8'
        }
    });

    return await response.json();
}