document.addEventListener('DOMContentLoaded', function () {

    /* ========== BOTÓN VOLVER ARRIBA ========== */
    var btn = document.createElement('button');
    btn.className = 'scroll-top-btn';
    btn.innerHTML = '&#9650;';
    btn.title = 'Volver arriba';
    btn.setAttribute('aria-label', 'Volver arriba');
    document.body.appendChild(btn);

    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    function toggleScrollBtn() {
        if (window.scrollY > 400) {
            btn.classList.add('visible');
        } else {
            btn.classList.remove('visible');
        }
    }

    window.addEventListener('scroll', toggleScrollBtn);
    toggleScrollBtn();

    /* ========== VALIDACIÓN DEL FORMULARIO DE REGISTRO ========== */
    var formRegistro = document.querySelector('#form-registro');
    if (formRegistro) {
        formRegistro.addEventListener('submit', function (e) {
            var nombre = document.querySelector('#nombre');
            var correo = document.querySelector('#correo');
            var password = document.querySelector('#password');
            var terminos = document.querySelector('#terminos');
            var errores = [];

            if (!nombre || nombre.value.trim() === '') {
                errores.push('El nombre completo es obligatorio.');
            }
            if (!correo || correo.value.trim() === '') {
                errores.push('El correo electrónico es obligatorio.');
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo.value.trim())) {
                errores.push('Ingresa un correo electrónico válido.');
            }
            if (!password || password.value.length < 8) {
                errores.push('La contraseña debe tener al menos 8 caracteres.');
            }
            if (!terminos || !terminos.checked) {
                errores.push('Debes aceptar los Términos de Servicio.');
            }

            if (errores.length > 0) {
                e.preventDefault();
                var alerta = document.querySelector('#mensaje-validacion');
                if (!alerta) {
                    alerta = document.createElement('div');
                    alerta.id = 'mensaje-validacion';
                    alerta.className = 'mensaje mensaje-error';
                    formRegistro.parentNode.insertBefore(alerta, formRegistro);
                }
                alerta.innerHTML = errores.map(function (err) { return '<p>' + err + '</p>'; }).join('');
                alerta.style.display = 'block';
            }
        });
    }

    /* ========== VALIDACIÓN DEL FORMULARIO DE LOGIN ========== */
    var formLogin = document.querySelector('#form-login');
    if (formLogin) {
        formLogin.addEventListener('submit', function (e) {
            var correo = document.querySelector('#correo');
            var password = document.querySelector('#password');
            var errores = [];

            if (!correo || correo.value.trim() === '') {
                errores.push('El correo electrónico es obligatorio.');
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo.value.trim())) {
                errores.push('Ingresa un correo electrónico válido.');
            }
            if (!password || password.value.trim() === '') {
                errores.push('La contraseña es obligatoria.');
            }

            if (errores.length > 0) {
                e.preventDefault();
                var alerta = document.querySelector('#mensaje-validacion');
                if (!alerta) {
                    alerta = document.createElement('div');
                    alerta.id = 'mensaje-validacion';
                    alerta.className = 'mensaje mensaje-error';
                    formLogin.parentNode.insertBefore(alerta, formLogin);
                }
                alerta.innerHTML = errores.map(function (err) { return '<p>' + err + '</p>'; }).join('');
                alerta.style.display = 'block';
            }
        });
    }

});
