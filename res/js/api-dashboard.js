const api_key_button = document.getElementById("new-api-key-button")
const api_key = document.getElementById("api-key")

function new_api_key() {
    fetch('/api-publique/new_api_key.php')
    .then(response => response.json())
    .then(data => {
        console.log('JSON reçu:', data);
        api_key.innerText = data['api_key']
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

api_key_button.addEventListener('click', new_api_key)