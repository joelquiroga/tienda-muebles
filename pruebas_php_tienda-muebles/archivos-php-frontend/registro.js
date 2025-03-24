document.addEventListener('DOMContentLoaded', () => {
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.querySelector('.container');

    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

    signUpButton.addEventListener('click', () => {
        container.classList.add('right-panel-active');
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove('right-panel-active');
    });

    // Manejo del formulario de registro
    document.getElementById('registerForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const user = document.getElementById('registerUser').value;
        const name = document.getElementById('registerName').value;
        const email = document.getElementById('registerEmail').value;
        const password = document.getElementById('registerPassword').value;

        if (user === '' || name === '' || email === '' || password === '') {
            alert('Por favor, complete todos los campos.');
            return;
        }

        // Enviar datos al servidor con fetch
        fetch('/api_registro.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
             credentials: 'same-origin',
    body: `nombre_completo=${encodeURIComponent(name)}&correo=${encodeURIComponent(email)}&usuario=${encodeURIComponent(user)}&contrasena=${encodeURIComponent(password)}&csrf_token=${encodeURIComponent(csrfToken)}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la solicitud: ' + response.statusText);
            }
            return response.text();
        })
        .then(data => {
            alert(data);
            container.classList.remove('right-panel-active');
        })
        .catch(error => console.error('Error en la petición Fetch:', error));
    });

    
});

