// utils.js: Утилитарные функции

// Проверка валидности email
export function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Пример использования: if (!isValidEmail(email)) { alert('Invalid email!'); }
