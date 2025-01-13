document.addEventListener('DOMContentLoaded', function () {
    console.log('switch.js');
    // トグルスイッチの動作
    const toggleSwitch = document.getElementById('switch');
    toggleSwitch.addEventListener('change', function () {
        if (toggleSwitch.checked) {
            console.log('checked');
            document.getElementById('scoreChart').style.display = '';
            document.getElementById('mistakesChart').style.display = 'none';
        } else {
            console.log('unchecked');
            document.getElementById('scoreChart').style.display = 'none';
            document.getElementById('mistakesChart').style.display = '';
        }
    });
});