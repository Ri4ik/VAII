// reservationTable.js: Работа с таблицей резерваций
import { fetchData } from './ajax.js';

document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.getElementById('reservation-table-body');

    async function loadReservations() {
        try {
            const reservations = await fetchData('http://localhost:8000/api/reservations.php');
            tableBody.innerHTML = ''; // Очищаем таблицу
            console.log(reservations); // Посмотреть, что именно возвращает API
            if (reservations.success && Array.isArray(reservations.reservations)) {
                reservations.reservations.forEach(reservation => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
            <td>${reservation.id}</td>
            <td>${reservation.name}</td>
            <td>${reservation.email}</td>
            <td>${reservation.service}</td>
            <td>${reservation.date}</td>
            <td>${reservation.time}</td>
            <td>
                <button class="edit-btn" data-id="${reservation.id}">Upraviť</button>
                <button class="delete-btn" data-id="${reservation.id}">Odstrániť</button>
            </td>
        `;
                    tableBody.appendChild(row);
                });
            } else {
                console.error('Unexpected response:', reservations);
            }

            attachEventListeners();
        } catch (error) {
            console.error('Error loading reservations:', error);
        }
    }

    async function deleteReservation(id) {
        if (!confirm('Naozaj chcete odstrániť túto rezerváciu?')) return;

        try {
            const response = await fetchData(`http://localhost:8000/api/reservations.php?id=${id}`, 'DELETE');
            if (response.success) {
                alert('Rezervácia bola odstránená.');
                loadReservations();
            } else {
                alert('Chyba pri odstraňovaní rezervácie.');
            }
        } catch (error) {
            console.error('Error deleting reservation:', error);
        }
    }

    function attachEventListeners() {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                deleteReservation(id);
            });
        });

        // Логика для редактирования будет добавлена позже
    }

    // Загружаем резервации при загрузке страницы
    loadReservations();
});