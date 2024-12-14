/*<script>
    document.getElementById('game-field').addEventListener('click', function(event) {
    var img = event.target;
    var x = event.offsetX; // Получаем X координату клика
    var y = event.offsetY; // Получаем Y координату клика

    // Отправка координат на сервер с помощью AJAX
    fetch('/save-coordinates', {
    method: 'POST',
    headers: {
    'Content-Type': 'application/json',
},
    body: JSON.stringify({x: x, y: y}),
})
    .then(response => response.json())
    .then(data => {
    console.log('Координаты сохранены:', data);
})
    .catch(error => {
    console.error('Ошибка при отправке координат:', error);
});
});
</script>*/