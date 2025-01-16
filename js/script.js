// js/script.js
document.addEventListener("DOMContentLoaded", function () {
    // Загружаем список услуг через AJAX
    fetch('http://localhost:8000/api/services.php') //('../api/services.php') // Путь к API для получения услуг
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const serviceSelect = document.getElementById('service');
            // Очищаем список (на случай повторного вызова)
            serviceSelect.innerHTML = '<option value="">Vyberte službu</option>';

            // Добавляем каждую услугу в выпадающий список
            data.forEach(service => {
                const option = document.createElement('option');
                option.value = service.id;
                option.textContent = `${service.name} - ${service.price}€ -  cca${service.duration} minut`;
                serviceSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading services:', error);
            alert('Nastala chyba pri načítavaní služieb.');
        });

    // Обработчик отправки формы
    document.getElementById("reservation-form").addEventListener("submit", function (e) {
        e.preventDefault(); // Предотвращаем отправку формы по умолчанию

        // Получаем данные из формы
        const name = document.getElementById("name").value;
        const email = document.getElementById("email").value;
        const service = document.getElementById("service").value;
        const date = document.getElementById("date").value;
        const time = document.getElementById("time").value;

        // Проверяем, чтобы все поля были заполнены
        if (!name || !email || !service || !date || !time) {
            alert("Všetky polia sú povinné!");
            return;
        }

        // Создаем объект с данными формы
        const formData = {
            name: name,
            email: email,
            service: service,
            date: date,
            time: time
        };

        // Отправляем данные на сервер с помощью AJAX (fetch)
        fetch('../api/reservations.php', {
            method: 'POST',  // HTTP метод
            body: JSON.stringify(formData),  // Данные формы в формате JSON
            headers: {
                'Content-Type': 'application/json'  // Указываем тип данных
            }
        })
            .then(response => response.json())  // Преобразуем ответ в JSON
            .then(data => {
                if (data.success) {
                    alert("Rezervácia bola úspešne pridaná!"); // Уведомление об успешной отправке
                } else {
                    alert("Chyba: " + data.message);  // Уведомление о ошибке
                }
            })
            .catch(error => {
                console.error('Error:', error.message);
                alert("Nastala chyba pri odosielaní formulára.");
            });
    });
});
