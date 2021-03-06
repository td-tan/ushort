

window.addEventListener('load', function () {
    'use strict';

    var access_token;
    var refresh_token;
    var first_time = true;

    // Silent refresh on first load
    setInterval(call_refresh_api().then(json => {
        if(json.message === 'success') {
            access_token = json.body.access_token;
            if(first_time) {
                show_dashboard(access_token);
                first_time = false;
            }
        }
    }), 1000*30*60); // Every 30 minutes check on refresh

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

                        show_dashboard(access_token);

                    });
                    data.catch(error => console.log(error));
                }
            }
        });
    });


    console.log(access_token);
});

let targetContext; // For context button targeting row

function show_dashboard(access_token) {


    const dashboard = document.getElementById('dashboard');
    const tbody = document.getElementById('ltable');

    // Hide login panel
    document.getElementById('login').setAttribute('hidden', '');

    const logoutBtn = document.getElementById('logoutBtn');
    logoutBtn.addEventListener('click', function logout() {
        // Show spinner loading
        logoutBtn.children[0].removeAttribute('hidden');
        call_logout_api(access_token).then( json => {
            if(json.message === 'success') {
                // Show login panel
                document.getElementById('login').removeAttribute('hidden');
                // Delete old data
                while(tbody.firstChild) {
                    tbody.removeChild(tbody.firstChild);
                }
                // Hide dashboard
                dashboard.setAttribute('hidden', '');
                // Hide spinner loading again
                logoutBtn.children[0].setAttribute('hidden', '');
            }
        }).catch(error => console.log(error));
    })

    // Create dashboard for user
    display_user_slinks(access_token, tbody);
    

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
        
        td0.setAttribute('class', 'link');
        td1.setAttribute('class', 'short');

        tr.appendChild(td0);
        tr.appendChild(td1);
        tr.appendChild(td2);

        tr.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            targetContext = e.target;

            // TODO Refactor this
            var top = e.pageY - 10;
            var left = e.pageX - 90;

            $("#context-menu").css({
                display: "block",
                top: top,
                left: left
            }).addClass("show");

        });
        $("body").on("click", function() {
            $("#context-menu").removeClass("show").hide();
        });


        tbody.appendChild(tr);
    });

    $("#context-menu button").on("click", function(e) {
        if(e.target.textContent === 'Save') {
            let link = '', short = '', created = '', oldShort = '';

            if(!targetContext.hasAttribute('contenteditable')) {
                return;
            }
            // TODO Refactor link short class btn
            // TODO Refactor THIS!
            // Get data of clicked row
            if(targetContext.className === 'link') {
                link = targetContext.firstChild.data;
                short = targetContext.nextSibling.firstChild.data;
                if(targetContext.nextSibling.lastChild) {
                    oldShort = targetContext.nextSibling.lastChild.textContent;
                }
                if(targetContext.nextSibling.nextSibling.firstChild) {
                    created = targetContext.nextSibling.nextSibling.firstChild.data;
                }

                targetContext.removeAttribute('contenteditable');
                targetContext.nextSibling.removeAttribute('contenteditable');
            }
            else if (targetContext.className === 'short') {
                link = targetContext.previousSibling.firstChild.data;
                short = targetContext.firstChild.data;
                if(targetContext.nextSibling.firstChild) {
                    oldShort = targetContext.lastChild.textContent;
                }
                if(targetContext.nextSibling.firstChild) {
                    created = targetContext.nextSibling.firstChild.data;
                }
                targetContext.removeAttribute('contenteditable');
                targetContext.previousSibling.removeAttribute('contenteditable');
            }
            else {
                link = targetContext.previousSibling.previousSibling.firstChild.data;
                short = targetContext.previousSibling.firstChild.data;
                if (targetContext.previousSibling.lastChild) {
                    oldShort = targetContext.previousSibling.lastChild.textContent;
                }
                if(targetContext.firstChild) {
                    created = targetContext.firstChild.data;
                }
                targetContext.previousSibling.previousSibling.removeAttribute('contenteditable');
                targetContext.previousSibling.removeAttribute('contenteditable');
            }

            // TODO Check Created
            if(created.length  > 0) {
                // TODO Modify short

                data = {
                    old_short: oldShort,
                    short: short
                }

                call_modify_short_api(access_token, data)
                    .then(json => {
                        // TODO load new data
                        display_user_slinks(access_token, tbody);
                    })
                    .catch(error => console.log(error));
            }
            else {
                data = {
                    link: link,
                    short: short
                }

                call_create_link_api(access_token, data)
                    .then(json => {
                        // TODO load new data
                        display_user_slinks(access_token, tbody);
                    })
                    .catch(error => console.log(error));
            }
        }
        else if(e.target.textContent === 'Delete') {
            let short = '';
            if(targetContext.className === 'link') {
                short = targetContext.nextSibling.firstChild.data;
            }
            else if (targetContext.className === 'short') {
                short = targetContext.firstChild.data;
            }
            else {
                short = targetContext.previousSibling.firstChild.data;
            }

            call_delete_short_api(access_token, short)
            .then(json => {
                if (json.message === 'success') {
                    display_user_slinks(access_token, tbody);
                }
            })
            .catch(error => console.log(error));
        }
        else if(e.target.textContent === 'Edit') {
            // Get data of clicked row
            if(targetContext.className === 'link') {
                targetContext.setAttribute('contenteditable', '');
                targetContext.nextSibling.setAttribute('contenteditable', '');

                targetContext.setAttribute('class', 'link');
                targetContext.nextSibling.setAttribute('class', 'short');

                if (targetContext.nextSibling.lastChild === 'old') {
                    targetContext.nextSibling.lastChild.remove();
                }
                let oldShort = document.createElement('td');
                oldShort.textContent = targetContext.nextSibling.firstChild.data;
                oldShort.setAttribute('class', 'old');
                oldShort.setAttribute('hidden', '');
                targetContext.nextSibling.appendChild(oldShort);
            }
            else if (targetContext.className === 'short') {
                targetContext.setAttribute('contenteditable', '');
                targetContext.previousSibling.setAttribute('contenteditable', '');

                targetContext.setAttribute('class', 'short');
                targetContext.previousSibling.setAttribute('class', 'link');

                if (targetContext.lastChild.className === 'old') {
                    targetContext.lastChild.remove();
                }

                let oldShort = document.createElement('td');
                oldShort.textContent = targetContext.firstChild.data;
                oldShort.setAttribute('class', 'old');
                oldShort.setAttribute('hidden', '');
                targetContext.appendChild(oldShort);
            }
            else {
                targetContext.previousSibling.previousSibling.setAttribute('contenteditable', '');
                targetContext.previousSibling.setAttribute('contenteditable', '');

                targetContext.previousSibling.previousSibling.setAttribute('class', 'link');
                targetContext.previousSibling.setAttribute('class', 'short');

                if (targetContext.previousSibling.lastChild === 'old') {
                    targetContext.previousSibling.lastChild.remove();
                }

                let oldShort = document.createElement('td');
                oldShort.textContent = targetContext.previousSibling.firstChild.data;
                oldShort.setAttribute('class', 'old');
                oldShort.setAttribute('hidden', '');
                targetContext.previousSibling.appendChild(oldShort);
            }
        }
        $(this).parent().removeClass("show").hide();
    });
}

