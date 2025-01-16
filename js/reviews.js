import { fetchData } from './ajax.js';

document.addEventListener("DOMContentLoaded", function () {
    const reviewList = document.getElementById('review-list');
    const reviewForm = document.getElementById('review-form');

    // Загрузка отзывов
    async function loadReviews() {
        try {
            const reviews = await fetchData('http://localhost:8000/api/reviews.php');
            reviewList.innerHTML = '';

            reviews.forEach(review => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <strong>${review.name}</strong> (${new Date(review.created_at).toLocaleString()}):
                    <p>${review.review_text}</p>
                    <button class="delete-btn" data-id="${review.id}">Odstrániť</button>
                `;
                reviewList.appendChild(li);
            });

            attachEventListeners();
        } catch (error) {
            console.error('Error loading reviews:', error);
        }
    }

    // Добавление нового отзыва
    reviewForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const userId = document.getElementById('user-id').value; // ID пользователя
        const reviewText = document.getElementById('review-text').value;

        if (!userId || !reviewText) {
            alert('Všetky polia sú povinné!');
            return;
        }

        try {
            const response = await fetchData('http://localhost:8000/api/reviews.php', 'POST', {
                user_id: userId,
                review_text: reviewText
            });

            if (response.success) {
                alert('Recenzia bola pridaná!');
                reviewForm.reset();
                loadReviews();
            } else {
                alert('Chyba: ' + response.message);
            }
        } catch (error) {
            alert('Nastala chyba pri pridávaní recenzie.');
        }
    });

    // Удаление отзыва
    async function deleteReview(id) {
        if (!confirm('Naozaj chcete odstrániť túto recenziu?')) return;

        try {
            const response = await fetchData(`http://localhost:8000/api/reviews.php?id=${id}`, 'DELETE');
            if (response.success) {
                alert('Recenzia bola odstránená.');
                loadReviews();
            } else {
                alert('Chyba pri odstraňovaní recenzie.');
            }
        } catch (error) {
            console.error('Error deleting review:', error);
        }
    }

    function attachEventListeners() {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                deleteReview(id);
            });
        });
    }

    // Загрузка отзывов при загрузке страницы
    loadReviews();
});
