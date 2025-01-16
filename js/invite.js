document.addEventListener("DOMContentLoaded", () => {
    // Получение токена из URL
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get("token");

    if (!token) {
        alert("Pozvánka je neplatná alebo chýba token.");
        return;
    }

    // Устанавливаем токен в скрытое поле формы
    const tokenInput = document.getElementById("token");
    tokenInput.value = token;

    // Обработчик отправки формы
    document.getElementById("invite-form").addEventListener("submit", function (e) {
        e.preventDefault(); // Предотвращаем стандартное поведение формы

        // Считываем данные из формы
        const formData = {
            token: document.getElementById("token").value,
            name: document.getElementById("name").value,
            email: document.getElementById("email").value,
            password: document.getElementById("password").value,
        };

        // Отправляем данные на сервер
        fetch('../api/register.php', {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(formData),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert("Registrácia bola úspešná!");
                    window.location.href = "index.html";
                } else {
                    alert("Chyba: " + data.message);
                }
            })
            .catch((error) => {
                console.error("Chyba:", error);
                alert("Nastala chyba pri registrácii.");
            });
    });
});