function display_user_slinks(access_token, tbody) {

    while(tbody.firstChild) {
        tbody.firstChild.remove();
    }

    const user_info = call_user_api(access_token);

    const spinner = document.getElementById('data-loading');
    spinner.removeAttribute('hidden');

    user_info.then( json => {
        // Show user email on welcome message
        document.getElementById('user').textContent = json.body.user;

        json.body.links.forEach(element => {
            const tr = document.createElement('tr');
            const td0 = document.createElement('td');
            const td1 = document.createElement('td');
            const td2 = document.createElement('td');


            td0.setAttribute('class', 'link');
            td1.setAttribute('class', 'short');

            td0.textContent = element.link;
            td1.textContent = element.redirect;
            td2.textContent = element.created;

            tr.appendChild(td0);
            tr.appendChild(td1);
            tr.appendChild(td2);

            // Table row contextmenu
            tr.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                targetContext = e.target;

                var top = e.pageY - 10;
                var left = e.pageX - 90;
                $("#context-menu").css({
                    display: "block",
                    top: top,
                    left: left
                }).addClass("show");
            });

            $("body").on("click", function() {
                $("#context-menu").removeClass("show").hide();
            });
            /*
            $("#context-menu button").on("click", function() {
                $(this).parent().removeClass("show").hide();
            });*/

            tbody.appendChild(tr);
        });
        spinner.setAttribute('hidden', '');
    }).catch(error => console.log(error));
}

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

async function call_refresh_api() {
    'use strict';


    let response = await fetch('/api/refresh', {
        method: 'POST',
        headers: {
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

async function call_logout_api(access_token) {
    'use strict';


    let response = await fetch('/api/logout', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${access_token}`,
            'Content-Type': 'application/json;charset=utf-8'
        }
    });

    return await response.json();
}

async function call_create_link_api(access_token, data) {
    'use strict';

    let response = await fetch('/api/create-link', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${access_token}`,
            'Content-Type': 'application/json;charset=utf-8'
        },
        body: JSON.stringify(data)
    });

    return await response.json();
}

async function call_modify_short_api(access_token, data) {
    'use strict';

    let response = await fetch('/api/modify-short', {
        method: 'PUT',
        headers: {
            'Authorization': `Bearer ${access_token}`,
            'Content-Type': 'application/json;charset=utf-8'
        },
        body: JSON.stringify(data)
    });

    return await response.json();
}

async function call_delete_short_api(access_token, short) {
    'use strict';

    let response = await fetch(`/api/${short}`, {
        method: 'DELETE',
        headers: {
            'Authorization': `Bearer ${access_token}`,
            'Content-Type': 'application/json;charset=utf-8'
        }
    });

    return await response.json();
}