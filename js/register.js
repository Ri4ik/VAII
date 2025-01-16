document.getElementById('register-form').addEventListener('submit', function (e) {
    e.preventDefault(); // Останавливаем стандартное поведение формы

    const formData = {
        username: document.getElementById('username').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value
    };

    fetch('http://localhost:8000/api/register.php', {
        method: 'POST', // Убедитесь, что здесь указан POST
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData) // Передача данных
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Регистрация прошла успешно!');
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => console.error('Ошибка:', error));
});
