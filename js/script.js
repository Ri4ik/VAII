document.addEventListener("DOMContentLoaded", function () {
    const reservationTable = document.getElementById("reservation-table-body");

    // Загрузка списка услуг
    fetch('http://localhost:8000/api/services.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const serviceSelect = document.getElementById('service');
            serviceSelect.innerHTML = '<option value="">Vyberte službu</option>';
            data.forEach(service => {
                const option = document.createElement('option');
                option.value = service.id;
                option.textContent = `${service.name} - ${service.price}€ - cca ${service.duration} minut`;
                serviceSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading services:', error);
            alert('Nastala chyba pri načítavaní služieb.');
        });

    // Отправка формы резервации (CREATE)
    document.getElementById("reservation-form").addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = {
            name: document.getElementById("name").value,
            email: document.getElementById("email").value,
            service: document.getElementById("service").value,
            date: document.getElementById("date").value,
            time: document.getElementById("time").value
        };

        if (Object.values(formData).some(field => !field)) {
            alert("Všetky polia sú povinné!");
            return;
        }

        fetch('../api/reservations.php', {
            method: 'POST',
            body: JSON.stringify(formData),
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Rezervácia bola úspešne pridaná!");
                    loadReservations(); // Перезагружаем таблицу
                } else {
                    alert("Chyba: " + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error.message);
                alert("Nastala chyba pri odosielaní formulára.");
            });
    });

    // Загрузка всех резерваций (READ)
    function loadReservations() {
        fetch('../api/reservations.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.reservations) {
                    reservationTable.innerHTML = '';
                    data.reservations.forEach(reservation => {
                        const row = document.createElement("tr");

                        row.innerHTML = `
                            <td>${reservation.id}</td>
                            <td>${reservation.user_name}</td>
                            <td>${reservation.email}</td>
                            <td>${reservation.service_name}</td>
                            <td>${reservation.reservation_date}</td>
                            <td>${reservation.reservation_time}</td>
                            <td>
                                <button class="delete-btn" data-id="${reservation.id}">Odstrániť</button>
                                <button class="edit-btn" data-id="${reservation.id}">Upraviť</button>
                            </td>
                        `;
                        reservationTable.appendChild(row);
                    });

                    // Удаление резервации (DELETE)
                    document.querySelectorAll(".delete-btn").forEach(button => {
                        button.addEventListener("click", function () {
                            const id = this.dataset.id;

                            fetch('../api/reservations.php', {
                                method: 'DELETE',
                                body: JSON.stringify({ id }),
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        alert("Rezervácia bola odstránená!");
                                        loadReservations(); // Перезагружаем таблицу
                                    } else {
                                        alert("Chyba: " + data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert("Nastala chyba pri odstraňovaní rezervácie.");
                                });
                        });
                    });

                    // Редактирование резервации (UPDATE)
                    document.querySelectorAll(".edit-btn").forEach(button => {
                        button.addEventListener("click", function () {
                            const id = this.dataset.id;
                            const service = prompt("Zadajte nové ID služby:");
                            const date = prompt("Zadajte nový dátum (YYYY-MM-DD):");
                            const time = prompt("Zadajte nový čas (HH:MM):");

                            if (service && date && time) {
                                fetch('../api/reservations.php', {
                                    method: 'PUT',
                                    body: JSON.stringify({ id, service, date, time }),
                                    headers: {
                                        'Content-Type': 'application/json'
                                    }
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            alert("Rezervácia bola upravená!");
                                            loadReservations(); // Перезагружаем таблицу
                                        } else {
                                            alert("Chyba: " + data.message);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert("Nastala chyba pri úprave rezervácie.");
                                    });
                            }
                        });
                    });
                }
            })
            .catch(error => {
                console.error('Error loading reservations:', error);
                alert("Nastala chyba pri načítavaní rezervácií.");
            });
    }

    // Загрузка резерваций при загрузке страницы
    loadReservations();
});
