// reservation.js: Логика обработки формы резервации
import { fetchData } from './ajax.js';

document.addEventListener("DOMContentLoaded", function () {
    const reservationForm = document.getElementById("reservation-form");

    reservationForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        // Получаем данные из формы
        const formData = {
            name: document.getElementById("name").value,
            email: document.getElementById("email").value,
            service: document.getElementById("service").value,
            date: document.getElementById("date").value,
            time: document.getElementById("time").value,
        };

        // Проверяем заполненность полей
        if (Object.values(formData).some(field => !field)) {
            alert("Všetky polia sú povinné!");
            return;
        }

        try {
            const response = await fetchData('http://localhost:8000/api/reservations.php', 'POST', formData);
            if (response.success) {
                alert("Rezervácia bola úspešne pridaná!");
                // Очистка формы
                reservationForm.reset();
            } else {
                alert("Chyba: " + response.message);
            }
        } catch (error) {
            alert("Nastala chyba pri odosielaní formulára.");
        }
    });
});
